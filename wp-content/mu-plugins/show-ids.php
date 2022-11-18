<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Show IDs
 * Description: Shows IDs on lists of posts, pages, categories, etc
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 1.0
 */

function kts_show_ids() {

	foreach( get_taxonomies() as $taxonomy ) {
		if ( isset( $taxonomy ) ) {
			add_action( 'manage_edit-' . $taxonomy . '_columns', 'kts_add_id_column' );
			add_filter( 'manage_' . $taxonomy . '_custom_column', 'kts_column_return_value', 100, 3 );
			add_filter( 'manage_edit-' . $taxonomy . '_sortable_columns', 'kts_add_id_column' );
		}
	}

	foreach( get_post_types() as $post_type ) {
		if ( isset( $post_type ) ) {
			add_action( 'manage_edit-' . $post_type . '_columns', 'kts_add_id_column' );
			add_filter( 'manage_' . $post_type . '_posts_custom_column', 'kts_column_value', 100, 3 );
			add_filter( 'manage_edit-' . $post_type . '_sortable_columns', 'kts_add_id_column' );
		}
	}

}
add_action( 'admin_init', 'kts_show_ids' );

function kts_show_ids_css() {
	echo "\n" . '<style>
	table.widefat th.column-tkshowid {
		width: 70px;
	}
	table.widefat td.column-tkshowid {
		word-wrap: normal;
	}
	</style>' . "\n";
}
add_action( 'admin_head', 'kts_show_ids_css' );


/* ADD ID COLUMN */
function kts_add_id_column( $cols ) {
	$cols['tkshowid'] = __( 'ID' );
	return $cols;
}
add_action( 'manage_users_columns', 'kts_add_id_column', 100 );
add_filter( 'manage_users_sortable_columns', 'kts_add_id_column' );
add_action( 'manage_edit-comments_columns', 'kts_add_id_column' );
add_filter( 'manage_edit-comments_sortable_columns', 'kts_add_id_column' );
add_filter( 'manage_media_columns', 'kts_add_id_column' );
add_filter( 'manage_link-manager_columns', 'kts_add_id_column' );
add_action( 'manage_edit-link-categories_columns', 'kts_add_id_column' );


/* ECHO ID FOR COLUMN */
function kts_column_value( $column_name, $id ) {
	if ( $column_name === 'tkshowid' ) {
		echo $id;
	}
}
add_action( 'manage_comments_custom_column', 'kts_column_value', 100, 2 );
add_action( 'manage_media_custom_column', 'kts_column_value', 10, 2 );
add_action( 'manage_link_custom_column', 'kts_column_value', 10, 2 );


/* RETURN ID FOR COLUMN */
function kts_column_return_value( $value, $column_name, $id ) {
	if ( $column_name === 'tkshowid' ) {
		$value = $id;
	}
	return $value;
}
add_filter( 'manage_users_custom_column', 'kts_column_return_value', 100, 3 );
add_filter( 'manage_link_categories_custom_column', 'kts_column_return_value', 100, 3 );
