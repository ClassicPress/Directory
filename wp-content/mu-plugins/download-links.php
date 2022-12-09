<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Download Links
 * Plugin URI: https://directory.classicpress.net/
 * Description: Keeps download links up to date
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 1.0.0
 **/
 
/* RENDER MANUAL UPDATE DOWNLOAD LINK FORM */
function kts_render_software_update_link_form( $post ) {

	# Bail if not logged in
	if ( ! is_user_logged_in() ) {
		return;
	}

	# Bail if not the user's own archive page
	if ( get_current_user_id() !== get_queried_object_id() ) {
		return;
	}

	$update_nonce = cp_set_nonce( 'update_nonce' );
			
	if ( isset( $_GET['notification'] ) ) {
		if ( $_GET['notification'] === 'nonce-wrong-' . absint( $post->ID ) ) {
			echo '<div class="error-message" role="alert"><p>' . __( 'You have already submitted this', 'classicpress' ) . '</p></div>';
		}
		elseif ( $_GET['notification'] === 'github-api-wrong-' . absint( $post->ID ) ) {
			echo '<div class="error-message" role="alert"><p>' . __( 'Something went wrong with the GitHub API', 'classicpress' ) . '</p></div>';
		}
		elseif ( $_GET['notification'] === 'success-' . absint( $post->ID ) ) {
			echo '<div class="success-message" role="polite"><p class="wp-caption-text">' . __( 'The link has been updated', 'classicpress' ) . '</p></div>';
		}
	}

	echo '<form id="update-form" class="update-form" method="POST" autocomplete="off">';
	echo '<input type="hidden" name="software-id" value="' . absint( $post->ID ) . '">';
	echo '<input type="hidden" name="update-nonce-name" value="' . $update_nonce['name'] . '">';
	echo '<input type="hidden" name="update-nonce-value" value="' . $update_nonce['value'] . '">';
	echo '<button type="submit" class="aligncenter">' . __( 'Update Download Link', 'classicpress' ) . '</button>';
	echo '</form>';
}


/* PROCESS UPDATE DOWNLOAD LINK FORM */
function kts_software_update_link_redirect() {

	# Check for nonce
	if ( empty( $_POST['update-nonce-name'] ) ) {
		return;
	}

	# If nonce is wrong
	$nonce = cp_check_nonce( $_POST['update-nonce-name'], $_POST['update-nonce-value'] );
	$referer = remove_query_arg( 'notification', wp_get_referer() );	
	$software_id = absint( $_POST['software-id'] );

	if ( $nonce === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=nonce-wrong-' . $software_id ) );
		exit;
	}
	
	$update = kts_maybe_update( $software_id );

	if ( $update === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=github-api-wrong-' . $software_id ) );
		exit;
	}

	if ( is_array( $update ) ) {
		wp_update_post( array(
			'ID'			=> $software_id,
			'post_content'	=> $update['description'],
			'meta_input'	=> array(
				'download_link'		=> $update['download_link'],
				'current_version'	=> $update['current_version'],
				'requires_php'		=> $update['requires_php'],
				'requires_cp'		=> $update['requires_cp'],
			),
		) );
	}

	# Generate success message
	wp_safe_redirect( esc_url_raw( $referer . '?notification=success-' . $software_id ) );
	exit;

}
add_action( 'template_redirect', 'kts_software_update_link_redirect' );

 
/* UPDATE DOWNLOAD LINKS VIA CRONJOB EVERY 10 MINUTES */
function kts_cron_update_download_links() {

	# Get all plugins, themes, and snippets
	$args = array(
		'numberposts'	=> 2,
		'post_type'		=> array( 'plugin', 'theme', 'snippet' ),
		'post_status'	=> 'publish',
		'orderby'     	=> 'modified',
		'order'       	=> 'ASC',
	);
	$posts = get_posts( $args );

	foreach( $posts as $key => $post ) {
		wp_update_post( $post );
		$update = kts_maybe_update( $post->ID );
		if ( $update === false || $update === true ) {
			continue;
		}

		wp_update_post( array(
			'ID'			=> $post->ID,
			'post_content'	=> $update['description'],
			'meta_input'	=> array(
				'download_link'		=> $update['download_link'],
				'current_version'	=> $update['current_version'],
				'requires_php'		=> $update['requires_php'],
				'requires_cp'		=> $update['requires_cp'],
			),
		) );
	}
}
add_action( 'update_cron_hook', 'kts_cron_update_download_links' );


/* CRONJOB SCHEDULE */
function kts_cron_add_ten_minutes( $schedules ) {
	$schedules['ten_minutes'] = array(
		'interval' => 10 * MINUTE_IN_SECONDS,
		'display' => __( 'Every 10 minutes' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'kts_cron_add_ten_minutes' );

function kts_cp_directory_cronjobs() {
	if ( ! wp_next_scheduled( 'update_cron_hook' ) ) {
		wp_schedule_event( time(), 'ten_minutes', 'update_cron_hook' );
	}
}
add_action( 'init', 'kts_cp_directory_cronjobs' );

/**
 * Get the latest version of a software from GitHub API
 *
 * To use a GitHub API token put define('GITHUB_API_TOKEN', 'YOURTOKENVALUE'); in wp-config.php
 * To create a token, go to https://github.com/settings/tokens and generate a new token.
 * When generating the new token don't select any scope.
 *
 * @param int  $software_id ID of the custom post types.
 * @param bool $force       Force reloading data.
 *
 * @return bool|array      An array containing updated data,
 *                         true if there is no update,
 *                         false if the check failed.
 *
 */
function kts_maybe_update( $software_id, $force = false ) {

	# Construct the URL to the GitHub API
	$download_link = get_post_meta( $software_id, 'download_link', true );
	$tidy_url = preg_replace( '~releases\/[\s\S]+?\.zip~', '', $download_link );
	$repo_url = str_replace( 'https://github.com/', 'https://api.github.com/repos/', $tidy_url );
	$github_url = $repo_url . 'releases/latest';

	# Make GET request to GitHub API to retrieve latest software download link
	if ( defined ( 'GITHUB_API_TOKEN' ) ) {
		$auth = [
			'headers' => [
				'Authorization' => 'token ' . GITHUB_API_TOKEN,
			],
		];
	} else {
		$auth = [];
	}

	$result = json_decode( wp_remote_retrieve_body( wp_safe_remote_get( esc_url_raw( $github_url ), $auth ) ) );
	if ( isset ( $result->message ) ) {
		trigger_error( 'Something went wrong with the GitHub API on item ' . $software_id . ': ' . esc_html( $result->message ) );
		return false;
	}

	$new_link = '';
	if ( ! empty( $result ) && ! empty( $result->assets ) ) {
		$new_link = $result->assets[0]->browser_download_url;
	}

	if ( empty( $new_link ) ) {
		return false;
	}

	# Check that URL to download software is to a later release	
	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $download_link, $orig_matches );
	$orig_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $orig_matches[0] );

	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $new_link, $new_matches );
	$new_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $new_matches[0] );

	# Update download link and current version if newer
	if ( version_compare( $new_version, $orig_version ) === 1 || $force ) {
		# Enable the download_url() and wp_handle_sideload() functions
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		# Download (upload?) file to temp dir
		$temp_file = download_url( $new_link, 5 ); // 5 secs before timeout
		if ( is_wp_error( $temp_file ) ) {
			trigger_error( 'Something went wrong with the GitHub API on item ' . $software_id . ': ' . esc_html( $temp_file->get_error_message() ) );
			return false;
		}

		# Array based on $_FILE as seen in PHP file uploads
		$file = array(
			'name'     => basename( $new_link ),
			'type'     => 'application/zip',
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		# Find slug from top level folder name
		$zip = new ZipArchive();
		$zip->open( $file['tmp_name'] );
		$slug = strstr( $zip->getNameIndex(0), '/', true );

		# Check that slug matches the current slug for the software
		$current_slug = get_post_meta( $software_id, 'slug', true );
		if ( $slug !== $current_slug ) {
			trigger_error( 'The slug for the new version does not match the current version.' );
			return false;
		}

		# Get description
		$readme_index = $zip->locateName( '/README.md', ZipArchive::FL_NOCASE );
		$readme_md = $zip->getFromIndex( $readme_index, 0, ZipArchive::FL_UNCHANGED );
		$description = kts_render_md( $readme_md );
		$description = wp_kses_post( preg_replace('~<h1>.*<\/h1>~', '', $description ) );

		# Check themes
		$post_type = get_post( $software_id )->post_type;
		if ( $post_type === 'theme' ) {

			# Get headers
			$style_index = $zip->locateName( 'style.css', ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR );
			$style_txt = $zip->getFromIndex( $style_index, 8192, ZipArchive::FL_UNCHANGED );

			$headers = kts_get_plugin_data( $style_txt );

			# If still no headers or no description from style.css file above, try readme.txt file
			if ( empty( $headers['RequiresCP'] ) || empty( $description ) ) {

				$readme_txt_index = $zip->locateName( 'readme.txt', ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR );
				$readme_txt = $zip->getFromIndex( $readme_txt_index, 8192, ZipArchive::FL_UNCHANGED );

				if ( empty( $headers['RequiresCP'] ) ) {
					$headers = kts_get_plugin_data( $readme_txt );
				}

				if ( empty( $description ) ) {
					$readme_txt = str_replace( ['====', '===', '=='], ['####', '###', '##'], $readme_txt );

					$description = kts_render_md( str_replace( '## Description ##', '', strstr( $readme_txt, '## Description ##' ) ) );
					$description = wp_kses_post( $description );
				}
			}
		}

		# Snippets
		elseif ( $post_type === 'snippet' ) {

			# Get headers
			for ( $i = 0; $i < $zip->numFiles; $i++ ) {
				if ( ! preg_match( '~\.php$~', $zip->getNameIndex( $i ) ) || substr_count( $zip->getNameIndex( $i ), '/' ) !== 1 ) {

					# Only check PHP files and don't recourse into subdirs
					continue;
				}
				$file_data = $zip->getFromIndex( $i, 8192 );
				$headers = kts_get_plugin_data( $file_data );
				if ( ! empty( $headers['RequiresCP'] ) ) {

					# We have the headers
					$main_plugin_file = $zip->getNameIndex( $i );
					break;
				}
			}
		}

		# Plugins
		else {

			# Check if most common location for main file contain headers
			$guessed_main_file = $slug . '/' . $slug . '.php';
			$file_data = $zip->getFromName( $guessed_main_file, 8192 );
			$headers = kts_get_plugin_data( $file_data );

			if ( empty( $headers['RequiresCP'] ) ) {

				# Parse other files for headers
				for ( $i = 0; $i < $zip->numFiles; $i++ ) {
					if ( ! preg_match( '~\.php$~', $zip->getNameIndex( $i ) ) || substr_count( $zip->getNameIndex( $i ), '/' ) !== 1 ) {

						# Only check PHP files and don't recourse into subdirs
						continue;
					}
					$file_data = $zip->getFromIndex( $i, 8192 );
					$headers = kts_get_plugin_data( $file_data );
					if ( ! empty( $headers['RequiresCP'] ) ) {

						# We have the headers
						$main_plugin_file = $zip->getNameIndex( $i );
						break;
					}
				}
			}

			# If still no headers or no description from README.md file above, try readme.txt file
			if ( empty( $headers['RequiresCP'] ) || empty( $description ) ) {

				$readme_txt_index = $zip->locateName( 'readme.txt', ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR );
				$readme_txt = $zip->getFromIndex( $readme_txt_index, 8192, ZipArchive::FL_UNCHANGED );

				if ( empty( $headers['RequiresCP'] ) ) {
					$headers = kts_get_plugin_data( $readme_txt );
				}

				if ( empty( $description ) ) {
					$readme_txt = str_replace( ['====', '===', '=='], ['####', '###', '##'], $readme_txt );

					$description = kts_render_md( str_replace( '## Description ##', '', strstr( $readme_txt, '## Description ##' ) ) );
					$description = wp_kses_post( $description );
				}
			}
		}

		# Delete temporary file
		wp_delete_file( $file['tmp_name'] );

		# Fallback to get software description
		if ( empty( $description ) ) {

			$github_info = wp_remote_get( rtrim( $repo_url, '/' ), $auth );
			if ( is_wp_error( $github_info ) ) {
				trigger_error( 'Something went wrong with the GitHub API on item ' . $software_id . ': ' . esc_html( $temp_file->get_error_message() ) );
				return false;
			}

			$data = json_decode( wp_remote_retrieve_body( $github_info ) );
			if ( isset ( $data->message ) ) {
				trigger_error( 'Something went wrong with the GitHub API for item ' . $software_id . ': ' . esc_html( $data->message ) );
				return false;
			}
			$default_branch = $data->default_branch;

			$readme_url = str_replace( 'https://github.com', 'https://raw.githubusercontent.com', $tidy_url );
			$readme_url = $readme_url . $default_branch . '/README.md';
			$readme = wp_remote_get( $readme_url );

			if ( wp_remote_retrieve_response_code( $readme ) === 200 ) {
				$description = kts_render_md( $readme['body'] );
				$description = wp_kses_post( preg_replace('~<h1>.*<\/h1>~', '', $description ) );
			}
			else {
				trigger_error( 'The new version has no description.' );
				return false;
			}
		}

		# Check for the existence of the remaining items that we need
		if ( empty( $headers ) ) {
			trigger_error( 'The new version has no headers.' );
			return false;
		}

		if ( empty( $headers['RequiresCP'] ) ) {
			trigger_error( 'The new version is missing a Requires CP header.' );
			return false;
		}

		if ( empty( $headers['RequiresPHP'] ) ) {
			trigger_error( 'The new version is missing a Requires PHP header.' );
			return false;
		}

		# Return data to update
		return array(
			'download_link'		=> $new_link,
			'current_version'	=> $new_version,
			'description'		=> $description,
			'requires_php'		=> $headers['RequiresPHP'],
			'requires_cp'		=> $headers['RequiresCP'],
		);
	}

	return true;
}

function kts_render_md($md) {

	$body = [ 'text' => $md ];
	$url  = 'https://api.github.com/markdown';
	$args = [
		'headers' => [
			'Accept'               => 'application/vnd.github+json',
			'X-GitHub-Api-Version' => '2022-11-28',
			'Authorization'        => 'token ' . GITHUB_API_TOKEN,
			'Content-Type'         => 'text/x-markdown'
		],
		'body' => json_encode( $body ),
	];
	$response = wp_remote_post( $url, $args );

	// Request failed
	if ( is_wp_error( $response ) ) {
		trigger_error( 'kts_render_md: ' . $response->get_error_message() );
		return '';
	}

	// Request is valid
	if ( isset( $response[ 'response' ][ 'code' ] ) && $response[ 'response' ][ 'code' ] === 200 ) {
		return $response['body'];
	}

	// Uncaught error
	$api_error = json_decode( $response['body'], true );
	if ( $api_error === null || ! isset ( $api_error[ 'message'] ) ) {
		return '';
	}

	// API error
	trigger_error( 'kts_render_md (API error): ' . $api_error[ 'message'] );
	return '';

}