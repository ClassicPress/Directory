<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Custom Slug Taxonomies
 * Plugin URI: https://directory.classicpress.net/
 * Description: Creates custom taxonomies for the slugs of each software type.
 * Enables efficient and scalable checking for duplicates when new software is
 * submitted.
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 0.1.0
 */


/* REGISTER CUSTOM TAXONOMIES */
function kts_register_slug_taxonomies() {

	# https://tomjn.com/2018/03/16/utility-taxonomies/
	$plugin_args = array(
		'hierarchical'		=> false,
		'label'				=> __( 'Plugin Slugs', 'classicpress' ),
		'show_ui'			=> false,
		'public'			=> false,
		'show_admin_column'	=> false,
		'rewrite'			=> false,
	);
	register_taxonomy( 'plugin_slugs', array( 'plugin' ), $plugin_args );

	$theme_args = array(
		'hierarchical'		=> false,
		'label'				=> __( 'Theme Slugs', 'classicpress' ),
		'show_ui'			=> false,
		'public'			=> false,
		'show_admin_column'	=> false,
		'rewrite'			=> false,
	);
	register_taxonomy( 'theme_slugs', array( 'theme' ), $theme_args );

	$snippet_args = array(
		'hierarchical'		=> false,
		'label'				=> __( 'Snippet Slugs', 'classicpress' ),
		'show_ui'			=> false,
		'public'			=> false,
		'show_admin_column'	=> false,
		'rewrite'			=> false,
	);
	register_taxonomy( 'snippet_slugs', array( 'snippet' ), $snippet_args );

}
add_action( 'init', 'kts_register_slug_taxonomies' );
