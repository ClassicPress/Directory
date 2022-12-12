<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Reviews
 * Plugin URI: https://directory.classicpress.net/
 * Description: A post type for code reviews on plugins and themes
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/
 
function kts_register_review_post_type() {

 	# Define labels
	$labels = array (
		'name' 				=> __( 'Reviews','Review', 'classicpress' ),
		'singular_name' 	=> __( 'Review', 'Review', 'classicpress' ),
		'name_admin_bar'	=> __( 'Reviews', 'classicpress' ),
		'add_new' 			=> __( 'Add new Review', 'classicpress' ),
		'add_new_item' 		=> __( 'Add new Review', 'classicpress' ),
		'edit_item' 		=> __( 'Edit Review', 'classicpress' ),
		'new_item' 			=> __( 'New Review', 'classicpress' ),
		'view_item' 		=> __( 'View Review', 'classicpress' ),
		'menu_name'			=> __( 'Reviews', 'classicpress' ),
		'all_items'			=> __( 'All Reviews', 'classicpress' ),
		'view_item'			=> __( 'View Review', 'classicpress' ),
		'edit_item'			=> __( 'Edit Review', 'classicpress' ),
		'update_item'		=> __( 'Update Review', 'classicpress' ),
		'search_items'		=> __( 'Search Review', 'classicpress' ),
		'not_found'			=> __( 'No Review found', 'classicpress' ),
		'not_found_in_trash'=> __( 'No Reviews found in Trash', 'classicpress' )
	);

	# Define args
	$args = array (
		'labels' 				=> $labels,
    	'description'			=> __( 'Provides a post type for reviews of ClassicPress plugins and themes', 'classicpress' ),
    	'hierarchical'			=> false,
		'public' 				=> true,
		'show_in_menu'			=> true,
		'show_in_nav_menus'		=> false,
		'menu_position'			=> 8,
		'menu_icon' 			=> 'dashicons-format-status',
		'supports' 				=> array( 'title', 'editor', 'excerpt', 'author', 'comments', 'custom-fields' ),
		'can_export'			=> true,
        'has_archive'			=> false,
        'exclude_from_search'	=> true,
        'publicly_queryable' 	=> false,
		'show_in_rest'			=> false,
	);

	register_post_type( 'review', $args );
}
add_action( 'init', 'kts_register_review_post_type' );


/* RE-PURPOSE EXCERPTS FOR PRIVATE COMMENTS IN REVIEWS */
function kts_rename_excerpt( $translation, $original ) {
	global $post, $pagenow;
	
	if ( in_array( $pagenow, [ 'post.php', 'post-new.php' ] ) && isset( $post ) && $post->post_type === 'review' ) {

		if ( 'Excerpt' == $original ) {
			return __( 'Private Comments (seen only by editors and administrators and not sent to the software developer)' );
		}
		elseif ( strpos( $original, 'Excerpts are optional hand-crafted summaries of your' ) !== false ) {
			return __( '' );
		}
		return $translation;
	}

	return $original;
}
add_filter( 'gettext', 'kts_rename_excerpt', 10, 2 );


/* ADD CUSTOM FIELDS TO REVIEW EDIT SCREEN */
function kts_add_review_custom_fields_meta_box( $post ) {
	add_meta_box(
		'software_info_meta_box', // id
		__( 'Summary of Software Reviewed' ), // title
		'kts_show_software_info_meta_box', // callback
		'review', // post type
		'side', // context
		'high' // priority
	);
}
add_action( 'add_meta_boxes_review', 'kts_add_review_custom_fields_meta_box' );

function kts_show_software_info_meta_box( $post ) {
	wp_nonce_field( 'software_info_meta_box_nonce', 'software_info_meta_box_nonce' );
	?>

	<label for="post-type"><strong>Software Type (plugin or theme)</strong></label>
	<input type="text" name="post-type" id="post-type" value="<?php echo esc_attr( get_post_meta( $post->ID, 'post-type', true ) ); ?>" required>

	<label for="post-id"><strong>Software ID</strong></label>
	<input type="text" name="post-id" id="post-id" value="<?php echo absint( get_post_meta( $post->ID, 'post-id', true ) ); ?>" required>

	<?php
}


/* ENABLE USE OF CPTs IN OBJECT RELATIONSHIPS */
function kts_add_cpts_to_object_relationships( $objects ) {
	$objects[] = 'plugin';
	$objects[] = 'theme';
	$objects[] = 'review';
	return $objects;
}
add_filter( 'recognized_relationship_objects', 'kts_add_cpts_to_object_relationships' );


/* PREVENT PUBLICATION OF REVIEW IF NOT READY, BUT ADD OBJECT RELATIONSHIP IF IT IS */
function kts_check_review_meta( $data, $postarr ) {

	# Only run on reviews
	if ( $data['post_type'] !== 'review' ) {
		return $data;
	}

	# Set flag for use later in function
	$flag = true;

	# Prevent review from being published if no content
	if ( empty( $data['post_content'] ) ) {
		if ( $data['post_status'] === 'publish' ) {
			$data['post_status'] = 'draft';
		}
	}

	# Prevent review from being published if no specified CPT type
	$new_post_type = isset( $_POST['post-type'] ) ? wp_unslash( $_POST['post-type'] ) : '';
	if ( empty( $new_post_type ) || ! in_array( $new_post_type, ['plugin', 'theme'] ) ) {
		if ( $data['post_status'] === 'publish' ) {
			$data['post_status'] = 'draft';
		}
		$flag = false;
	}

	# Prevent review from being published if no specified CPT ID
	$new_post_id = isset( $_POST['post-id'] ) ? absint( $_POST['post-id'] ) : '';
	if ( empty( $new_post_id ) ) {
		if ( $data['post_status'] === 'publish' ) {
			$data['post_status'] = 'draft';
		}
		$flag = false;
	}

	# Set object relationship with software reviewed
	if ( $flag === true ) {
		kts_add_object_relationship( $postarr['ID'], 'review', $new_post_type, $new_post_id );
	}

	return $data;
}
add_filter( 'wp_insert_post_data', 'kts_check_review_meta', 10, 2 );


/* SAVE META FIELDS */
function kts_save_review_meta_fields( $post_id, $post, $update ) {

	# Check if nonce is set
	if ( empty( $_POST['software_info_meta_box_nonce'] ) ) {
		return;
	}

	# Verify that nonce is valid
	if ( ! wp_verify_nonce( $_POST['software_info_meta_box_nonce'], 'software_info_meta_box_nonce' ) ) {
		return;
	}

	# Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	# Check permissions: must be an editor or administrator
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	# Get current values and update if appropriate
	$old_post_type = get_post_meta( $post->ID, 'post-type', true );
	$new_post_type = sanitize_text_field( wp_unslash( $_POST['post-type'] ) );
	if ( ! empty( $new_post_type ) && in_array( $new_post_type, ['plugin', 'theme'] ) && $new_post_type !== $old_post_type ) {
		update_post_meta( $post_id, 'post-type', $new_post_type );
	}

	$old_post_id = get_post_meta( $post->ID, 'post-id', true );
	$new_post_id = absint( $_POST['post-id'] );
	if ( ! empty( $new_post_id ) && $new_post_id !== $old_post_id ) {
		update_post_meta( $post_id, 'post-id', $new_post_id );
	}
}
add_action( 'save_post_review', 'kts_save_review_meta_fields', 10, 3 );


/* SEND EMAIL WHEN REVIEW PUBLISHED */
function kts_email_faculty_protocol( $new_status, $old_status, $post ) {

	# Bail if not published for the first time
    if ( 'publish' !== $new_status || 'publish' === $old_status ) {
        return;
	}

	# Bail if not a review
    if ( 'review' !== $post->post_type ) {
        return;
	}

	# Bail if the current user is not an editor or administrator
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	# Get ID of plugin or theme reviewed
	$software_ids = kts_get_object_relationship_ids( $post->ID, 'review', 'plugin' );
	if ( empty( $software_ids ) ) {
		$software_ids = kts_get_object_relationship_ids( $post->ID, 'review', 'theme' );
	}

	# Bail if not associated with software
	if ( empty( $software_ids ) ) {
		return;
	}

	# Retrieve info needed to send email
	$software = get_post( $software_ids[0] );
	$author_id = $software->post_author;
	$author = get_user_by( 'id', (int) $author_id );

	$subject = 'Review of ' . esc_html( $software->post_title ) . ': ' . esc_html( ucwords( $software->post_type ) ) . ' Submission ID #' . absint( $software_ids[0] );

	$message = esc_html( $post->post_content ) . '<p>Please do not respond to this review by email, as this email address is not monitored. If you need to modify your code to comply with the requirements of the review, you should use the <a href="' . esc_url( home_url( '/code-review-response-form/?reviewed-item-id=' . absint( $software_ids[0] ) . '&reviewed-item-name=' . urlencode( $software->post_title ) . '&reviewed-item-type=' . $software->post_type ) ) . '">Code Review Response Form</a>. If you simply wish to ask a question, you should do that on Slack or the <a href="https://forums.classicpress.net/">ClassicPress forums</a>.</p>';
	
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	wp_mail( $author->user_email, $subject, $message, $headers );
}
add_action( 'transition_post_status', 'kts_email_faculty_protocol', 10, 3 );
