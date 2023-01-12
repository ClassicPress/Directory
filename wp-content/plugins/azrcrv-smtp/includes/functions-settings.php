<?php
/*
	tab output on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\SMTP;

/**
 * Declare PHPMailer Namespace.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Get options including defaults.
 *
 * @since 1.2.0
 */
function get_option_with_defaults( $option_name ) {

	$defaults = array(
		'smtp-host'               => '',
		'smtp-encryption-type'    => 'ssl',
		'smtp-port'               => 465,
		'smtp-username'           => '',
		'smtp-password'           => '',
		'allow-no-authentication' => 0,
		'from-email-address'      => '',
		'from-email-name'         => '',
		'test-email-address'      => '',
		'test-email-subject'      => '',
		'test-email-message'      => '',
	);

	$options = get_option( $option_name, $defaults );

	$options = wp_parse_args( $options, $defaults );

	return $options;

}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 */
function display_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-smtp' ) );
	}

	// Retrieve plugin configuration options from database.
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	echo '<div id="' . esc_attr( PLUGIN_HYPHEN ) . '-general" class="wrap">';

		echo '<h1>';
			echo '<a href="' . esc_url_raw( DEVELOPER_URL_RAW ) . esc_attr( PLUGIN_SHORT_SLUG ) . '/"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="' . DEVELOPER_NAME . '" /></a>';
			echo esc_html( get_admin_page_title() );
		echo '</h1>';

	if ( $options['smtp-host'] === '' && get_option( 'azrcrv-smtp-maybe', false ) !== false ) {
		// phpcs:ignore. ?>
			<div class="notice notice-info is-dismissible azrcrv-smtp-import-dismiss" data-nonce="<?php echo wp_create_nonce( PLUGIN_UNDERSCORE . '_import_dismiss_nonce' ); ?>">
				<p><strong>
				<?php
				// Display notice about imported settings
				$url = remove_query_arg( 'page' );
				$url = add_query_arg(
					array(
						'action'                   => 'azrcrv_smtp_import_options',
						'azrcrv_smtp_import_nonce' => wp_create_nonce( 'azrcrv_smtp_import_nonce' ),
					),
					$url
				);
					   esc_html_e( 'Found Easy WP SMTP settings that can be imported.', 'azrcrv-smtp' );
					   echo '<br>';
					   echo '<a href="' . esc_url_raw( $url ) . '">';
					   esc_html_e( 'Import settings', 'azrcrv-smtp' );
					   echo '</a>';
				?>
				</strong></p>
			</div>
			<?php
	}
	// phpcs:ignore.
	if ( isset( $_GET['settings-updated'] ) ) {
		?>
			<div class="notice notice-success is-dismissible">
				<p><strong>
			<?php
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				esc_html_e( 'Settings have been saved.', 'azrcrv-smtp' );
			?>
				</strong></p>
			</div>
		<?php
			// phpcs:ignore.
			} elseif ( isset( $_GET['test-email'] ) and $_GET['status'] == 'sent' ) { ?>
			<div class="notice notice-info is-dismissible">
				<p><strong>
				<?php
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					esc_html_e( 'Test email has been sent successfully.', 'azrcrv-smtp' );
				?>
				</strong></p>
			</div>
		<?php
			// phpcs:ignore.
			} elseif ( isset( $_GET['test-email'] ) and $_GET['status'] == 'failed' ) { ?>
			<div class="notice notice-error is-dismissible">
				<p>
					<strong>
					<?php
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						esc_html_e( 'Error sending test email:', 'azrcrv-smtp' );
					?>
					</strong>
					<?php
					$test_result = get_option( 'azrcrv-smtp-test' );
					foreach ( $test_result as $error ) {
						echo '<br />' . wp_kses( $error, array ( 'br' => array(), 'hr' => array() ) );
					}
					?>
				</p>
			</div>
			<?php
		}

		require_once 'tab-settings.php';
		require_once 'tab-test-email.php';
		require_once 'tab-instructions.php';
		require_once 'tab-other-plugins.php';
		require_once 'tabs-output.php';
		?>
		
	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 */
function save_options() {
	// Check that user has proper security level.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-smtp' ) );
	}
	// Check that nonce field created in configuration form is present.
	if ( ! empty( $_POST ) && check_admin_referer( PLUGIN_HYPHEN, PLUGIN_HYPHEN . '-nonce' ) ) {

		// Retrieve original plugin options array
		$options = get_option( 'azrcrv-smtp' );

		$option_name = 'smtp-host';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'smtp-encryption-type';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'smtp-port';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( intval( $_POST[ $option_name ] ) );
		}

		$option_name = 'smtp-username';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'smtp-password';
		if ( isset( $_POST[ $option_name ] ) ) {
			if ( $_POST[ $option_name ] != '#ProtectedPassword#' ) {
				$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
			}
		}

		$option_name = 'allow-no-authentication';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = 1;
		} else {
			$options[ $option_name ] = 0;
		}

		$option_name = 'from-email-address';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_email( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'from-email-name';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		// Store updated options array to database
		update_option( 'azrcrv-smtp', $options );

		// Redirect the page to the configuration form that was processed
		wp_safe_redirect( add_query_arg( 'page', 'azrcrv-smtp&settings-updated', admin_url( 'admin.php' ) ) );
		exit;
	}
}

// Handle click on import
function import_options() {

	// phpcs:ignore.
	if ( ! current_user_can( 'manage_options' ) || ! isset( $_REQUEST[ PLUGIN_UNDERSCORE . '_import_nonce' ] ) || ! wp_verify_nonce( $_REQUEST[ PLUGIN_UNDERSCORE . '_import_nonce' ], PLUGIN_UNDERSCORE . '_import_nonce' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-smtp' ) );
	}

	update_option( PLUGIN_HYPHEN, get_option( PLUGIN_HYPHEN . '-maybe' ) );
	delete_option( PLUGIN_HYPHEN . '-maybe' );

	wp_safe_redirect( add_query_arg( 'page', 'azrcrv-smtp&settings-updated', admin_url( 'admin.php' ) ) );
	exit;

}

// Handle AJAX notice dismiss
function import_dismiss() {

	// phpcs:ignore.
	if ( ! wp_doing_ajax() || ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], PLUGIN_UNDERSCORE . '_import_dismiss_nonce' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-smtp' ) );
	}
	delete_option( PLUGIN_HYPHEN . '-maybe' );
	wp_send_json_success( array( 'Dismissed' => 'Yes' ) );
}
