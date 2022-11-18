<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Plugins
 * Plugin URI: https://directory.classicpress.net/
 * Description: ClassicPress Plugins
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/

function kts_register_plugin_post_type() {

 	# Define labels
	$labels = array (
		'name' 				=> __( 'Plugins','Plugin', 'classicpress' ),
		'singular_name' 	=> __( 'Plugin', 'Plugin', 'classicpress' ),
		'name_admin_bar'	=> __( 'Plugins', 'classicpress' ),
		'add_new' 			=> __( 'Add new Plugin', 'classicpress' ),
		'add_new_item' 		=> __( 'Add new Plugin', 'classicpress' ),
		'edit_item' 		=> __( 'Edit Plugin', 'classicpress' ),
		'new_item' 			=> __( 'New Plugin', 'classicpress' ),
		'view_item' 		=> __( 'View Plugin', 'classicpress' ),
		'menu_name'			=> __( 'Plugins', 'classicpress' ),
		'all_items'			=> __( 'All Plugins', 'classicpress' ),
		'view_item'			=> __( 'View Plugin', 'classicpress' ),
		'edit_item'			=> __( 'Edit Plugin', 'classicpress' ),
		'update_item'		=> __( 'Update Plugin', 'classicpress' ),
		'search_items'		=> __( 'Search Plugin', 'classicpress' ),
		'not_found'			=> __( 'No Plugin found', 'classicpress' ),
		'not_found_in_trash'=> __( 'No Plugins found in Trash', 'classicpress' ),
	);

	# Define args
	$args = array (
		'labels' 				=> $labels,
		'public' 				=> true,
		'show_in_nav_menus'		=> false,
		'menu_position'			=> 2,
		'menu_icon' 			=> 'dashicons-plugins-checked',
		'supports' 				=> array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'custom-fields' ),
		'can_export'			=> true,
		'rewrite'				=> array( 'slug' => 'plugins' ),
        'has_archive'			=> true,
        'exclude_from_search'	=> false,
        'publicly_queryable' 	=> true,
		'show_in_rest'			=> true,
		'rest_base'				=> 'plugins',
		'taxonomies'			=> array( 'category' ),
	);

	register_post_type( 'plugin', $args );
}
add_action( 'init', 'kts_register_plugin_post_type' );


/* DELETE ATTACHMENTS WHEN PLUGIN POST TYPE DELETED */
function kts_delete_when_plugin_deleted( $post_id ) {

	if ( get_post_type( $post_id ) !== 'plugin' ) {
		return;
	}

	$attachments = get_attached_media( '', $post_id ); // all mime types

	if ( ! empty( $attachments ) ) {
		foreach ( $attachments as $attachment ) {
			wp_delete_attachment( $attachment->ID, 'true' ); // force delete
		}
	}
}
//add_action( 'before_delete_post', 'kts_delete_when_plugin_deleted' );
