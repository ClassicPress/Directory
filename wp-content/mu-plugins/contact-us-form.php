<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Contact Us Form
 * Plugin URI: https://directory.classicpress.net/
 * Description: Form to contact administrators of ClassicPress Directory with messages stored as custom post type
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/
 
function kts_register_message_post_type() {

 	# Define labels
	$labels = array (
		'name' 				=> __( 'Messages','Message', 'classicpress' ),
		'singular_name' 	=> __( 'Message', 'Message', 'classicpress' ),
		'name_admin_bar'	=> __( 'Messages', 'classicpress' ),
		'add_new' 			=> __( 'Add new Message', 'classicpress' ),
		'add_new_item' 		=> __( 'Add new Message', 'classicpress' ),
		'edit_item' 		=> __( 'Edit Message', 'classicpress' ),
		'new_item' 			=> __( 'New Message', 'classicpress' ),
		'view_item' 		=> __( 'View Message', 'classicpress' ),
		'menu_name'			=> __( 'Messages', 'classicpress' ),
		'all_items'			=> __( 'All Messages', 'classicpress' ),
		'view_item'			=> __( 'View Message', 'classicpress' ),
		'edit_item'			=> __( 'Edit Message', 'classicpress' ),
		'update_item'		=> __( 'Update Message', 'classicpress' ),
		'search_items'		=> __( 'Search Message', 'classicpress' ),
		'not_found'			=> __( 'No Message found', 'classicpress' ),
		'not_found_in_trash'=> __( 'No Messages found in Trash', 'classicpress' )
	);

	# Define args
	$args = array (
		'labels' 				=> $labels,
    	'description'			=> __( 'Provides a post type for messages sent from the Contact Us form', 'classicpress' ),
    	'hierarchical'			=> false,
		'public' 				=> true,
		'show_in_menu'			=> true,
		'show_in_nav_menus'		=> false,
		'menu_position'			=> 9,
		'menu_icon' 			=> 'dashicons-images-alt2',
		'supports' 				=> array( 'title', 'editor', 'author' ),
		'can_export'			=> true,
        'has_archive'			=> false,
        'exclude_from_search'	=> true,
        'publicly_queryable' 	=> false,
		'show_in_rest'			=> false,
	);

	register_post_type( 'message', $args );
}
add_action( 'init', 'kts_register_message_post_type' );


/* DISPLAY CONTACT US FORM */
function kts_render_contact_us_form() {
	ob_start();

	if ( ! is_user_logged_in() ) {
		_e( '<p>You need to <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">log in</a> to be able to use the form on this page.</p>', 'classicpress' );
	}
	else {
		$cp_nonce = cp_set_nonce( 'contact_nonce' );

		# Error Messages
		if ( isset( $_GET['notification'] ) ) {
			if ( $_GET['notification'] === 'nonce-wrong' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You have already submitted this form.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-subject' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must give your message a subject!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-message' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must write some content for your message!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'not-sent' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'There was a problem submitting the form. Your message has not been sent.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'success' ) {
				echo '<div class="success-message" role="polite"><p>' . __( 'Your message has been submitted successfully. If appropriate, we will respond in due course.', 'classicpress' ) . '</p></div>';
			}
		}
	?>

		<form id="contact-form" method="post" autocomplete="off">

			<label for="subject"><?php _e( 'Subject', 'classicpress' ); ?></label>
			<input id="subject" name="subject" type="text" required>

			<label for="message"><?php _e( 'Message', 'classicpress' ); ?></label>
			<textarea id="message" name="message" required></textarea>

			<input type="hidden" name="cp-contact-name" value="<?php echo $cp_nonce['name']; ?>">
			<input type="hidden" name="cp-contact-value" value="<?php echo $cp_nonce['value']; ?>">
			<button id="submit-btn" type="submit" enterkeyhint="send">Submit</button>
			<button type="reset" enterkeyhint="go">Clear</button>

		</form>
	<?php
	}
		
	echo ob_get_clean();
}


/* PROCESS CONTACT US FORM */
function kts_contact_form_redirect() {

	# Check that user is logged in
	if ( ! is_user_logged_in() ) {
		return;
	}

	# Check for nonce
	if ( empty( $_POST['cp-contact-name'] ) ) {
		return;
	}

	# If nonce is wrong
	$nonce = cp_check_nonce( $_POST['cp-contact-name'], $_POST['cp-contact-value'] );
	$referer = remove_query_arg( 'notification', wp_get_referer() );

	if ( $nonce === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=nonce-wrong' ) );
		exit;
	}

	# Check that message has been given a subject
	if ( empty( $_POST['subject'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-subject' ) );
		exit;
	}

	# Check that the message has content
	if ( empty( $_POST['message'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-message' ) );
		exit;
	}

	# Submit form as a post type
	$post_info = array(
		'post_title'	=> sanitize_text_field( wp_unslash( $_POST['subject'] ) ),
		'post_content'	=> sanitize_textarea_field( wp_unslash( $_POST['message'] ) ),
		'post_type'		=> 'message',
		'post_status'	=> 'publish',
		'post_author'	=> get_current_user_id(),
	);

	# Save post
	$post_id = wp_insert_post( $post_info );

	# Generate an error message if there is a problem with submitting the form
	if ( $post_id === 0 || is_wp_error( $post_id ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=not-sent' ) );
		exit;
	}

	# Generate success message
	wp_safe_redirect( esc_url_raw( $referer . '?notification=success' ) );
	exit;
}
add_action( 'template_redirect', 'kts_contact_form_redirect' );


/* EMAIL ALL SITE ADMINISTRATORS WHEN MESSAGE SUBMITTED */
function kts_email_on_message_submitted( $new_status, $old_status, $post ) {

	# Bail if not message CPT
    if ( 'message' !== $post->post_type ) {
        return;
	}

	# Bail if an update or autosave
    if ( 'publish' !== $new_status || 'publish' === $old_status ) {
        return;
	}

	# Get email addresses of administrators and send
	$users = get_users( array( 'role' => 'administrator' ) );
	foreach( $users as $user ) {
		wp_mail( $user->user_email, esc_html( $post->post_title ), esc_html( $post->post_content ) );
	}
}
add_action( 'transition_post_status', 'kts_email_on_message_submitted', 10, 3 );
