<?php if (!defined('ABSPATH')) {
	exit;
}

/**
 * Plugin Name: Two Factor
 * Plugin URI: https://directory.classicpress.net/
 * Description: Code tweaks to address whether user has enabled 2FA
 * Author: Tim Kaye
 * Author URI: https://timkaye.org/
 * Version: 0.1.0
 **/

/* CHECK IF USER HAS ENABLED 2FA */
function kts_2fa_enabled($user_id)
{
	if (class_exists('Two_Factor_Core')) {
		$two_fa = Two_Factor_Core::is_user_using_two_factor($user_id);
		return empty($two_fa) ? false : true;
	} else {
		return new WP_Error('two_fa', __('The Two Factor plugin is not activated on this site.'));
	}
}


/* ADD MESSAGE TO TOP OF EACH SUBSCRIBER'S PROFILE PAGE */
function kts_profile_message()
{
	$user_id = get_current_user_id();
	$two_fa = kts_2fa_enabled($user_id);
	if (!current_user_can('edit_posts')) {
		if ($two_fa === false) {
			_e('<div class="notice notice-info is-dismissible two-fa"><strong>You need to activate <a href="#two-factor-options">Two Factor Authentication</a> before you can be approved to submit software to the Directory.</strong></div>', 'classicpress');
		} else {
			$two_fa_method = get_user_meta($user_id, '_two_factor_provider', true);
			if ($two_fa_method === 'Two_Factor_Dummy') {
				_e('<div class="notice notice-info is-dismissible two-fa"><strong>You need to activate <a href="#two-factor-options">Two Factor Authentication</a> before you can be approved to submit software to the Directory.<br><br>Enabling the dummy method is not acceptable for this purpose.</strong></div>', 'classicpress');
			} else {
				_e('<div class="notice notice-info is-dismissible two-fa"><strong>Your account is pending review. You will receive an email when you have been approved.</strong></div>', 'classicpress');
			}
		}
	}
}
add_action('admin_notices', 'kts_profile_message');


/* SEND EMAIL TO ADMINS WHEN 2FA FIRST ACTIVATED BY SUBSCRIBER */
function kts_email_admins_when_2fa_enabled($check, $user_id, $meta_key, $meta_value, $prev_value)
{

	# Bail if the metadata being updated is not about 2FA
	if ($meta_key !== '_two_factor_enabled_providers') {
		return $check;
	}

	# Bail if not about a subscriber
	$user = get_user_by('id', $user_id);
	if (!in_array('subscriber', $user->roles)) {
		return $check;
	}

	# Bail if two-factor-enabled already set
	$meta2fa = get_user_meta($user_id, 'two-factor-enabled', true);
	if ($meta2fa === '1') {
		return $check;
	}

	# Email admins when 2FA enabled for the first time
	if ((is_string($prev_value) && $prev_value === '') || (is_array($prev_value) && count(array_intersect(['', 'Two_Factor_Dummy'], $prev_value)) > 0)) {
		if (is_array($meta_value) && count(array_intersect(['', 'Two_Factor_Dummy'], $meta_value)) === 0) {

			$admins = get_users(array('role' => 'administrator'));
			$user = get_user_by('id', $user_id);
			$profile_url = admin_url('user-edit.php?user_id=' . $user_id);

			$subject = __('A subscriber to the ClassicPress Directory has enabled 2FA.', 'classicpress');

			$message = $user->display_name . __(' has enabled 2FA. You may now consider upgrading them to the Contributor role at ' . make_clickable(esc_url($profile_url)) . '.');

			$headers = array('Content-Type: text/html; charset=UTF-8');

			foreach ($admins as $admin) {
				wp_mail($admin->user_email, $subject, $message, $headers);
			}

			add_user_meta($user_id, 'two-factor-enabled', 1, true);
		}
	}

	return $check;
}
add_filter('update_user_metadata', 'kts_email_admins_when_2fa_enabled', 10, 5);


/* PREVENT CONTRIBUTORS AND ABOVE DEACTIVATING 2FA */
function kts_prevent_2fa_removal($providers, $user_id)
{

	if (current_user_can('edit_posts')) {
		if (empty($providers) && class_exists('Two_Factor_Email')) {
			$providers[] = 'Two_Factor_Email';
		}
	}
	update_user_meta($user_id, '_two_factor_enabled_providers', $providers);

	return $providers;
}
add_filter('two_factor_enabled_providers_for_user', 'kts_prevent_2fa_removal', 10, 2);

/* DISABLE FIDO PROVIDER SINCE IT'S BROKEN */
add_filter('two_factor_providers', 'kts_remove_fido');
function kts_remove_fido($providers)
{
	unset($providers['Two_Factor_FIDO_U2F']);
	return $providers;
}
