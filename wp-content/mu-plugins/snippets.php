<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Code Snippets
 * Plugin URI: https://directory.classicpress.net/
 * Description: Snippets of code for use with ClassicPress
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/
 
function kts_register_snippet_post_type() {

 	# Define labels
	$labels = array (
		'name' 				=> __( 'Snippets','Snippet', 'classicpress' ),
		'singular_name' 	=> __( 'Snippet', 'Snippet', 'classicpress' ),
		'name_admin_bar'	=> __( 'Snippets', 'classicpress' ),
		'add_new' 			=> __( 'Add new Snippet', 'classicpress' ),
		'add_new_item' 		=> __( 'Add new Snippet', 'classicpress' ),
		'edit_item' 		=> __( 'Edit Snippet', 'classicpress' ),
		'new_item' 			=> __( 'New Snippet', 'classicpress' ),
		'view_item' 		=> __( 'View Snippet', 'classicpress' ),
		'menu_name'			=> __( 'Snippets', 'classicpress' ),
		'all_items'			=> __( 'All Snippets', 'classicpress' ),
		'view_item'			=> __( 'View Snippet', 'classicpress' ),
		'edit_item'			=> __( 'Edit Snippet', 'classicpress' ),
		'update_item'		=> __( 'Update Snippet', 'classicpress' ),
		'search_items'		=> __( 'Search Snippet', 'classicpress' ),
		'not_found'			=> __( 'No Snippet found', 'classicpress' ),
		'not_found_in_trash'=> __( 'No Snippets found in Trash', 'classicpress' ),
	);

	# Define args
	$args = array (
		'labels' 				=> $labels,
		'public' 				=> true,
		'show_in_nav_menus'		=> false,
		'menu_position'			=> 7,
		'menu_icon' 			=> 'dashicons-editor-code',
		'supports' 				=> array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'custom-fields' ),
		'can_export'			=> true,
		'rewrite'				=> array( 'slug' => 'snippets' ),
        'has_archive'			=> true,
        'exclude_from_search'	=> false,
        'publicly_queryable' 	=> true,
		'show_in_rest'			=> true,
		'rest_base'				=> 'snippets',
		'taxonomies'			=> array( 'post_tag' ),
	);

	register_post_type( 'snippet', $args );
}
add_action( 'init', 'kts_register_snippet_post_type' );
