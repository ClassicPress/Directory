<?php
/*
	plugin images functions
*/

/**
 * Declare the Namespace.
 *
 * @since 1.0.0
 */
namespace azurecurve\SMTP;

/**
 * Custom plugin image path.
 *
 * @since 1.2.0
 */
function custom_image_path( $path ) {
	return plugin_dir_path( PLUGIN_FILE ) . 'assets/images';
}

/**
 * Custom plugin image url.
 *
 * @since 1.2.0
 */
function custom_image_url( $url ) {
	return esc_url_raw( plugin_dir_url( PLUGIN_FILE ) . 'assets/images' );
}
