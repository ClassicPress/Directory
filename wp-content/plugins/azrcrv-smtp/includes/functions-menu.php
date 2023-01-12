<?php
/*
	tab output on settings page
*/

/**
 * Declare the Namespace.
 *
 * @since 1.0.0
 */
namespace azurecurve\SMTP;

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 */
function add_plugin_action_link( $links, $file ) {

	$this_plugin = PLUGIN_SLUG . '/' . PLUGIN_SLUG . '.php';

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . esc_url_raw( admin_url( 'admin.php?page=' . PLUGIN_HYPHEN ) ) . '"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="' . DEVELOPER_NAME . '" />' . esc_html__( 'Settings', 'azrcrv-smtp' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 */
function create_admin_menu() {

	add_submenu_page(
		'azrcrv-plugin-menu',
		PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-smtp' ),
		PLUGIN_NAME,
		'manage_options',
		PLUGIN_HYPHEN,
		__NAMESPACE__ . '\\display_options'
	);
}
