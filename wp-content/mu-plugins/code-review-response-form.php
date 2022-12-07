<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Code Review Response Form
 * Plugin URI: https://directory.classicpress.net/
 * Description: Form to enable developers to respond to code reviews
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/

function kts_render_review_response_form() {
	$user_id = get_current_user_id();
	$two_fa = kts_2fa_enabled( $user_id );
	ob_start();

	if ( ! is_user_logged_in() ) {
		_e( '<p>You need to <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">log in</a> to be able to use the form on this page.</p>', 'classicpress' );
	}

	elseif ( ! current_user_can( 'edit_posts' ) ) { // Not a Contributor or above
		if ( $two_fa === false ) {
			_e( '<p>You must be approved before you can submit software for review.</p>', 'classicpress' );
			_e( '<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="' . esc_url( get_edit_profile_url( get_current_user_id() ) ) . '#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>.</p>', 'classicpress' );
			_e( '<p>You will receive an email when you have been approved.</p>', 'classicpress' );
		}
		else {
			$two_fa_method = get_user_meta( $user_id, '_two_factor_provider', true );
			if ( $two_fa_method === 'Two_Factor_Dummy' ) {
				_e( '<p>You must be approved before you can submit software for review.</p>', 'classicpress' );
				_e( '<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="' . esc_url( get_edit_profile_url( get_current_user_id() ) ) . '#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>. Enabling the dummy method is not acceptable for this purpose.</p>', 'classicpress' );
				_e( '<p>You will receive an email when you have been approved.</p>', 'classicpress' );
			}
			else {
				_e( '<p>Your account is pending review. You will receive an email when you have been approved.</p>', 'classicpress' );
			}
		}
	}

	else { // Contributor role or above
		_e( '<p><strong>IMPORTANT:</strong> Before completing the form, please ensure that you have released a more recent version of your software on GitHub than you submitted previously. The URL to the latest release is a required field in this form, and the form will be rejected if the version you link to is not later than the version you last submitted for review.</p>', 'classicpress' );

		$cp_nonce = cp_set_nonce( 'response_nonce' );

		# Error Messages
		if ( isset( $_GET['notification'] ) ) {
			if ( $_GET['notification'] === 'nonce-wrong' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You have already submitted this form.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-name' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a name for your software!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-software-type' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify the type of software that you are submitting!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-software-type' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The software type you have specified is not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-software-id' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must specify the software ID!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-software-id' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The software ID you have specified is not recognized!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'id-type-mismatch' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The software ID you have specified does not match the software type!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'wrong-author' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'Only the software author may respond to a code review!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-comments' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide some comments!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-download-link' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a download link!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'invalid-download-link' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a valid URL for the download link!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'invalid-github' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'You must provide a URL for a GitHub repository!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-later-link' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'The download link you provide must be to a later release than your previous submission!', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'no-comment' ) {
				echo '<div class="error-message" role="alert"><p>' . __( 'There was a problem submitting this reponse. Your comments have not been sent.', 'classicpress' ) . '</p></div>';
			}
			elseif ( $_GET['notification'] === 'success' ) {
				echo '<div class="success-message" role="polite"><p>' . __( 'Your comments have been submitted successfully. Your reviewer will respond by email.', 'classicpress' ) . '</p></div>';
			}
		}

		# Get info from query
		if ( isset( $_REQUEST['reviewed-item-name'] ) ) {
			$precompiled_name = 'value="' . esc_attr( wp_unslash( $_REQUEST['reviewed-item-name'] ) ) . '"';
		} else {
			$precompiled_name = '';
		}
		if ( isset( $_REQUEST['reviewed-item-id'] ) ) {
			$precompiled_id = 'value="' . (int) wp_unslash( $_REQUEST['reviewed-item-id'] ) . '"';
		} else {
			$precompiled_id = '';
		}
		if ( isset( $_REQUEST['reviewed-item-type'] ) && in_array( $_REQUEST['reviewed-item-type'], ['plugin', 'theme', 'snippet'] ) ) {
			$precompiled_type = wp_unslash( $_REQUEST['reviewed-item-type'] );
		} else {
			$precompiled_type = '';
		}
		?>

		<section>
		<div class="section-header">
			<h2><?php _e( 'Your Response', 'classicpress' ); ?></h2>
			<small class="required-text"><?php _e( 'All fields are required.', 'classicpress' ); ?></small>
		</div>

		<form id="review-code-response-form" method="post" autocomplete="off">

			<label for="name"><?php _e( 'Name of Software', 'classicpress' ); ?></label>
			<input id="name" name="name" type="text" <?php echo $precompiled_name; ?> required>

			<fieldset>
				<legend><?php _e( 'Select Type of Software', 'classicpress' ); ?></legend>
				<div class="clear"></div>
				<label for="plugin">
					<input id="plugin" class="mgr-lg" name="software-type" type="radio" value="plugin" <?php checked( $precompiled_type, 'plugin' ) ?> required>
					Plugin
				</label>
				<br>
				<label for="theme">
					<input id="theme" class="mgr-lg" name="software-type" type="radio" value="theme" <?php checked( $precompiled_type, 'theme' ) ?> required>
					Theme
				</label>
				<br>
				<label for="snippet">
					<input id="snippet" class="mgr-lg" name="software-type" type="radio" value="snippet" <?php checked( $precompiled_type, 'snippet' ) ?> required>
					Code Snippet
				</label>
			</fieldset>

			<label for="software_id"><?php _e( 'Software ID (shown on the review)', 'classicpress' ); ?></label>
			<input id="software_id" name="software_id" type="number" min="1" <?php echo $precompiled_id; ?> required>

			<label for="comments"><?php _e( 'Comments in response to code review', 'classicpress' ); ?></label>
			<textarea id="comments" name="comments" required></textarea>

			<label for="download_link"><?php _e( 'Latest Download Link (full URL including https://)', 'classicpress' ); ?></label>
			<div id="download_link_hint">This link must be to a release later than that linked to in the previous submission</div>
			<input id="download_link" name="download_link" type="url" aria-describedby="download_link_hint" required>

			<input type="hidden" name="cp-response-name" value="<?php echo $cp_nonce['name']; ?>">
			<input type="hidden" name="cp-response-value" value="<?php echo $cp_nonce['value']; ?>">
			<button id="submit-btn" type="submit" enterkeyhint="send">Submit</button>
			<button type="reset" enterkeyhint="go">Clear</button>

		</form>
		</section>
	<?php
	}		
	echo ob_get_clean();
}


/* PROCESS RESPONSE FORM */
function kts_review_response_form_redirect() {

	# Check that user has the capability to submit software for review
	if ( ! current_user_can( 'edit_posts' ) ) { // Contributor role or above
		return;
	}

	# Check for nonce
	if ( empty( $_POST['cp-response-name'] ) ) {
		return;
	}

	# If nonce is wrong
	$nonce = cp_check_nonce( $_POST['cp-response-name'], $_POST['cp-response-value'] );
	$referer = remove_query_arg( [ 'notification', 'reviewed-item-name', 'reviewed-item-id', 'reviewed-item-type' ] , wp_get_referer() );

	if ( $nonce === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=nonce-wrong' ) );
		exit;
	}

	# Check that name of software has been provided
	if ( empty( $_POST['name'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-name' ) );
		exit;
	}

	# Get correct type of software
	$post_type = sanitize_text_field( wp_unslash( $_POST['software-type'] ) );

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

	# Check that software ID has been provided
	if ( empty( $_POST['software_id'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-software-id' ) );
		exit;
	}

	# Check that software ID is a positive integer
	if ( filter_var( $_POST['software_id'], FILTER_VALIDATE_INT ) === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-software-id' ) );
		exit;
	}

	# Check that software ID matches post type
	$software_id = absint( $_POST['software_id'] );
	$software_type = get_post_type( $software_id );
	if ( $software_type !== $post_type ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=id-type-mismatch' ) );
		exit;
	}

	# Check that the person submitting this form is the software author
	$author_id = get_post_field( 'post_author', $software_id );
	$user_id = get_current_user_id();
	if ( $user_id !== (int) $author_id ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=wrong-author' ) );
		exit;
	}

	# Check that comments have been provided
	if ( empty( $_POST['comments'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-comments' ) );
		exit;
	}

	# Check that URL to download software has been provided
	if ( empty( $_POST['download_link'] ) ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-download-link' ) );
		exit;
	}

	# Check that the download link is a valid URL
	if ( filter_var( $_POST['download_link'], FILTER_VALIDATE_URL ) === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=invalid-download-link' ) );
		exit;
	}

	# Check that the download link corresponds with the Update URI 
	$user_id = get_current_user_id();
	$github_username = get_user_meta( $user_id, 'github_username', true );
	$update_uri = esc_url_raw( 'https://github.com/' . $github_username . '/' );
	if ( stripos( $_POST['download_link'], $update_uri ) !== 0 ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=invalid-github' ) );
		exit;
	}

	# Check that URL to download software is to a later release	
	$download_link = get_post_meta( $software_id, 'download_link', true );
	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $download_link, $orig_matches );
	$orig_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $orig_matches[0] );

	$new_link = esc_url_raw( wp_unslash( $_POST['download_link'] ) );
	preg_match( '~releases\/download\/v?[\s\S]+?\/~', $new_link, $new_matches );
	$new_version = str_replace( ['releases/download/v', 'releases/download/', '/'], '', $new_matches[0] );

	if ( version_compare( $new_version, $orig_version ) !== 1 ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-later-link' ) );
		exit;
	}
 
	# Get reviews related to this software
	$review_ids = kts_get_object_relationship_ids( $software_id, $post_type, 'review' );

	# Update download link and current version
	update_post_meta( $software_id, 'download_link', $new_link );
	update_post_meta( $software_id, 'current_version', $new_version );

	# Get comments
	$comments = sanitize_textarea_field( wp_unslash( $_POST['comments'] ) );

	# Add comment
	$commentdata = array(
		'comment_approved'	=> 1,
		'comment_content'	=> $comments,
		'comment_post_ID'	=> end( $review_ids ), // get latest associated review
		'user_id'			=> $user_id,
	);
	$comment_id = wp_insert_comment( $commentdata );

	# Generate an error message if there is a problem with submitting the form
	if ( $comment_id === false ) {
		wp_safe_redirect( esc_url_raw( $referer . '?notification=no-comment' ) );
		exit;
	}

	# Generate success message
	wp_safe_redirect( esc_url_raw( $referer . '?notification=success' ) );
	exit;
}
add_action( 'template_redirect', 'kts_review_response_form_redirect' );


/* EMAIL ALL SITE ADMINISTRATORS WHEN RESPONSE SUBMITTED */
function kts_email_on_response_submitted( $comment_ID, $comment ) {

	# Bail if not a comment on a review CPT
	$post = get_post( $comment->comment_post_ID );
	if ( $post->post_type !== 'review' ) {
		return;
	}

	# Get details of software
	$software_id = get_post_meta( $comment->comment_post_ID, 'post-id', true );
	$software = get_post( $software_id );
	$software_author = get_the_author_meta( 'display_name', (int) $software->post_author );

	# Get details of message	
	$subject = _e( esc_html( $software_author ) . ' has responded to code review ID #' . absint( $comment->comment_post_ID ), 'classicpress' );

	$review_admin_url = admin_url( 'post.php?post=' . $comment->comment_post_ID ) . '&action=edit';

	$message = _e( 'This response and the original review, titled <strong>' . esc_html( $post->post_title ) . '</strong>, may be found on <a href="' . esc_url( $review_admin_url ) . '">this admin page</a>. The updated software download link is ' . make_clickable( esc_url( get_post_meta( $software_id, 'download_link', true ) ) ) . '.', 'classicpress' );

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	# Send email to administrators and editors
	$users = get_users( [ 'role__in' => [ 'administrator', 'editor' ] ] );
	foreach( $users as $user ) {
		wp_mail( $user->user_email, $subject, $message, $headers );
	}
}
add_action( 'wp_insert_comment', 'kts_email_on_response_submitted', 10, 2 );
