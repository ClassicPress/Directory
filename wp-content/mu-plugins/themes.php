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
		'taxonomies'			=> array( 'post_tag', 'theme_categories' ),
	);

	register_post_type( 'theme', $args );
}
add_action( 'init', 'kts_register_theme_post_type' );

// Register custom taxonomy
function theme_categories() {

	# Define labels
	$labels = array(
		'name'                       => _x( 'Categories', 'Taxonomy General Name', 'classicpress' ),
		'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'classicpress' ),
		'menu_name'                  => __( 'Taxonomy', 'classicpress' ),
		'all_items'                  => __( 'All Items', 'classicpress' ),
		'parent_item'                => __( 'Parent Item', 'classicpress' ),
		'parent_item_colon'          => __( 'Parent Item:', 'classicpress' ),
		'new_item_name'              => __( 'New Item Name', 'classicpress' ),
		'add_new_item'               => __( 'Add New Item', 'classicpress' ),
		'edit_item'                  => __( 'Edit Item', 'classicpress' ),
		'update_item'                => __( 'Update Item', 'classicpress' ),
		'view_item'                  => __( 'View Item', 'classicpress' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'classicpress' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'classicpress' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'classicpress' ),
		'popular_items'              => __( 'Popular Items', 'classicpress' ),
		'search_items'               => __( 'Search Items', 'classicpress' ),
		'not_found'                  => __( 'Not Found', 'classicpress' ),
		'no_terms'                   => __( 'No items', 'classicpress' ),
		'items_list'                 => __( 'Items list', 'classicpress' ),
		'items_list_navigation'      => __( 'Items list navigation', 'classicpress' ),
	);
	
	$rewrite = array(
		'slug'                       => 'theme-category',
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
		'rewrite'                    => $rewrite,
		'show_in_rest'               => true,
	);
	
	register_taxonomy( 'theme_categories', array( 'theme' ), $args );

}
add_action( 'init', 'theme_categories', 0 );
