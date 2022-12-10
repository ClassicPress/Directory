<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Redirects
 * Plugin URI: https://directory.classicpress.net/
 * Description: Redirects instead of 404s.
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 0.1.0
 */

/* AVOID 404s */
function kts_redirect_from_404() {

	# Bail if not a 404
	if ( ! is_404() ) {
		return;
	}

	$uri = $_SERVER['REQUEST_URI'];
	if ( strpos( $uri, '/themes/' ) !== false ) {
		wp_safe_redirect( esc_url_raw( home_url( '/themes/' ) ) );
	}
	else {
		wp_safe_redirect( esc_url_raw( home_url( '/plugins/' ) ) );
	}

}
add_action( 'template_redirect', 'kts_redirect_from_404' );
