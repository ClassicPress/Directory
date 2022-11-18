<?php

/**
 * Plugin Name: Real Nonce
 * Version: 0.1
 * Description: A simple but genuine NONCE system for ClassicPress. Forked, with bug fixes and security enhancements, from wp-simple-nonce by Cal Evans.
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Plugin URI: https://timkaye.org
 */

/* INCLUDE REQUIRED FILES */
require_once __DIR__ . '/inc/real-nonce-class.php';


/* SET NONCE */
function cp_set_nonce( $name ) {
	return KTS_Real_Nonce::create_nonce( $name );
}


/* CREATE NONCE FIELD */
function cp_set_nonce_field( $name ) {
	return KTS_Real_Nonce::create_nonce_field( $name );
}


/* GET NONCE */
function cp_get_nonce( $name ) {
	return KTS_Real_Nonce::fetch_nonce( $name );
}


/* CHECK NONCE */
function cp_check_nonce( $name, $value ) {
	return KTS_Real_Nonce::check_nonce( $name, $value );
}


/* STORE NONCE IN DATABASE */
function cp_store_nonce( $name, $value ) {
	return KTS_Real_Nonce::store_nonce( $name, $value );
}


/* DELETE NONCE */
function cp_delete_nonce( $name ) {
	return KTS_Real_Nonce::delete_nonce( $name );
}


/* SHORTCODE TO GENERATE NONCE AS STRING WITH SPACE BETWEEN NAME AND VALUE */
function kts_real_nonce_shortcode() {
	$real_nonce_array = KTS_Real_Nonce::create_nonce( $name = 'nonce' );
	return $real_nonce_array ? $real_nonce_array['name'] . ' ' . $real_nonce_array['value'] : '';
}
add_shortcode( 'real_nonce', 'kts_real_nonce_shortcode' );


/* ENSURE THAT EXPIRED NONCES ARE DELETED */
function kts_real_nonce_cleanup() {
	return KTS_Real_Nonce::clear_nonces();
}
add_action( 'real_nonce_cleanup', 'kts_real_nonce_cleanup' );

function kts_real_nonce_register_garbage_collection() {
	if ( ! wp_next_scheduled( 'real_nonce_cleanup' ) ) {
		wp_schedule_event( time(), 'hourly', 'real_nonce_cleanup' );
	}
}
register_activation_hook( __FILE__, 'kts_real_nonce_register_garbage_collection' );
