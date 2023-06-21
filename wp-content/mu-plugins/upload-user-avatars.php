<?php
/**
 * Plugin Name: Upload User Avatars
 * Description: Enables upload of local avatar from admin or frontend
 * Version:     1.0.0
 * Author:      Tim Kaye
 * Author URI:  https://timkaye.org
 * Text domain: upload-user-avatars
 */

/* ADD SETTINGS TO DISCUSSION PAGE IN ADMIN */
function kts_avatar_admin_init() {	

	register_setting( 'discussion', 'upload_user_avatars_caps', 'kts_avatar_checkbox_options' );
	add_settings_field( 'upload-user-avatars-caps', __( 'Local Avatar Permissions', 'upload-user-avatars' ), 'kts_avatar_settings_field', 'discussion', 'avatars', array( 'label_for' => 'upload_user_avatars_caps' ) );

	register_setting( 'discussion', 'upload_user_avatars_folder', 'sanitize_text_field' );
	add_settings_field( 'upload-user-avatars-folder', __( 'Choose Avatar Folder', 'upload-user-avatars' ), 'kts_avatar_uploads_folder', 'discussion', 'avatars', array( 'label_for' => 'upload_user_avatars_folder' ) );

}
add_action( 'admin_init', 'kts_avatar_admin_init' );


/* DISCUSSION SETTINGS OPTION */
function kts_avatar_settings_field( $args ) {
	$caps = get_option( 'upload_user_avatars_caps' ); ?>

	<input type="checkbox" name="upload_user_avatars_caps" id="upload_user_avatars_caps" <?php checked( ! empty( $caps ) ); ?>>

	<?php _e( 'Only allow users with file upload capabilities (authors and above) to upload local avatars', 'upload-user-avatars' );
}


/* CHOOSE CAPS TO BE ABLE TO UPLOAD AVATAR */
function kts_avatar_checkbox_options( $input ) {
	return empty( $_POST['upload_user_avatars_caps'] ) ? 0 : 1;
}


/* CHOOSE AVATAR UPLOADS FOLDER */
function kts_avatar_uploads_folder( $args ) {
	$folder = get_option( 'upload_user_avatars_folder' ); ?>

	<?php _e( '<p>Choose the folder to which you want avatars to be uploaded, relative to the uploads folder. If the folder does not exist, it will be created.</p>', 'upload-user-avatars' ); ?>

	<input type="text" class="regular-text" name="upload_user_avatars_folder" id="upload_user_avatars_folder" value="<?php echo esc_attr( $folder ); ?>"> &nbsp;

	<?php _e( '<p>You might, for example, type <code>avatars</code> to have avatars uploaded to the <code>/uploads/avatars</code> folder, or <code>users/avatars</code> to have avatars uploaded to the <code>/uploads/users/avatars</code> folder.</p>', 'upload-user-avatars' );
	_e( '<p>Leave blank to use the uploads folder itself (or month- and year-based folders if you have selected that option on the Media Settings page).</p>', 'upload-user-avatars' );
}

/* SANITIZE AVATAR UPLOADS FOLDER CHOICE */
function kts_avatar_sanitize_folders( $input ) {
	return sanitize_text_field( $_POST['upload_user_avatars_folder'] );	 
}


/* CHANGE UPLOAD FOLDER FOR AVATARS */
function kts_avatar_change_upload_dir( $dirs ) {
	$folder = get_option( 'upload_user_avatars_folder' );
	$folder = trim( $folder, '/' );
	if ( ! empty( $folder ) ) {
		$dirs['subdir'] = '/' . $folder;
		$dirs['path'] = $dirs['basedir'] . '/' . $folder;
		$dirs['url'] = $dirs['baseurl'] . '/' . $folder;
	}
    return $dirs;
}


/* REMOVE LIST OF DEFAULT AVATARS AND REFERENCE TO GRAVATAR */
add_filter( 'avatar_defaults', '__return_empty_array' );
add_filter( 'default_avatar_select', '__return_empty_string' );
add_filter( 'user_profile_picture_description', '__return_false' );


/* FILTER AVATAR EARLY */
function kts_get_avatar( $avatar, $id_or_email, $args ) {
	$uploads = wp_upload_dir();
	$folder = get_option( 'upload_user_avatars_folder' );	
	$folder = trim( $folder, '/' ) . '/';
	$size = esc_attr( $args['size'] );
	$user = false;

	# Determine if we receive an ID, email, or type of object
	if ( is_numeric( $id_or_email ) ) {
		$user = get_user_by( 'id', absint( $id_or_email ) );
	}
	elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
	}
	elseif ( $id_or_email instanceof WP_User ) {
		$user = $id_or_email;
	}
	elseif ( $id_or_email instanceof WP_Comment ) {
		if ( ! empty( $id_or_email->user_id ) ) {
            $user = get_user_by( 'id', (int) $id_or_email->user_id );
        }
	}

	# Spy SVG if no longer a registered user (anonymous)
	if ( empty( $user ) ) {
		$anon = '<img alt="" src="' . esc_url( $uploads['baseurl'] . '/' . $folder ) . 'svg/anonymous.svg" class="avatar avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" role="presentation" aria-hidden="true" /><div class="hidden no-read-aloud" role="presentation" hidden>Icons made by <a href="https://www.flaticon.com/authors/pixel-perfect" title="Pixel perfect">Pixel perfect</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>';

		return apply_filters( 'kts_anonymous_avatar', $anon, $uploads, $folder, $size );
	}

	$local_avatars = get_user_meta( $user->ID, 'upload_user_avatar', true );

	# Fallback to SVG if no custom avatar uploaded
	if ( empty( $local_avatars ) || empty( $local_avatars['full'] ) ) {
		$svg_fallback = '<img alt="' . esc_attr( $user->display_name ) . '" src="' . esc_url( $uploads['baseurl'] . '/' . $folder . 'svg/' . sanitize_file_name( $user->display_name ) ) . '.svg" class="avatar avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';

		return apply_filters( 'kts_svg_avatar_fallback', $svg_fallback, $uploads, $folder, $size, $user );
	}

	# Generate a new size
	if ( ! array_key_exists( $size, $local_avatars ) ) {
		$local_avatars[$size] = $local_avatars['full']; // in case of failure elsewhere

		# Get path for image by converting URL
		$avatar_full_path = str_replace( $uploads['baseurl'], $uploads['basedir'], $local_avatars['full'] );

		# Generate the new size
		$editor = wp_get_image_editor( $avatar_full_path );
		if ( ! is_wp_error( $editor ) ) {
			$resized = $editor->resize( $size, $size, true );
			if ( ! is_wp_error( $resized ) ) {
				$dest_file = $editor->generate_filename();
				$saved = $editor->save( $dest_file );
				if ( ! is_wp_error( $saved ) ) {
					$local_avatars[$size] = str_replace( $uploads['basedir'], $uploads['baseurl'], $dest_file );
				}
			}
		}

		# Save updated avatar sizes
		update_user_meta( $user->ID, 'upload_user_avatar', $local_avatars );
	}

	if ( substr( $local_avatars[$size], 0, 4 ) !== 'http' ) {
		$local_avatars[$size] = home_url( $local_avatars[$size] );
	}

	$alt = get_the_author_meta( 'display_name', $user->ID );
	$author_class = is_author( $user->ID ) ? ' current-author' : '' ;

	$avatar = '<img alt="' . esc_attr( $alt ) . '" src="' . esc_url( set_url_scheme( $local_avatars[$size] ) ) . '" class="avatar avatar-' . $size . $author_class . ' photo" height="' . $size . '" width="' . $size . '" />';

	return apply_filters( 'upload_user_avatar', $avatar, $user, $size );
}
add_filter( 'pre_get_avatar', 'kts_get_avatar', 10, 3 );


/* FORM TO DISPLAY ON USER PROFILE EDIT SCREEN */
function kts_avatar_user_profile( $user ) { ?>

	<h3><?php _e( 'Avatar', 'upload-user-avatars' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="upload-user-avatar"><?php _e( 'Upload Avatar', 'upload-user-avatars' ); ?></label></th>
			<td style="width: 50px;" valign="top">
				<?php echo get_avatar( $user->ID ); ?>
			</td>
			<td> <?php

			$caps = get_option( 'upload_user_avatars_caps' );
			$avatar = get_user_meta( $user->ID, 'upload_user_avatar', true );

			if ( empty( $caps ) || current_user_can( 'upload_files' ) ) {

				# Nonce security
				wp_nonce_field( 'upload_user_avatar_nonce', '_upload_user_avatar_nonce', false );
					
				# File upload input
				echo '<input id="upload-local-avatar" type="file" name="upload_user_avatar" enterkeyhint="go"><br>';

				if ( empty( $avatar ) ) {
					echo '<p class="description">' . __( 'You currently have no photo or avatar. Use the button above to upload one, and then click Update Profile.', 'upload-user-avatars' ) . '</p>';
				}
				else {
					echo '<p><input type="checkbox" name="upload_user_avatar_erase" value="1"> ' . __( 'Delete current image', 'upload-user-avatars' ) . '</p>';
					echo '<p class="description">' . __( 'Update your photo or avatar, or check the box above to delete your current one, and then click Update Profile.', 'upload-user-avatars' ) . '</p>';
				}

			}
			else {
				if ( empty( $avatar ) ) {
					echo '<p class="description">' . __( 'No avatar has been uploaded.', 'upload-user-avatars' ) . '</p>';
				}
				else {
					echo '<p class="description">' . __( 'You do not have the appropriate media management permissions to change your avatar here.</p><p class="description">To change your avatar, contact the site administrator.', 'upload-user-avatars' ) . '</p>';
				}	
			}
			?>
			</td>
		</tr>
	</table>
	<script>
		var form = document.getElementById('your-profile');
		form.encoding = 'multipart/form-data';
		form.setAttribute('enctype', 'multipart/form-data');
	</script> <?php
}
add_action( 'show_user_profile', 'kts_avatar_user_profile' );
add_action( 'edit_user_profile', 'kts_avatar_user_profile' );


/* UPDATE USER'S AVATAR SETTING */
function kts_avatar_user_profile_update( $user_id ) {

	# Check for nonce
	if ( empty( $_POST['_upload_user_avatar_nonce'] ) || ! wp_verify_nonce( $_POST['_upload_user_avatar_nonce'], 'upload_user_avatar_nonce' ) ) {
		return;
	}

	$file = $_FILES['upload_user_avatar'];
	if ( ! empty( $file['name'] ) ) {

		# Allowed file extensions/types
		$mimes = array(
			'jpg|jpeg|jpe'	=> 'image/jpeg',
			'gif'			=> 'image/gif',
			'png'			=> 'image/png',
		);

		# Front end support - shortcode
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		# Delete all sizes of previous avatar
		kts_avatar_delete( $user_id );

		# Enable change of folder to which avatars are uploaded
		add_filter( 'upload_dir', 'kts_avatar_change_upload_dir' );

		# Upload avatar
		$avatar = wp_handle_upload( $file, array(
			'mimes' => $mimes,
			'test_form' => false,
			'unique_filename_callback' => function( $dir, $name, $ext ) use ( $user_id ) { // pass $user_id variable to anonymous function
				$user = get_user_by( 'id', $user_id );
				$name = $base_name = sanitize_file_name( $user->display_name . '-avatar-' . time() ); // use time() to bust cache
				$number = 1;

				while ( file_exists( $dir . '/' . $name . $ext ) ) {
					$name = $base_name . '-' . $number;
					$number++;
				}

				return $name . $ext;
			}
		) );

		# Remove filter
		remove_filter( 'upload_dir', 'kts_avatar_change_upload_dir' );

		# Handle failures
		if ( empty( $avatar['file'] ) && ! empty( $avatar['error'] ) ) {
			if ( is_admin() ) {
				add_action( 'user_profile_update_errors', function( $errors ) use ( $avatar ) { // pass $avatar variable
					return $errors->add( 'avatar_error', '<strong>' . __( 'An error occurred while attempting to upload the file.', 'upload-user-avatars' ) . '</strong> ' . esc_html( $avatar['error'] ) . __( ' An upload must be of a .jpg, .jpeg, .gif, or .png file.</p>', 'upload-user-avatars' ) );
				} );
				return;
			}
			else {
				echo '<div class="alert-error" role="alert">' . __( '<p>An error occurred while attempting to upload the file.', 'upload-user-avatars' ) . ' ' . esc_html( $avatar['error'] ) . __( ' An upload must be of a .jpg, .jpeg, .gif, or .png file.</p>', 'upload-user-avatars' ) . '</div>';
				return;
			}
		}

		# Save user information (overwriting previous)
		if ( isset( $avatar['url'] ) ) {
			update_user_meta( $user_id, 'upload_user_avatar', array( 'full' => $avatar['url'] ) );
		}

	}
	elseif ( ! empty( $_POST['upload_user_avatar_erase'] ) ) {
		# Delete the current avatar
		kts_avatar_delete( $user_id );
	}

	# Ensure page with shortcode is refreshed so that correct text is displayed
	if ( ! is_admin() ) {
		wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
		exit;
	}
}
add_action( 'personal_options_update', 'kts_avatar_user_profile_update' );
add_action( 'edit_user_profile_update', 'kts_avatar_user_profile_update' );


/* ENABLE AVATAR MANAGEMENT VIA SHORTCODE */
function kts_avatar_shortcode() {
	if ( ! is_user_logged_in() ) {
		return; // do nothing if not logged in
	}

	$user_id = get_current_user_id();
	$avatar = get_user_meta( $user_id, 'upload_user_avatar', true );

	if ( ! empty( $_FILES['upload_user_avatar'] ) ) {
		kts_avatar_user_profile_update( $user_id );
	}

	ob_start(); ?>

	<form id="upload-user-avatar-form" action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data"> <?php

		echo get_avatar( $user_id );

		$caps = get_option( 'upload_user_avatars_caps' );
		if ( empty( $caps ) || current_user_can( 'upload_files' ) ) {

			# Nonce security
			wp_nonce_field( 'upload_user_avatar_nonce', '_upload_user_avatar_nonce', false );
				
			# File upload input
			echo '<p><input id="upload-local-avatar" class="avatar-file" type="file" name="upload_user_avatar" enterkeyhint="go"></p>';

			if ( empty( $avatar ) ) {
				echo '<p class="description">' . __( 'You do not currently have a personalized photo or avatar. Use the button above to select one, and then click Update.', 'upload-user-avatars' ) . '</p>';
			}
			else {
				echo '<p><input type="checkbox" name="upload_user_avatar_erase" value="1"> ' . __( 'Delete current image', 'upload-user-avatars' ) . '</p>';
				echo '<p class="description">' . __( 'Update your photo or avatar, or check the box above to delete your current one, and then click Update.', 'upload-user-avatars' ) . '</p>';
			}
		}
		else {
			if ( empty( $avatar ) ) {
				echo '<p class="description">' . __( 'No avatar has been uploaded.', 'upload-user-avatars' ) . '</p>';
			}
			else {
				echo '<p class="description">' . __( 'You do not have the appropriate media management permissions to change your avatar here.</p><p class="description">To change your avatar, contact the site administrator.', 'upload-user-avatars' ) . '</p>';
			}	
		} ?>

		<input type="submit" name="avatar_submit" value="<?php _e( 'Update', 'upload-user-avatars' ); ?>" enterkeyhint="send">
	</form> <?php

	return ob_get_clean();
}
add_shortcode( 'upload-user-avatars', 'kts_avatar_shortcode' );


/* AVATAR WIDGET */
class KTS_Avatar_Widget extends WP_Widget {

	function __construct() {

		$widget_options = array (
			'classname' => 'upload-user-avatar-widget',
			'description' => 'Enable uploading of user avatar from front-end'
		);

		parent::__construct( 'upload_user_avatar_widget', 'Upload User Avatar', $widget_options );

	}

	# Output the widget form in admin
	function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
		</p> <?php
	}

	# Define the data saved by the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;          
	}

	# Display the widget on the front-end, but only on the Welcome page
	function widget( $args, $instance ) {
		if ( ! is_page( 'welcome' ) ) {
			return;
		}

		$title = apply_filters( 'widget_title', $instance['title'] );

		# Output code
		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		};
		echo kts_avatar_shortcode();

		echo $args['after_widget'];

	}
}

# REGISTER WIDGET
function kts_register_avatar_widget() {
	register_widget( 'KTS_Avatar_Widget' );  
}
add_action( 'widgets_init', 'kts_register_avatar_widget' );


/* DELETE USER'S AVATAR */
function kts_avatar_delete( $user_id ) {
	$old_avatars = get_user_meta( $user_id, 'upload_user_avatar', true );

	if ( is_array( $old_avatars ) && ! empty( $old_avatars['full'] ) ) {

		# Get directory where avatars stored
		$upload_path = wp_upload_dir();
		$folder = get_option( 'upload_user_avatars_folder' );
		$folder = trim( $folder, '/' ) . '/';
		$avatar_directory = $upload_path['basedir'] . '/' . $folder;

		# Delete every size of avatar for this user
		$user = get_user_by( 'id', $user_id );
		$name = sanitize_file_name( $user->display_name . '-avatar' );
		foreach( glob( $avatar_directory . '*.*' ) as $file ) {
			$pos = strpos( $file, $name );
			if ( $pos !== false ) {
				wp_delete_file( $file );
			}
		}
	}
	delete_user_meta( $user_id, 'upload_user_avatar' );
}


/* GENERATE FALLBACK AVATAR USING SVG OF FIRST INITIAL*/
# Based on https://avatars.oxro.io/
function kts_svg_initial_avatar_generator( $display_name ) {

    # Pick a dark color at random for the background
    $colors = ["#E284B3", "#FFD900", "#681313", "#D6293A", "#735372",  "#009975", "#FFBD39", "#FF0000", "#52437B", "#F76262", "#216583", "#293462", "#DD9D52", "#936B93", "#6DD38D", "#888888", "#6F8190", "#A27BEA", "#128762", "#96C2ED", "#3593CE", "#5EE2CD", "#96366E", "#E38080", "#FF3300", "#FFB366", "#0000FF", "#000099", "#000033"];

    $colors = apply_filters( 'kts_svg_avatar_backgrounds', $colors );

	$random_color_key = array_rand( $colors, 1 );
	$background = $colors[$random_color_key];
	$letter = strtoupper( remove_accents( $display_name[0] ) );
	
	$svg = '<?xml version="1.0" encoding="UTF-8"?><svg style="font-weight:bold;" width="96px" height="96px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><style type="text/css">@font-face {font-family: "Source Sans Pro";src: url("' . get_template_directory_uri() . '/fonts/source-sans-pro-latin-bold.woff2") format("woff2");font-weight: normal;font-style: normal;}</style></defs><rect x="0" y="0" width="500" height="500" style="fill:' . $background . '"/><text x="50%" y="50%" dy=".1em" fill="#eee" text-anchor="middle" dominant-baseline="middle" style="font-family: &quot;Source Sans Pro&quot;, sans-serif; font-size:72px; line-height: 1">' . $letter . '</text></svg>';

	$svg = apply_filters( 'kts_svg_avatar_style', $svg, $display_name );

	$uploads = wp_upload_dir();
	$folder = get_option( 'upload_user_avatars_folder' );
	$folder = trim( $folder, '/' ) . '/';
	$display_name = sanitize_file_name( $display_name );

	file_put_contents( $uploads['basedir'] . '/' . $folder . 'svg/' . $display_name . '.svg', $svg );

}


/* UPDATE SVG FALLBACK IF DISPLAY NAME UPDATED */
# Hook with early priority so completed before details sent to support site
function kts_update_svg_initial_avatar( $user_id, $old_user_data ) {
	$user = get_user_by( 'id', $user_id );
	if ( $user->display_name === $old_user_data->display_name ) {
		return;
	}

	# Delete current SVG fallback
	$uploads = wp_upload_dir();
	$folder = get_option( 'upload_user_avatars_folder' );
	$folder = trim( $folder, '/' ) . '/';
	$display_name = sanitize_file_name( $old_user_data->display_name );
	$svg = $uploads['basedir'] . '/' . $folder . 'svg/' . $display_name . '.svg';
	wp_delete_file( $svg );

	# Create new SVG fallback avatar
	kts_svg_initial_avatar_generator( $user->display_name );
}
add_action( 'profile_update', 'kts_update_svg_initial_avatar', 1, 2 );


/* DELETE BOTH AVATAR AND FALLBACK WHEN USER DELETED */
function kts_delete_avatar_when_user_deleted( $user_id ) {
	$uploads = wp_upload_dir();
	$folder = get_option( 'upload_user_avatars_folder' );
	$folder = trim( $folder, '/' ) . '/';
	$user = get_user_by( 'id', $user_id );
	$display_name = sanitize_file_name( $user->display_name );
	$svg = $uploads['basedir'] . '/' . $folder . 'svg/' . $display_name . '.svg';

	kts_avatar_delete( $user_id );
	wp_delete_file( $svg );
}
add_action( 'delete_user', 'kts_delete_avatar_when_user_deleted' );

/* BULK ACTION TO REGENERATE AVATARS */
# Add bulk action option to user list
add_filter('bulk_actions-users', 'register_my_bulk_actions');
function register_my_bulk_actions($bulk_actions)
{
	$bulk_actions['regenerate_avatars'] = __('Regenerate Avatars', 'domain');
	return $bulk_actions;
}

# Handle the bulk action
add_filter('handle_bulk_actions-users', 'my_bulk_action_handler', 10, 3);
function my_bulk_action_handler($redirect_to, $doaction, $users_ids)
{
	if ($doaction !== 'regenerate_avatars') {
		return $redirect_to;
	}

	foreach ($users_ids as $user_id) {
		kts_bulk_svg_initial_avatar_generator($user_id);
	}

	$redirect_to = add_query_arg('avatars_regenerated', count($users_ids), $redirect_to);
	return $redirect_to;
}

# Display the result notice
add_action('admin_notices', 'my_bulk_action_admin_notice');
function my_bulk_action_admin_notice()
{
	if (!empty($_REQUEST['avatars_regenerated'])) {
		$processed_count = intval($_REQUEST['avatars_regenerated']);
		printf('<div id="message" class="updated fade">' .
			_n(
				'%s avatar regenerated.',
				'%s avatars regenerated.',
				$processed_count,
				'domain'
			) . '</div>', $processed_count);
	}
}

# The modified function to regenerate avatars
function kts_bulk_svg_initial_avatar_generator($user_id)
{
	$user = get_user_by('id', $user_id);

	# Delete current SVG fallback
	$uploads = wp_upload_dir();
	$folder = get_option('upload_user_avatars_folder');
	$folder = trim($folder, '/') . '/';
	$display_name = sanitize_file_name($user->display_name);
	$svg = $uploads['basedir'] . '/' . $folder . 'svg/' . $display_name . '.svg';
	wp_delete_file($svg);

	# Create new SVG fallback avatar
	kts_svg_initial_avatar_generator($user->display_name);
}