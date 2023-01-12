<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Registration Form and User Enhancements
 * Plugin URI: https://directory.classicpress.net/
 * Description: Adds fields to default registration form
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/
 
/* REGISTER CUSTOM USER TAXONOMY */
# Enables scalable querying when checking value directly
function kts_register_github_taxonomy() {

	# https://tomjn.com/2018/03/16/utility-taxonomies/
	$args = array(
		'hierarchical'		=> false,
		'label'				=> __( 'GitHub Usernames', 'classicpress' ),
		'show_ui'			=> false,
		'public'			=> false,
		'show_admin_column'	=> false,
		'rewrite'			=> false,
	);
	register_taxonomy( 'github_usernames', 'user', $args );
}
add_action( 'init', 'kts_register_github_taxonomy' );


/* ADD CUSTOM FIELDS TO REGISTRATION FORM */
function kts_registration_form_fields() { ?>

	<p>
		<label for="first_name"><?php _e( 'First Name', 'classicpress' ); ?></label>
		<br>
		<input id="first_name" name="first_name" type="text" required>
	</p>

	<p>
		<label for="last_name"><?php _e( 'Last Name', 'classicpress' ); ?></label>
		<br>
		<input id="last_name" name="last_name" type="text" required>
	</p>

	<p>
		<label for="github_username"><?php _e( 'GitHub Username (or GitHub Org Name)', 'classicpress' ) ?></label>
		<input id="github_username" name="github_username" type="text" required>
	</p>

<?php
}
add_action( 'register_form', 'kts_registration_form_fields' );


/* ENABLE ERROR MESSAGES FOR CUSTOM FIELDS */
function kts_registration_errors( $errors, $sanitized_user_login, $user_email ) {

	if ( empty( $_POST['first_name'] ) ) {
		$errors->add( 'first_name_error', __( '<strong>ERROR</strong>: Please enter your first name.', 'classicpress' ) );
	}

	if ( empty( $_POST['last_name'] ) ) {
		$errors->add( 'last_name_error', __( '<strong>ERROR</strong>: Please enter your last name.', 'classicpress' ) );
	}

	if ( empty( $_POST['github_username'] ) ) {
		$errors->add( 'github_username_error', __( '<strong>ERROR</strong>: Please enter your GitHub username.', 'classicpress' ) );
	}
	else {
		# Prevent someone registering with the Directory with a GitHub Username that's already been claimed
		$github_username = sanitize_text_field( wp_unslash( $_POST['github_username'] ) );

		$github_usernames = get_terms( array(
			'taxonomy' => 'github_usernames',
			'hide_empty' => false,
			'fields' => 'names',
		) );

		if ( in_array( sanitize_title( $github_username ), $github_usernames ) ) {
			$errors->add( 'no_repo_error', __( '<strong>ERROR</strong>: This GitHub username has already been registered with the ClassicPress Directory.', 'classicpress' ) );
		}
		
		# Check if there's a GitHub repo associated with this username
		$github_api = 'https://api.github.com/users/' . $github_username;
		$github_repo = wp_remote_get( $github_api );
		$data = json_decode( wp_remote_retrieve_body( $github_repo ) );
		if ( empty( $data ) || empty( $data->repos_url ) ) {
			$errors->add( 'no_repo_error', __( '<strong>ERROR</strong>: There is no GitHub repository associated with this GitHub username.', 'classicpress' ) );
		}
	}

	return $errors;
}
add_filter( 'registration_errors', 'kts_registration_errors', 10, 3 );


/* PROCESS CUSTOM FIELDS */
function kts_register_custom_fields( $user_id ) {

	# Require first and last name
	if ( empty( $_POST['first_name'] ) ) {
		return;
	}

	if ( empty( $_POST['last_name'] ) ) {
		return;
	}

	# Require Github username
	if ( empty( $_POST['github_username'] ) ) {
		return;
	}

	# Check if there's a GitHub repo associated with this GitHub Username
	$github_username = sanitize_text_field( wp_unslash( $_POST['github_username'] ) );
	$github_api = 'https://api.github.com/users/' . $github_username;
	$github_repo = wp_remote_get( $github_api );
	$data = json_decode( wp_remote_retrieve_body( $github_repo ) );
	if ( empty( $data ) || empty( $data->repos_url ) ) {
		return;
	}

	# Prevent someone registering with the Directory with a GitHub Username that's already been claimed
	$current_gu = get_user_meta( $user_id, 'github_username', true );

	if ( $github_username !== $current_gu ) {
		$github_usernames = get_terms( array(
			'taxonomy' => 'github_usernames',
			'hide_empty' => false,
			'fields' => 'names',
		) );

		if ( in_array( sanitize_title( $github_username ), $github_usernames ) ) {
			return;
		}

		# Good to go, so delete current taxonomy term
		$current_gu_tax_term_ids = wp_get_object_terms(
			$user_id, 
			'github_usernames',
			array(
				'fields' => 'ids',
			),
		);
		if ( ! empty( $current_gu_tax_term_ids ) ) {
			wp_delete_term( $current_gu_tax_term_ids[0], 'github_usernames' );
		}
	}

	# OK to update custom fields
	$first_name = sanitize_text_field( wp_unslash( $_POST['first_name'] ) );
	update_user_meta( $user_id, 'first_name', $first_name );

	$last_name = sanitize_text_field( wp_unslash( $_POST['last_name'] ) );
	update_user_meta( $user_id, 'last_name', $last_name );

	# On registration only, add both meta and custom taxonomy for GitHub Username
	if ( empty( $current_gu ) ) {
		add_user_meta( $user_id, 'github_username', $github_username );
		wp_set_object_terms( $user_id, [sanitize_title( $github_username )], 'github_usernames' );
	}

	# Enable administrators to update GitHub Username
	if ( is_admin() && current_user_can( 'manage_options' ) ) {
		update_user_meta( $user_id, 'github_username', $github_username );
		wp_remove_object_terms( $user_id, sanitize_title( $current_gu ), 'github_usernames' );
		wp_set_object_terms( $user_id, [sanitize_title( $github_username )], 'github_usernames' );
	}
			
}
add_action( 'user_register', 'kts_register_custom_fields' );
add_action( 'edit_user_created_user', 'kts_register_custom_fields' ); // backend
add_action( 'personal_options_update', 'kts_register_custom_fields' );
add_action( 'edit_user_profile_update', 'kts_register_custom_fields' );


/* BACK-END USER REGISTRATION CUSTOM FIELDS */
# Do not provide users with a means for changing their GitHub Username
function kts_user_admin_register_custom_fields( $user ) {
	$editable = 'readonly';
	if ( current_user_can( 'manage_options' ) ) {
		$editable = '';
	} ?>
	
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="github_username"><?php _e( 'GitHub Username', 'classicpress' ) ?></label> <span class="description"><?php _e( '(required)', 'classicpress' ); ?></span>
			</th>

			<td>
				<input id="github_username" name="github_username" type="text" class="regular-text" value="<?php echo esc_attr( get_user_meta( $user->ID, 'github_username', true ) ); ?>" <?php echo $editable; ?>>
				<br>
			</td>
		</tr>
	</table>

<?php
}
add_action( 'user_new_form', 'kts_user_admin_register_custom_fields' );
add_action( 'show_user_profile', 'kts_user_admin_register_custom_fields' );
add_action( 'edit_user_profile', 'kts_user_admin_register_custom_fields' );


/* BACKEND CUSTOM FIELDS ERRORS */
function kts_user_profile_update_errors( $errors, $update, $user ) {

	# Require first and last name
	if ( empty( $_POST['first_name'] ) ) {
		$errors->add( 'first_name_error', __( '<strong>ERROR</strong>: Please enter a first name.', 'classicpress' ) );
	}

	if ( empty( $_POST['last_name'] ) ) {
		$errors->add( 'last_name_error', __( '<strong>ERROR</strong>: Please enter a last name.', 'classicpress' ) );
	}
}
add_action( 'user_profile_update_errors', 'kts_user_profile_update_errors', 10, 3 );


/* DISPLAY EXTRA INFO ON USERS LIST ADMIN PAGE */
function kts_add_user_admin_columns( $columns ) {
	$columns['plugin'] = __( 'Plugins', 'classicpress' );
	$columns['theme'] = __( 'Themes', 'classicpress' );
	$columns['github_username'] = __( 'GitHub Username', 'classicpress' );
	$columns['last_login'] = __( 'Last Login', 'classicpress' );
	unset( $columns['posts'] );
	return $columns;
}
add_filter( 'manage_users_columns', 'kts_add_user_admin_columns' );

function kts_user_meta_columns( $custom_column, $column_name, $user_id ) {	
	$timezone = get_option( 'timezone_string' );

	switch( $column_name ) {
		case 'plugin':
			return count_user_posts( $user_id, 'plugin' );
		break;
		case 'theme':
			return count_user_posts( $user_id, 'theme' );
		break;
		case 'github_username':
			return get_user_meta( $user_id, 'github_username', true );
		break;
		case 'last_login':
			$last_login = (int) get_user_meta( $user_id, 'last_login', true );
			return $last_login ? kts_ts2time( $last_login, $timezone ) : '';
		break;
	}
}
add_action( 'manage_users_custom_column', 'kts_user_meta_columns', 9, 3 );


/* RECORD LAST SUCCESSFUL LOGIN */
function kts_user_last_login( $user_login, $user ) {

	# Set time
    update_user_meta( $user->ID, 'last_login', time() );
}
add_action( 'wp_login', 'kts_user_last_login', 9, 2 );


/* CHANGE AUTHOR BASE TO DEVELOPER */
function kts_change_author_base() {
	global $wp_rewrite;
	$wp_rewrite->author_base = 'developers';
}
add_action( 'init', 'kts_change_author_base' );


/* REMOVE TITLE PREFIX ON ARCHIVE PAGES */
function kts_change_author_archive_base( $title ) {
    if ( is_author() ) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    }
    elseif ( is_tax() ) {
        $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
    }
    elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'kts_change_author_archive_base' );


/* KEEP THOSE OTHER THAN EDITORS AND ADMINISTRATORS ON PROFILE PAGE IN ADMIN */
function kts_restrict_admin_access() {
	global $pagenow;

	if ( ! current_user_can( 'edit_pages' ) ) {
		if ( $pagenow !== 'profile.php' ) {
			wp_redirect( esc_url_raw( admin_url( 'profile.php' ) ) );
			exit;
		}
	}

	# Remove ability to choose admin colors
	remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
}
add_action( 'admin_init', 'kts_restrict_admin_access' );


/* DISABLE VISUAL EDITOR */
add_filter( 'user_can_richedit' , '__return_false' );


/* REMOVE VISUAL EDITOR, KEYBOARD SHORTCUTS, AND TOOLBAR OPTIONS */
if ( ! function_exists( 'kts_remove_personal_options' ) ) {

	function kts_remove_personal_options( $subject ) {
		global $pagenow;
		if ( $pagenow === 'profile.php' ) {
			$subject = preg_replace( '#<h2 class="user-profile-personal-options">Personal Options</h2>.+?/table>#s', '', $subject, 1 );
			return $subject;
		}
	}

	function kts_profile_subject_start() {
		global $pagenow;
		if ( $pagenow === 'profile.php' ) {
			ob_start( 'kts_remove_personal_options' );
		}
	}

	function kts_profile_subject_end() {
		global $pagenow;
		if ( $pagenow === 'profile.php' ) {
			ob_end_flush();
		}
	}
}
add_action( 'admin_head', 'kts_profile_subject_start' );
add_action( 'admin_footer', 'kts_profile_subject_end' );


/* REMOVE ADMIN MENU ITEMS FOR THOSE OTHER THAN EDITORS AND ADMINISTRATORS */
function kts_remove_admin_menu_items() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit.php?post_type=plugin' );
		remove_menu_page( 'edit.php?post_type=theme' );
		remove_menu_page( 'edit.php?post_type=review' );
		remove_menu_page( 'edit.php?post_type=message' );
		remove_menu_page( 'edit.php?post_type=page' );
		remove_menu_page( 'upload.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'users.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'options-general.php' );
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'plugins.php' );
	}
}
add_action( 'admin_menu', 'kts_remove_admin_menu_items' );


/* ADD LINKS TO THE ADMIN BAR */
function kts_add_toolbar_links( $admin_bar ) {
	$user_id = get_current_user_id();

	$admin_bar->add_menu( array(
		'id'    => 'my-plugins',
		'title' => __( 'My Plugins' ),
		'href'  => esc_url( get_author_posts_url( $user_id ) . '#ui-id-1' ),
		'meta'  => array(
			'title' => __( 'My Plugins' ),
			'target' => '_blank',
			'class' => 'my_menu_item_class'
		),
	) );
	$admin_bar->add_menu( array(
		'id'    => 'my-themes',
		'title' => __( 'My Themes' ),
		'href'  => esc_url( get_author_posts_url( $user_id ) . '#ui-id-2' ),
		'meta'  => array(
			'title' => __( 'My Themes' ),
			'target' => '_blank',
			'class' => 'my_menu_item_class'
		),
	) );
}
add_action( 'admin_bar_menu', 'kts_add_toolbar_links', 100 );


/* EMAIL DEVELOPERS WHEN ROLE CHANGED TO CONTRIBUTOR */
function kts_email_on_role_to_contributor( $user_id, $new_role, $old_roles ) {
		 	
	# Get role info
	$user = get_userdata( $user_id );
	
	# Send email when membership is first approved
	if ( in_array( 'subscriber', $old_roles ) && $new_role === 'contributor' ) {
		$to = $user->user_email;
		$subject = __( 'ClassicPress Directory Approval', 'classicpress' );
		$message = __( 'You have been approved to submit software to be listed in the <a href="' . esc_url( home_url( '/' ) ) . '">ClassicPress Directory</a>.', 'classicpress' );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $to, $subject, $message, $headers );
	}	
}
add_action( 'set_user_role', 'kts_email_on_role_to_contributor', 10, 3 );

/* ADD DONATION LINK INPUT TO PROFILES */
function cp_donation_link( $contactmethods ) {
	$contactmethods[ 'donation_url' ] = 'Donation URL';
	return $contactmethods;
}
add_filter( 'user_contactmethods', 'cp_donation_link' );