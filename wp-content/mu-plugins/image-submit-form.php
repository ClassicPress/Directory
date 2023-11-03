<?php
/**
 * Plugin Name: Image Submit Form
 * Plugin URI: https://directory.classicpress.net/
 * Description: Form to enable submission of the featured image for a ClassicPress plugin or theme
 * Author: Simone Fioravanti
 * Author URI: https://simonefioravanti.it
 * Version: 0.1
 */

if (!defined('ABSPATH')) {
	exit;
}

function xsx_render_image_submit_form() {
	$user_id = get_current_user_id();
	$two_fa = kts_2fa_enabled($user_id);
	ob_start();

	if (!is_user_logged_in()) {
		_e('<p>You need to <a href="'.esc_url(wp_login_url(get_permalink())).'">log in</a> to be able to use the form on this page.</p>', 'classicpress');
	} elseif (!current_user_can('edit_posts')) { // Not a Contributor or above
		if ($two_fa === false) {
			_e('<p>You must be approved before you can submit images.</p>', 'classicpress');
			_e('<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="'.esc_url(get_edit_profile_url($user_id)).'#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>.</p>', 'classicpress');
			_e('<p>You will receive an email when you have been approved.</p>', 'classicpress');
		} else {
			$two_fa_method = get_user_meta($user_id, '_two_factor_provider', true);
			if ($two_fa_method === 'Two_Factor_Dummy') {
				_e('<p>You must be approved before you can submit images.</p>', 'classicpress');
				_e('<p><strong>IMPORTANT:</strong> Before you can be approved, you must go to your <strong><a href="'.esc_url(get_edit_profile_url($user_id)).'#two-factor-options">profile page and activate 2-Factor Authentication</a></strong>. Enabling the dummy method is not acceptable for this purpose.</p>', 'classicpress');
				_e('<p>You will receive an email when you have been approved.</p>', 'classicpress');
			} else {
				_e('<p>Your account is pending review. You will receive an email when you have been approved.</p>', 'classicpress');
			}
		}
	} else { // Contributor role or above
		_e('<p>Please use the form below to add or change a theme or plugin featured image.</p>', 'classicpress');

		$cp_nonce = cp_set_nonce('image_nonce');
		$categories = get_categories([
			'taxonomy'		=> 'category',
			'hide_empty'	=> false,
			'exclude'		=> [get_cat_ID('Uncategorized')],
		]);

		# Error Messages
		if (isset($_GET['notification'])) {
			if ($_GET['notification'] === 'nonce-wrong') {
				echo '<div class="alert error-message" role="alert"><p>'.__('You have already submitted this form.', 'classicpress').'</p></div>';
			} elseif ($_GET['notification'] === 'no-id') {
				echo '<div class="alert error-message" role="alert"><p>'.__('You must provide a plugin or theme!', 'classicpress').'</p></div>';
			} elseif ($_GET['notification'] === 'no-post') {
				echo '<div class="alert error-message" role="alert"><p>'.__('You must provide an existing software!', 'classicpress').'</p></div>';
			} elseif ($_GET['notification'] === 'wrong-item') {
				echo '<div class="alert error-message" role="alert"><p>'.__('You must provide an existing type of software!', 'classicpress').'</p></div>';
			} elseif ($_GET['notification'] === 'wrong-owner') {
				echo '<div class="alert error-message" role="alert"><p>'.__('You are not the owner of that item!', 'classicpress').'</p></div>';
			} elseif ($_GET['notification'] === 'wrong-file') {
				echo '<div class="alert error-message" role="alert"><p>'.__('There was a problem uploading your file!', 'classicpress').'</p></div>';
			} elseif ($_GET['notification'] === 'wrong-thumb') {
				echo '<div class="alert error-message" role="alert"><p>'.__('There was a problem setting your custom image!', 'classicpress').'</p></div>';
			} else {
				echo '<div class="alert error-message" role="alert"><p>'.__('Unknown error!', 'classicpress').'</p></div>';
			}
		}

	$args = [
		'author'         => $user_id,
		'orderby'        => 'name',
		'order'          => 'ASC',
		'posts_per_page' => -1,
		'post_status' => [
			'draft',
			'publish',
			'pending',
		],
		'post_type'   => [
			'plugin',
			'theme',
		],
	];

	$current_user_posts = get_posts($args);
	$post_statuses      = get_post_statuses();
	$select_content     = '';
	foreach ($current_user_posts as $post) {
		$status = $post->post_status === 'publish' ? '' : ', '.$post_statuses[$post->post_status];
		$select_content .= '<option value="'.(int) $post->ID.'">'.esc_html($post->post_title).'('.ucfirst(esc_html($post->post_type)).$status.')</option>'."\n";
	}
?>

		<section>
			<div class="section-header">
				<h2><?php _e('Software Information', 'classicpress'); ?></h2>
				<small class="required-text"><?php _e('All fields are required.', 'classicpress'); ?></small>
			</div>

			<noscript>
				<div class="alert error-message" role="alert">
					<p><?php _e('This form will not work without JavaScript turned on.', 'classicpress'); ?></p>
				</div>
			</noscript>

			<form id="submit-cp-image-form" method="post" autocomplete="off" enctype="multipart/form-data">

				<fieldset>
					<legend><?php _e('Select your plugin or theme', 'classicpress'); ?></legend>
					<div class="clear"></div>
					<select name="item" id="item">
						<?php echo $select_content; ?>
					</select>
					<input type="file" id="file" name="file" multiple="false">
				</fieldset>

				<input type="hidden" name="cp-nonce-name" value="<?php echo esc_attr($cp_nonce['name']); ?>">
				<input type="hidden" name="cp-nonce-value" value="<?php echo esc_attr($cp_nonce['value']); ?>">

				<div class="form-btn">
					<button id="submit-btn" type="submit" enterkeyhint="send"><?php _e('Submit', 'classicpress'); ?></button>
					<button type="reset" enterkeyhint="go"><?php _e('Clear', 'classicpress'); ?></button>
				</div>
			</form>
		</section>
<?php
	}
	echo ob_get_clean();
}



// PROCESS SUBMISSION FORM
function xsx_image_submit_form_redirect() {
	# Check that user has the capability to submit software for review
	if (!current_user_can('edit_posts')) { // Contributor role or above
		return;
	}

	# Check for nonce
	if (empty($_POST['cp-nonce-name'])) {
		return;
	}

	# Exit if outside the form
	if (strpos($_POST['cp-nonce-name'], 'image_nonce_') !== 0) {
		return;
	}

	# If nonce is wrong
	$nonce = cp_check_nonce($_POST['cp-nonce-name'], $_POST['cp-nonce-value']);
	$referer = remove_query_arg('notification', wp_get_referer());

	if ($nonce === false) {
		wp_safe_redirect(esc_url_raw($referer.'?notification=nonce-wrong'));
		exit;
	}

	$item_ID = (int) $_POST['item'];
	# Check that the ID has been specified
	if (empty($item_ID)) {
		wp_safe_redirect(esc_url_raw($referer.'?notification=no-id'));
		exit;
	}

	$post = get_post($item_ID);
	# Check that the post specified actually exists
	if ($post === null) {
		wp_safe_redirect(esc_url_raw($referer.'?notification=no-post'));
		exit;
	}

	# Check that the type of software specified actually exists
	$software_types = ['plugin', 'theme', 'snippet'];
	if (!in_array($post->post_type, $software_types)) {
		wp_safe_redirect(esc_url_raw($referer.'?notification=wrong-item'));
		exit;
	}

	# Check that the software belongs to current user
	if ((int) get_current_user_id() !== (int) $post->post_author) {
		wp_safe_redirect(esc_url_raw($referer.'?notification=wrong-owner'));
		exit;
	}

	require_once(ABSPATH.'wp-admin/includes/image.php');
	require_once(ABSPATH.'wp-admin/includes/file.php');
	require_once(ABSPATH.'wp-admin/includes/media.php');
	$attachment_id = media_handle_upload('file', $item_ID);

	if (is_wp_error($attachment_id)) {
		wp_safe_redirect(esc_url_raw($referer.'?notification=wrong-file'));
		exit;
	}

	set_post_thumbnail($post, $attachment_id);

	# Redirect to post where published
	wp_safe_redirect(esc_url_raw(get_permalink($post)));
	exit;
}
add_action('template_redirect', 'xsx_image_submit_form_redirect');