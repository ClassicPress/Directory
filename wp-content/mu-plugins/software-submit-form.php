<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Software Submit Form
 * Plugin URI: https://directory.classicpress.net/
 * Description: Form to enable submission of a ClassicPress plugin, theme, or code snippet
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 0.1
 */

function kts_get_plugin_data( $file_data ) {
	$default_headers = array(
		'Name'			=> 'Plugin Name',
		'PluginURI'		=> 'Plugin URI',
		'Version' 		=> 'Version',
		'Description'	=> 'Description',
		'Author'		=> 'Author',
		'AuthorURI'		=> 'Author URI',
		'TextDomain'	=> 'Text Domain',
		'DomainPath'	=> 'Domain Path',
		'Network'		=> 'Network',
		'RequiresWP'	=> 'Requires at least',
		'RequiresPHP'	=> 'Requires PHP',
		'RequiresCP'	=> 'Requires CP', // lowest compatible version of ClassicPress
	);
	$plugin_data = kts_get_file_data( $file_data, $default_headers );
	$plugin_data['Network'] = ( 'true' == strtolower( $plugin_data['Network'] ) );
	return $plugin_data;
}

function kts_get_file_data( $file_data, $all_headers ) {
	$file_data = str_replace( "\r", "\n", $file_data );
	foreach ( $all_headers as $field => $regex ) {
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] )
			$all_headers[ $field ] = _cleanup_header_comment( $match[1] );
		else
			$all_headers[ $field ] = '';
	}
	return $all_headers;
}


function kts_render_software_submit_form() {
	$user_id = get_current_user_id();
	$two_fa = kts_2fa_enabled( $user_id );
	ob_start();

	if ( ! is_user_logged_in() ) {
		_e( '<p>You need to <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">log in</a> to be able to use the form on this page.</p>', 'classicpress' );
	}

	elseif ( ! current_user_can( 'edit_posts' ) ) { // Not a Contributor or above
		if ( $two_fa === false ) {
			_e( '<p>You must be approved before you can submit software for review.</p>', 'classicpress' );
			_e( '<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="' . esc_url( get_edit_profile_url( $user_id ) ) . '#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>.</p>', 'classicpress' );
			_e( '<p>You will receive an email when you have been approved.</p>', 'classicpress' );
		}
		else {
			$two_fa_method = get_user_meta( $user_id, '_two_factor_provider', true );
			if ( $two_fa_method === 'Two_Factor_Dummy' ) {
				_e( '<p>You must be approved before you can submit software for review.</p>', 'classicpress' );
				_e( '<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="' . esc_url( get_edit_profile_url( $user_id ) ) . '#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>. Enabling the dummy method is not acceptable for this purpose.</p>', 'classicpress' );
				_e( '<p>You will receive an email when you have been approved.</p>', 'classicpress' );
			}
			else {
				_e( '<p>Your account is pending review. You will receive an email when you have been approved.</p>', 'classicpress' );
			}
		}
	}

	else { // Contributor role or above
		_e( '<p>Please use the form below to upload your plugin or theme. All fields are required.</p>', 'classicpress' );
		_e( '<p>Before submitting your software, please ensure that it complies with the <a href="https://docs.classicpress.net/plugin-guidelines/directory-requirements/">Requirements</a> that must be met for listing in the ClassicPress Directory.</p>', 'classicpress' );
		_e( '<p>We also recommend that you run your code through the <a href="https://wpseek.com/pluginfilecheck/">Plugin Doctor</a>.</p>', 'classicpress' );
		_e( '<p>Once your software has been approved, details will be made public in this directory.</p>', 'classicpress' );

		$cp_nonce = cp_set_nonce( 'software_nonce' );
		$categories = get_categories( array( 
			'taxonomy'		=> 'category',
			'hide_empty'	=> false,
			'exclude'		=> array( get_cat_ID( 'Uncategorized' ) ),
		) );

		# Error Messages
		if ( isset( $_GET['notification'] ) ) {
			if ( $_GET['notification'] === 'nonce-wrong' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You have already submitted this form.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-software-type' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must specify the type of software that you are submitting!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-software-type' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'The software type you have specified is not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-name' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must provide a name for your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-slug' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must specify a suitable slug for your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-excerpt' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must provide a brief description of your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-excerpt' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'The brief description of your software is longer than 100 characters!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-categories' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must specify at least one category for your plugin!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-categories' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'The categories you have specified are not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-tags' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must specify at least one tag for your theme!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'too-many-tags' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You cannot specify more than three tags for your theme!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-current-version' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must specify the current version of your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-git-provider' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must specify your Git provider!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-git-provider' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'The Git provider you have specified is not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-download-link' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must provide a download link!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'invalid-download-link' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must provide a valid URL for the download link!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'invalid-github' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must provide a download link to a GitHub repository that is associated with the GitHub Username you have registered with the ClassicPress Directory.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'github-repo-error' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'There was a problem accessing the associated GitHub repository.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'temp-file-error' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'There was a problem creating a temporary file.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'duplicate-theme-slug' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'Your theme\'s slug is already taken. Please rebuild your zip file so that the top level folder within it has a unique name.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'duplicate-plugin-slug' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'Your plugin\'s slug is already taken. Please rebuild your zip file so that the top level folder within it has a unique name.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-headers' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'Your software is lacking required headers.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-requires-php' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You need to specify in a header called "Requires PHP" the minimum version of PHP compatible with your software.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-php-version' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'The minimum version of PHP you have specified is unrecognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-requires-cp' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You need to specify in a header called "Requires CP" the minimum version of ClassicPress compatible with your software.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-cp-version' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'The minimum version of ClassicPress you have specified is unrecognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-description' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'You must provide a description of your software in either a README.md file or a header called "Description".', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'not-sent' ) {
				echo '<div class="alert error-message" role="alert"><p>' . __( 'There was a problem submitting the form. Your message has not been sent.', 'classicpress' ) . '</p></div>';
			}
		}
		?>

		<section>
		<div class="section-header">
			<h2><?php _e( 'Software Information', 'classicpress' ); ?></h2>
			<small class="required-text"><?php _e( 'All fields are required.', 'classicpress' ); ?></small>
		</div>
		
		<noscript><div class="alert error-message" role="alert"><p><?php _e( 'This form will not work without JavaScript turned on.', 'classicpress' ); ?></p></div></noscript>

		<form id="submit-cp-code-form" method="post" autocomplete="off">

			<fieldset>
				<legend><?php _e( 'Select Type of Software', 'classicpress' ); ?></legend>
				<div class="clear"></div>
				<label for="plugin">
					<input id="plugin" class="mgr-lg" name="software_type" type="radio" value="plugin" required>
					Plugin
				</label>
				<br>
				<label for="theme">
					<input id="theme" class="mgr-lg" name="software_type" type="radio" value="theme" required>
					Theme
				</label>
			</fieldset>

			<div class="form-group">
				<label for="name"><?php _e( 'Name of Software', 'classicpress' ); ?></label>
				<input id="name" name="name" type="text" required>
			</div>

			<div class="form-group excerpt-group">
				<label for="excerpt"><?php _e( 'Brief Description of Software (no HTML)', 'classicpress' ); ?></label>
				<textarea id="excerpt" class="mb-0" name="excerpt" maxlength="150" rows="3" required></textarea>
				<span id="char-count">150/150</span>
			</div>
			<fieldset id="category" hidden>
				<legend id="cats"><?php _e( 'Specify to which of the following categories your plugin relates. (You must choose at least one.)', 'classicpress' ); ?></legend>
				<div class="clear"></div>

				<?php
				foreach( $categories as $category ) {
					echo '<input id="cat-' . $category->slug . '" class="mgr-lg" name="categories[]" type="checkbox" value="' . $category->cat_ID . '"  disabled>';
					echo '<label for="cat-' . $category->slug . '">' . $category->name . '</label>';
					echo '<br>';
				}
				?>

			</fieldset>

			<div id="tags-div" hidden>
				<label for="tags"><?php _e( 'Tags (you must specify at least one, and up to three, separated by commas)', 'classicpress' ); ?></label>
				<span id="max" class="alert error-message" role="alert" hidden><?php _e( 'You have specified more than three tags!', 'classicpress' ); ?></span>
				<input id="tags" name="tags" type="text" disabled>
			</div>

			<fieldset>
				<legend><?php _e( 'Select Git Provider (currently only GitHub)', 'classicpress' ); ?></legend>
				<div class="clear"></div>
				<label for="github">
					<input id="github" class="mgr-lg" name="git_provider" type="radio" value="github" checked required>
					GitHub
				</label>
				<br>
				<label for="gitlab">
					<input id="gitlab" class="mgr-lg" name="git_provider" type="radio" value="gitlab" disabled>
					GitLab
				</label>
			</fieldset>

			<div class="form-group">
				<label for="download_link"><?php _e( 'Software Download Link (full URL including https://)', 'classicpress' ); ?></label>
				<input id="download_link" name="download_link" class="mb-0" type="url" placeholder="https://" required>
				<small class="form-text">Example: https://github.com/classicpress/classic-seo/releases/download/v1.0.0/classic-seo.zip</small>
			</div>
			<input type="hidden" name="cp-nonce-name" value="<?php echo $cp_nonce['name']; ?>">
			<input type="hidden" name="cp-nonce-value" value="<?php echo $cp_nonce['value']; ?>">
			<div class="form-btn">
				<button id="submit-btn" type="submit" enterkeyhint="send">Submit</button>
				<button type="reset" enterkeyhint="go">Clear</button>
			</div>
		</form>
		</section>
	<?php
	}
	echo ob_get_clean();
}


/* PROCESS SUBMISSION FORM */
function kts_software_submit_form_redirect() {

	# Check that user has the capability to submit software for review
	if ( ! current_user_can( 'edit_posts' ) ) { // Contributor role or above
		return;
	}

	# Check for nonce
	if ( empty( $_POST['cp-nonce-name'] ) ) {
		return;
	}

	# If nonce is wrong
	$nonce = cp_check_nonce( $_POST['cp-nonce-name'], $_POST['cp-nonce-value'] );
	$referer = remove_query_arg( 'notification', wp_get_referer() );

	if ( $nonce === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=nonce-wrong' ) );
		exit;
	}

	# Get correct type of software
	$post_type = sanitize_text_field( wp_unslash( $_POST['software_type'] ) );

	# Check that the type of software has been specified
	if ( empty( $post_type ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-software-type' ) );
		exit;
	}

	# Check that the type of software specified actually exists
	$software_types = array( 'plugin', 'theme', 'snippet' );
	if ( ! in_array( $post_type, $software_types ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-software-type' ) );
		exit;
	}

	# If the software is a plugin, check that at least one category has been specified
	$category_ids = isset( $_POST['categories'] ) ? array_map( 'absint', wp_unslash( $_POST['categories'] ) ) : [];
	if ( $post_type === 'plugin' ) {
		if ( empty( $category_ids ) ) {
			wp_safe_redirect( esc_url_raw( $referer . '?notification=no-categories' ) );
			exit;
		}
		else { // and check each category is recognized
			$cat_ids = get_categories( array( 
				'taxonomy'		=> 'category',
				'hide_empty'	=> false,
				'exclude'		=> array( get_cat_ID( 'Uncategorized' ) ),
				'fields'		=> 'ids',
			) );
			foreach( $category_ids as $cat_id ) {
				if ( ! in_array( $cat_id, $cat_ids ) ) {
					wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-categories' ) );
					exit;
				}
			}
		}
	}

	# Check that at least one tag has been specified for a code snippet	
	$tags = isset( $_POST['tags'] ) ? sanitize_text_field( wp_unslash( $_POST['tags'] ) ) : '';
	if ( $post_type === 'theme' ) {
		if ( empty( $tags ) ) {
			wp_safe_redirect( esc_url_raw( $referer . '?notification=no-tags' ) );
			exit;
		} // and check no more than three provided
		else {
			$tags = str_replace( '#', '', strtolower( $tags ) );
			$tags_array = explode( ',', $tags );
			if ( count( $tags_array ) > 3 ) {
				wp_safe_redirect( esc_url_raw( $referer . '?notification=too-many-tags' ) );
				exit;
			}				
		}
	}

	# Check that name of software has been provided
	if ( empty( $_POST['name'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-name' ) );
		exit;
	}

	# Check that brief description of software has been provided
	if ( empty( $_POST['excerpt'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-excerpt' ) );
		exit;
	}

	# Check that brief description of software does not exceed 150 characters
	if ( strlen( $_POST['excerpt'] ) > 150 ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=excerpt-too-long' ) );
		exit;
	}

	# Check that Git provider has been provided
	if ( empty( $_POST['git_provider'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-git-provider' ) );
		exit;
	}

	# Check that Git provider specified actually exists
	$git_providers = array( 'github' );
	if ( ! in_array( $_POST['git_provider'], $git_providers ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-git-provider' ) );
		exit;
	}

	# Check that URL to download software has been provided
	if ( empty( $_POST['download_link'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-download_link' ) );
		exit;
	}

	# Check that the download link is a valid URL
	if ( filter_var( $_POST['download_link'], FILTER_VALIDATE_URL ) === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=invalid-download-link' ) );
		exit;
	}

	# Prevent form title being all upper case
	$title = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	if ( strtoupper( $title ) === $title ) { // all upper case
		$title = ucwords( strtolower( $title ) ); // convert to title case
	}

	# Get download link
	$download_link = esc_url_raw( wp_unslash( $_POST['download_link'] ) );

	# Check that the download link points to GitHub URI associated with GitHub Username and name of software
	$user_id = get_current_user_id();
	$github_username = get_user_meta( $user_id, 'github_username', true );	
	$update_uri = esc_url_raw( 'https://github.com/' . $github_username . '/' );
	
	if ( stripos( $download_link, $update_uri ) !== 0 ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=invalid-github' ) );
		exit;
	}

	# Access GitHub repo
	$github_api = str_replace( 'https://github.com', 'https://api.github.com/repos', $download_link );
	$github_api = substr( $github_api, 0, strpos( $github_api, '/releases' ) );

	# Find default Github branch for software
	# Make GET request to GitHub API to retrieve latest software download link
	# To use a GitHub API token put define('GITHUB_API_TOKEN', 'YOURTOKENVALUE'); in wp-config.php
	# To create a token, go to https://github.com/settings/tokens and generate a new token.
	# When generating the new token don't select any scope.
	if ( defined ( 'GITHUB_API_TOKEN' ) ) {
		$auth = [
			'headers' => [
				'Authorization' => 'token ' . GITHUB_API_TOKEN,
			],
		];
	} else {
		$auth = [];
	}

	$github_info = wp_remote_get( $github_api, $auth );
	if ( is_wp_error( $github_info ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=github-repo-error' ) );
		exit;
	}
	$data = json_decode( wp_remote_retrieve_body( $github_info ) );
	if ( isset ( $data->message ) ) {
		trigger_error( 'Something went wrong with GitHub API: ' . esc_html( $data->message ) );
		wp_safe_redirect( esc_url_raw( $referer . '?notification=github-repo-error' ) );
		exit;
	}
	$default_branch = $data->default_branch;

	# Get current version of software
	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $download_link, $matches );
	$current_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $matches[0] );

	# Enable the download_url() and wp_handle_sideload() functions
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	# Download (upload?) file to temp dir
	$temp_file = download_url( $download_link, 5 ); // 5 secs before timeout
	if ( is_wp_error( $temp_file ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=temp-file-error' ) );
		exit;
	}

	# Array based on $_FILE as seen in PHP file uploads
	$file = array(
		'name'     => basename( $download_link ),
		'type'     => 'application/zip',
		'tmp_name' => $temp_file,
		'error'    => 0,
		'size'     => filesize( $temp_file ),
	);

	# Find slug from top level folder name
	$zip = new ZipArchive();
	$zip->open( $file['tmp_name'] );
	$slug = strstr( $zip->getNameIndex(0), '/', true );

	# Get description
	$readme_index = $zip->locateName( '/README.md', ZipArchive::FL_NOCASE );
	$readme_md = $zip->getFromIndex( $readme_index, 0, ZipArchive::FL_UNCHANGED );
	$description = kts_render_md( $readme_md );
	$description = wp_kses_post( preg_replace('~<h1>.*<\/h1>~', '', $description ) );

	# Check that slug is unique, holding errors until temporary file deleted
	$slug_taxonomy = '';
	$slug_problem = '';
	$headers = [];

	if ( $post_type === 'theme' ) { // Themes
		$slugs = get_terms( array(
			'taxonomy' => 'theme_slugs',
			'hide_empty' => false,
			'fields' => 'names',
		) );

		if ( in_array( sanitize_title( $slug ), $slugs ) ) {
			$slug_problem = 'theme';
		}

		$slug_taxonomy = 'theme_slugs';

		# Don't bother with further processing if there's a slug problem
		if ( empty( $slug_problem ) ) {

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
	}
	else { // Plugins
		$slugs = get_terms( array(
			'taxonomy' => 'plugin_slugs',
			'hide_empty' => false,
			'fields' => 'names',
		) );

		if ( in_array( sanitize_title( $slug ), $slugs ) ) {
			$slug_problem = 'plugin';
		}

		$slug_taxonomy = 'plugin_slugs';

		# Don't bother with further processing if there's a slug problem
		if ( empty( $slug_problem ) ) {

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
	}

	# Delete temporary file
	wp_delete_file( $file['tmp_name'] );

	# Bail and redirect if slug provided is already taken
	if ( ! empty( $slug_problem ) ) {
		if ( $slug_problem === 'theme' ) {
			wp_safe_redirect( esc_url_raw( $referer . '?notification=duplicate-theme-slug' ) );
			exit;
		}
		if ( $slug_problem === 'plugin' ) {
			wp_safe_redirect( esc_url_raw( $referer . '?notification=duplicate-plugin-slug' ) );
			exit;
		}
	}

	# Bail and redirect if lacking required headers
	if ( empty( $headers ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-headers' ) );
		exit;
	}

	if ( empty( $headers['RequiresPHP'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-requires-php' ) );
		exit;
	}

	# Check that minimum version of PHP is a float
	if ( filter_var( $headers['RequiresPHP'], FILTER_VALIDATE_FLOAT ) === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-php-version' ) );
		exit;
	}

	if ( empty( $headers['RequiresCP'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-requires-cp' ) );
		exit;
	}

	# Check that minimum version of CP is a float
	if ( filter_var( $headers['RequiresCP'], FILTER_VALIDATE_FLOAT ) === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-cp-version' ) );
		exit;
	}

	# Fallback for getting software description
	if ( empty( $description ) && empty( $headers['Description'] ) ) {
		$github_url = str_replace( 'https://github.com', 'https://raw.githubusercontent.com', $download_link );
		$github_url = substr( $github_url, 0, strpos( $github_url, '/releases' ) );
		$readme_url = $github_url . '/' . $default_branch . '/README.md';
		$readme = wp_remote_get( $readme_url );

		if ( wp_remote_retrieve_response_code( $readme ) === 200 ) {
			$description = kts_render_md( $readme['body'] );
			$description = wp_kses_post( preg_replace('~<h1>.*<\/h1>~', '', $description ) );
		}
		else {
			wp_safe_redirect( esc_url_raw( $referer . '?notification=no-description' ) );
			exit;
		}
	}
	
	# Get brief description of software
	$excerpt = sanitize_text_field( wp_unslash( $_POST['excerpt'] ) );

	# Get git provider
	$git_provider = sanitize_text_field( wp_unslash( $_POST['git_provider'] ) );

	# Submit form as a post type
	$post_info = array(
		'post_title'	=> $title,
		'post_excerpt'	=> $excerpt,
		'post_content'	=> ( ! empty( $description ) ) ? $description : wp_kses_post( $headers['Description'] ),
		'post_type'		=> $post_type,
		'post_status'	=> 'draft',
		'post_author'	=> $user_id,
		'comment_status'=> 'closed',
	);

	# Add categories if software is a plugin or tags if a code snippet
	if ( $post_type === 'plugin' ) {
		$post_info['post_category'] = $category_ids;
	}
	elseif ( $post_type === 'theme' ) {
		$tags_array = array_map( 'trim', $tags_array );
		$post_info['tags_input'] = $tags_array;
	}

	# Save post
	$post_id = wp_insert_post( $post_info );

	# Generate an error message if there is a problem with submitting the form
	if ( $post_id === 0 || is_wp_error( $post_id ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=not-sent' ) );
		exit;
	}

	# Add slug as both postmeta and custom taxonomy
	add_post_meta( $post_id, 'slug', $slug );	
	wp_set_object_terms( $post_id, [sanitize_title( $slug )], $slug_taxonomy );
	
	# Get name of developer for REST API
	$user = get_user_by( 'id', $user_id );
	add_post_meta( $post_id, 'developer_name', sanitize_text_field( $user->display_name ) );

	# Add other meta fields for REST API
	add_post_meta( $post_id, 'current_version', $current_version );
	add_post_meta( $post_id, 'git_provider', $git_provider );
	add_post_meta( $post_id, 'download_link', $download_link );
	add_post_meta( $post_id, 'requires_php', $headers['RequiresPHP'] );
	add_post_meta( $post_id, 'requires_cp', $headers['RequiresCP'] );
	add_post_meta( $post_id, 'published_at', date("Y-m-d") );

	# Add names of categories and tags to meta fields for REST API
	if ( $post_type === 'plugin' ) {

		# Add names and slugs of categories to meta for REST API
		$cat_names = '';
		$cat_slugs = '';
		foreach( $category_ids as $key => $category_id ) {
			$category = get_category( $category_id );
			if ( $key === 0 ) {
				$cat_names .= $category->name;
				$cat_slugs .= $category->slug;
			}
			else {
				$cat_names .= ',' . $category->name;
				$cat_slugs .= ',' . $category->slug;
			}
		}
		add_post_meta( $post_id, 'category_names', $cat_names );
		add_post_meta( $post_id, 'category_slugs', $cat_slugs );
	}
	elseif ( $post_type === 'theme' ) {
		add_post_meta( $post_id, 'tags', implode( ',', $tags_array ) );
	}

	# Redirect to post where published
	wp_safe_redirect( esc_url_raw( get_permalink( $post_id ) ) );
	exit;
}
add_action( 'template_redirect', 'kts_software_submit_form_redirect' );


/* EMAIL ALL SITE ADMINISTRATORS AND EDITORS WHEN SOFTWARE SUBMITTED */
function kts_email_on_software_submitted( $post_id, $meta_key, $_meta_value ) {

	# Bail if not relevant CPT
	$post = get_post( $post_id );
	if ( ! in_array( $post->post_type, ['plugin', 'theme'] ) ) {
		return;
	}

	# Bail if not download link metadata
	if ( $meta_key !== 'download_link' ) {
		return;
	}

	# Get details of message
	$subject = __( 'A new ' . esc_html( $post->post_type ) . ' has been submitted for review', 'classicpress' );

	$post_admin_url = admin_url( 'post.php?post=' . $post->ID ) . '&action=edit';

	$message = __( 'The ' . esc_html( $post->post_type ) . '  is called ' . esc_html( $post->post_title ) . '. You will find the details on <a href="' . esc_url( $post_admin_url ) . '">this admin page</a>. The download link is ' . make_clickable( esc_url( $_meta_value ) ) . '.', 'classicpress' );

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	# Get email addresses of administrators and editors and send
	$users = get_users( [ 'role__in' => [ 'administrator', 'editor' ] ] );
	foreach( $users as $user ) {
		wp_mail( $user->user_email, $subject, $message, $headers );
	}
}
add_action( 'add_post_meta', 'kts_email_on_software_submitted', 10, 3 );
