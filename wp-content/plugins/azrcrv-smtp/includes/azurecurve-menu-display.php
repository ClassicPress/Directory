<?php
/**
 *  Menu Version 3.0
 */
// add actions.
add_action( 'admin_menu', 'azrcrv_add_azurecurve_menu' );

/**
 * Add azurecurve menu.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'azrcrv_add_azurecurve_menu' ) ) {
	function azrcrv_add_azurecurve_menu() {
		global $admin_page_hooks;

		if ( empty( $admin_page_hooks['azrcrv-plugin-menu'] ) ) {
			add_menu_page(
				'azurecurve Plugins',
				'azurecurve',
				'manage_options',
				'azrcrv-plugin-menu',
				'azrcrv_display_azurecurve_menu',
				esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) )
			);
			add_submenu_page(
				'azrcrv-plugin-menu',
				'Plugins',
				'Plugins',
				'manage_options',
				'azrcrv-plugin-menu',
				'azrcrv_display_azurecurve_menu'
			);
		}
	}
}

/**
 * Display plugin menu.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'azrcrv_display_azurecurve_menu' ) ) {
	function azrcrv_display_azurecurve_menu() {

		echo '<div id="azrcrv-general" class="wrap">';

			echo '<h1>';
				echo '<a href="' . azurecurve\SMTP\DEVELOPER_URL_RAW . '"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
				echo azurecurve\SMTP\DEVELOPER_SHORTNAME . ' ClassicPress Plugins';
			echo '</h1>';

			$plugin_array = get_option( 'azrcrv-plugin-menu' );

			$tab_active_label = esc_html__( 'Active Plugins', 'azrcrv-smtp' );
			$active_plugins   = '';

		foreach ( $plugin_array as $plugin_name => $plugin_details ) {

			$alternative_color = '';

			if ( isset( $plugin_details['premium'] ) and $plugin_details['premium'] == 1 ) {
				$alternative_color = 'premium-';
			}

			if ( isset( $plugin_details['bright'] ) and $plugin_details['bright'] == 1 ) {
				$alternative_color = 'bright-';
			}

			if ( isset( $plugin_details['retired'] ) and $plugin_details['retired'] == 1 ) {
				$alternative_color = 'grey-';
			}

			if ( is_plugin_active( $plugin_details['plugin_link'] ) ) {
				$active_plugins .= '<a href="' . esc_url_raw( $plugin_details['admin_URL'] ) . '" class="azrcrv-' . esc_html( $alternative_color ) . 'plugin-index">' . esc_html( $plugin_name ) . '</a>';
			}
		}

			$tab_active = '
				<table class="form-table azrcrv-settings">

					<tr>
					
						<td scope="row" colspan=2>
						
							<p>' .
								sprintf( esc_html__( '%1$s was one of the first plugin developers to start developing for ClassicPress; all plugins are available from %2$s and are integrated with the %3$s plugin for fully integrated, no hassle, updates.', 'azrcrv-smtp' ), '<strong>' . azurecurve\SMTP\DEVELOPER_NAME . '</strong>', azurecurve\SMTP\DEVELOPER_URL, '<a href="https://directory.classicpress.net/plugins/update-manager/">Update Manager</a>' )
							. '</p>
							<p>' .
								sprintf( esc_html__( 'The %s plugins active on your site are:', 'azrcrv-smtp' ), '<strong>' . azurecurve\SMTP\DEVELOPER_NAME . '</strong>' )
							. '</p>
						
						</td>
					
					</tr>
					
					<tr>
					
						<td scope="row" colspan=2>
						
							' . $active_plugins . '
							
						</td>

					</tr>
					
				</table>';

			$tab_other_label = esc_html__( 'Other Available Plugins', 'azrcrv-smtp' );
			$other_plugins   = '';

			$countofplugins = 0;

		foreach ( $plugin_array as $plugin_name => $plugin_details ) {

			if ( $plugin_details['retired'] == 0 ) {

				$alternative_color = '';
				if ( isset( $plugin_details['bright'] ) and $plugin_details['bright'] == 1 ) {
					$alternative_color = 'bright-';
				}

				if ( isset( $plugin_details['premium'] ) and $plugin_details['premium'] == 1 ) {
					$alternative_color = 'premium-';
				}

				if ( ! is_plugin_active( $plugin_details['plugin_link'] ) ) {
					$other_plugins  .= '<a href="' . esc_url_raw( $plugin_details['dev_URL'] ) . '" class="azrcrv-' . esc_html( $alternative_color ) . 'plugin-index">' . esc_html( $plugin_name ) . '</a>';
					$countofplugins += 1;
				}
			}
		}

		if ( $countofplugins == 0 ) {
			$other_plugins .= sprintf( esc_html__( 'Congratulations! You\'re using all of the %s plugins.', 'azrcrv-smtp' ), '<strong>' . azurecurve\SMTP\DEVELOPER_NAME . '</strong>' );
		}

			$tab_other = '
				<table class="form-table azrcrv-settings">

					<tr>
					
						<td scope="row" colspan=2>
						
							
							<p>' .
								sprintf( esc_html__( 'The other plugins available from %s are:', 'azrcrv-smtp' ), '<strong>' . azurecurve\SMTP\DEVELOPER_NAME . '</strong>' )
							. '</p>
						
						</td>
					
					</tr>
					
					<tr>
					
						<td scope="row" colspan=2>
						
							' . $other_plugins . '
							
						</td>

					</tr>
					
				</table>';

			require_once 'azurecurve-menu-tabs-output.php';

		echo '</div>';

	}
}
