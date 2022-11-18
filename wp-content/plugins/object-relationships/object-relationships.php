<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Plugin Name: Object Relationships
 * Plugin URI: https://timkaye.org
 * Description: Enables relationships between objects (and associated meta data) to be stored in dedicated database tables
 * Version: 0.3.0
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
*/

/* INCLUDE REQUIRED FILES */
require_once __DIR__ . '/inc/database.php'; // creates custom database tables
require_once __DIR__ . '/inc/meta-functions.php'; // provides helper functions to manipulate meta data
require_once __DIR__ . '/inc/relationship-functions.php'; // provides helper functions for object relationships


/* FIRE HOOK FOR DATABASE TABLES CREATION */
register_activation_hook( __FILE__, 'kts_object_relationships_create_db' );


/* REGISTER META TABLE (TO ENABLE HELPER FUNCTIONS) */
function kts_register_kts_object_relationshipmeta_table() {
	global $wpdb;
	$wpdb->kts_object_relationshipmeta = $wpdb->prefix . 'kts_object_relationshipmeta';
}
add_action( 'init', 'kts_register_kts_object_relationshipmeta_table' );
