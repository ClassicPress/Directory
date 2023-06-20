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
 * Declare PHPMailer Namespace.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Function needed because wp_parse_args() is not recursive.
 *
 * @since 1.2.0
 */
function recursive_merge( &$a, $b ) {
	$a      = (array) $a;
	$b      = (array) $b;
	$result = $b;
	foreach ( $a as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) ) {
			$result[ $k ] = recursive_merge( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}
	return $result;
}

/**
 * Manage migratation from Easy WP SMTP on activation.
 *
 * @since 1.2.0
 */
function activate() {

	// Exit if the options are already in place
	$my_options = get_option( 'azrcrv-smtp', false );
	if ( $my_options !== false ) {
		return;
	}

	// Exit if swpsmtp_options are missing
	$swpsmtp_options = get_option( 'swpsmtp_options', false );
	if ( $swpsmtp_options === false ) {
		return;
	}

	// Fine... we have settings

	// Check that everything is defined in swpsmtp_options
	$swpsmtp_options_default = array(
		'from_email_field' => '',
		'from_name_field'  => '',
		'smtp_settings'    => array(
			'host'            => '',
			'type_encryption' => 'SSL',
			'port'            => '465',
			'username'        => '',
			'autentication'   => 0,
			'encrypt_pass'    => 0,
		),
	);
	$swpsmtp_options         = recursive_merge( $swpsmtp_options, $swpsmtp_options_default );

	// Exit if password encrypted and openssl missing (possible?)
	if ( $swpsmtp_options['smtp_settings']['encrypt_pass'] === 1 && ! extension_loaded( 'openssl' ) ) {
		return;
	}

	// phpcs:ignore.
	$raw_password = base64_decode( $swpsmtp_options['smtp_settings']['password'], true );

	// Exit on failed Base64 decode
	if ( $raw_password === false ) {
		return;
	}

	// Decrypt password
	if ( $swpsmtp_options['smtp_settings']['encrypt_pass'] === 1 ) {
		// Exit if encryption key is missing
		$key = get_option( 'swpsmtp_enc_key', false );
		if ( $key === false ) {
			return false;
		}
		$iv_num_bytes = openssl_cipher_iv_length( 'aes-256-ctr' );
		$iv           = substr( $raw_password, 0, $iv_num_bytes );
		$data         = substr( $raw_password, $iv_num_bytes );
		$keyhash      = openssl_digest( $key, 'sha256', true );
		$password     = openssl_decrypt( $data, 'aes-256-ctr', $keyhash, OPENSSL_RAW_DATA, $iv );
		// Exit on decrypt error
		if ( $password === false ) {
			return false;
		}
	} else {
		$password = $raw_password;
	}

	// Get test e-mail options
	$smtp_test_mail_defaults = array(
		'swpsmtp_to'      => '',
		'swpsmtp_subject' => '',
		'swpsmtp_message' => '',
	);

	$smtp_test_mail = get_option( 'smtp_test_mail', $smtp_test_mail_defaults );
	$smtp_test_mail = wp_parse_args( $smtp_test_mail, $smtp_test_mail_defaults );

	// Create config and save
	$import = array(
		'smtp-host'               => $swpsmtp_options['smtp_settings']['host'],
		'smtp-encryption-type'    => $swpsmtp_options['smtp_settings']['type_encryption'],
		'smtp-port'               => $swpsmtp_options['smtp_settings']['port'],
		'smtp-username'           => $swpsmtp_options['smtp_settings']['username'],
		'smtp-password'           => $password,
		'allow-no-authentication' => ( $swpsmtp_options['smtp_settings']['autentication'] === 'yes' ) ? 0 : 1,
		'from-email-address'      => $swpsmtp_options['from_email_field'],
		'from-email-name'         => $swpsmtp_options['from_name_field'],
		'test-email-address'      => $smtp_test_mail['swpsmtp_to'],
		'test-email-subject'      => $smtp_test_mail['swpsmtp_subject'],
		'test-email-message'      => $smtp_test_mail['swpsmtp_message'],
	);

	// Save options
	update_option( 'azrcrv-smtp-maybe', $import );
}


/**
 * Send test email.
 *
 * @since 1.0.0
 */
function send_test_email() {
	// Check that user has proper security level
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-smtp' ) );
	}
	// Check that nonce field created in configuration form is present
	if ( ! empty( $_POST ) && check_admin_referer( PLUGIN_HYPHEN . '-send-test-email', PLUGIN_HYPHEN . '-send-test-email-nonce' ) ) {

		// Retrieve original plugin options array
		$options = get_option( PLUGIN_HYPHEN );

		$option_name = 'test-email-address';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_email( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'test-email-subject';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'test-email-message';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		// Store updated options array to database
		update_option( 'azrcrv-smtp', $options );

		// Deal with PHPMailer update in Classicpress 1.4.0
		if ( file_exists( ABSPATH . WPINC . '/PHPMailer/PHPMailer.php' ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			$phpmailer = new PHPMailer();
		} else {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
			$phpmailer = new \PHPMailer();
		}

		$test_result = array();
		$error       = '';
		$result      = 'test-email&status=sent';

		$phpmailer->isSMTP();

		$charset            = get_bloginfo( 'charset' );
		$phpmailer->CharSet = $charset;

		$phpmailer->addCustomHeader( 'MIME-Version', '1.0' );
		$phpmailer->addCustomHeader( 'Content-type', 'text/html' );

		$phpmailer->Host = $options['smtp-host'];
		$phpmailer->Port = $options['smtp-port'];

		if ( $options['smtp-encryption-type'] !== 'none' ) {
			$phpmailer->SMTPSecure = $options['smtp-encryption-type'];
		}
		$phpmailer->Username = $options['smtp-username'];
		$phpmailer->Password = $options['smtp-password'];

		// Don't authenticate if explicitly set to allow no authentication when username not set and username is not set
		if ( $options['allow-no-authentication'] == 1 and $options['smtp-username'] == '' ) {
			$phpmailer->SMTPAuth = false;
		} else {
			$phpmailer->SMTPAuth = true;
		}

		if ( strlen( $options['from-email-address'] ) > 0 ) {
			$phpmailer->From = $options['from-email-address'];
		}
		if ( strlen( $options['from-email-name'] ) > 0 ) {
			$phpmailer->FromName = $options['from-email-name'];
		}

		$phpmailer->addAddress( $options['test-email-address'] );
		$phpmailer->Subject = $options['test-email-subject'];
		$phpmailer->Body    = $options['test-email-message'];

		$level                  = 2;
		$phpmailer->SMTPDebug   = 1;
		$phpmailer->Debugoutput = function( $str, $level ) use ( &$error ) {
			$error .= $level . ': ' . $str . '<br />';
		};

		// Don't fail if the server is advertising TLS with an invalid certificate
		$phpmailer->SMTPAutoTLS = false;

		if ( $phpmailer->send() ) {
			$result = 'test-email&status=sent';
		} else {
			$test_result[] = $phpmailer->ErrorInfo;
			$result        = 'test-email&status=failed';
		}

		if ( strlen( $error ) > 0 ) {
			$test_result[] = '<hr>' . $error;
		}

		update_option( PLUGIN_HYPHEN . '-test', $test_result );

		// Redirect the page to the configuration form that was processed
		wp_safe_redirect( add_query_arg( 'page', PLUGIN_HYPHEN . '&' . $result, admin_url( 'admin.php' ) ) );
		exit;
	}

}


/**
 * Intercept phpmailer and update SMTP details and send email.
 *
 * @since 1.0.0
 * @since 1.4.0 replace from address with SMTP plugin settings only if set to admin email
 */
function send_smtp_email( $phpmailer ) {

	$options = get_option_with_defaults( 'azrcrv-smtp' );

	$phpmailer->isSMTP();
	$phpmailer->Host = $options['smtp-host'];
	$phpmailer->Port = $options['smtp-port'];
	if ( $options['smtp-encryption-type'] !== 'none' ) {
		$phpmailer->SMTPSecure = $options['smtp-encryption-type'];
	}
	$phpmailer->Username = $options['smtp-username'];
	$phpmailer->Password = $options['smtp-password'];

	// Don't authenticate if username is left empty
	$phpmailer->SMTPAuth = $options['smtp-encryption-type'] !== '';
	// Don't fail if the server is advertising TLS with an invalid certificate
	$phpmailer->SMTPAutoTLS = false;

	// replace from address only if currently set to admin email
	if ( get_option( 'admin_email' ) == $phpmailer->From ) {
		if ( strlen( $options['from-email-address'] ) > 0 ) {
			$phpmailer->From = $options['from-email-address'];
		}
		if ( strlen( $options['from-email-name'] ) > 0 ) {
			$phpmailer->FromName = $options['from-email-name'];
		}
	}

	$charset            = get_bloginfo( 'charset' );
	$phpmailer->CharSet = $charset;

	$phpmailer->addCustomHeader( 'MIME-Version', '1.0' );
	$phpmailer->addCustomHeader( 'Content-type', 'text/html' );

}
