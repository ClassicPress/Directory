<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Themes
 * Plugin URI: https://directory.classicpress.net/
 * Description: ClassicPress Themes
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/
 
function kts_register_theme_post_type() {

 	# Define labels
	$labels = array (
		'name' 				=> __( 'Themes','Theme', 'classicpress' ),
		'singular_name' 	=> __( 'Theme', 'Theme', 'classicpress' ),
		'name_admin_bar'	=> __( 'Themes', 'classicpress' ),
		'add_new' 			=> __( 'Add new Theme', 'classicpress' ),
		'add_new_item' 		=> __( 'Add new Theme', 'classicpress' ),
		'edit_item' 		=> __( 'Edit Theme', 'classicpress' ),
		'new_item' 			=> __( 'New Theme', 'classicpress' ),
		'view_item' 		=> __( 'View Theme', 'classicpress' ),
		'menu_name'			=> __( 'Themes', 'classicpress' ),
		'all_items'			=> __( 'All Themes', 'classicpress' ),
		'view_item'			=> __( 'View Theme', 'classicpress' ),
		'edit_item'			=> __( 'Edit Theme', 'classicpress' ),
		'update_item'		=> __( 'Update Theme', 'classicpress' ),
		'search_items'		=> __( 'Search Theme', 'classicpress' ),
		'not_found'			=> __( 'No Theme found', 'classicpress' ),
		'not_found_in_trash'=> __( 'No Themes found in Trash', 'classicpress' ),
	);

	# Define args
	$args = array (
		'labels' 				=> $labels,
		'public' 				=> true,
		'show_in_nav_menus'		=> false,
		'menu_position'			=> 3,
		'menu_icon' 			=> 'dashicons-format-image',
		'supports' 				=> array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'custom-fields' ),
		'can_export'			=> true,
		'rewrite'				=> array( 'slug' => 'themes' ),
        'has_archive'			=> true,
        'exclude_from_search'	=> false,
        'publicly_queryable' 	=> true,
		'show_in_rest'			=> true,
		'rest_base'				=> 'themes',
	);

	register_post_type( 'theme', $args );
}
add_action( 'init', 'kts_register_theme_post_type' );
