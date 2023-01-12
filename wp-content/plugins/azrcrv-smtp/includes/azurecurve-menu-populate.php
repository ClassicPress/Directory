<?php
/**
 *  Menu Version 3.0
 */


/**
 * Declare the Namespace.
 */
namespace azurecurve\SMTP;

/**
 *  Add action.
 */
add_action( 'admin_init', __NAMESPACE__ . '\\populate_azurecurve_menu' );

/**
 * Populate list of azurecurve plugins.
 *
 * @since 1.0.0
 */
function populate_azurecurve_menu() {

	require_once 'azurecurve-plugins.php';

	$plugin_menu = get_option( 'azrcrv-plugin-menu' );

	foreach ( $azurecurve_plugins as $plugin_name => $plugin_details ) {
		if ( isset( $plugin_menu[ $plugin_name ] ) ) {
			if ( strtotime( $plugin_menu[ $plugin_name ]['updated'] ) <= strtotime( $plugin_details['updated'] ) ) {
				$plugin_menu[ $plugin_name ] = $plugin_details;
			}
		} else {
			$plugin_menu[ $plugin_name ] = $plugin_details;
		}
	}

	ksort( $plugin_menu );

	update_option( 'azrcrv-plugin-menu', $plugin_menu );
}
