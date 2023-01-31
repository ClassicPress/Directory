<?php

/**
 * Plugin Name: Custom Status
 * Plugin URI: https://directory.classicpress.net/
 * Description: Adds status to plugins and themes
 * Author: Simone Fioravanti
 * Version: 0.1.0
 */

namespace ClassicPress\Status;

if (!defined('ABSPATH')) {
	die('-1');
}


class Status{

	private $statuses = [];

	public function __construct() {

		$this->statuses = [
				'active'    => esc_html__('Active', 'classicpress-directory'),
				'suspended' => esc_html__('Suspended', 'classicpress-directory'),
				'closed'    => esc_html__('Closed', 'classicpress-directory'),
				'ownership' => esc_html__('Ownership changed', 'classicpress-directory'),
				'adoption'  => esc_html__('Adoptable', 'classicpress-directory'),
			];

		add_action('add_meta_boxes', [$this, 'status_add_meta_box']);
		add_action('save_post_plugin', [$this, 'status_save'], 10, 1);
		add_action('save_post_theme', [$this, 'status_save'], 10, 1);

	}

	public function status_add_meta_box() {
		add_meta_box('cpdir-status', esc_html__('Status', 'classicpress-directory'), [$this, 'status_render_meta_box'], ['plugin', 'theme'], 'side');
	}

	private function render_select($status) {
		echo '<div id="directory-status">';
		echo '<select name="cpdir_status" id="cpdir_status">';
		foreach ($this->statuses as $key => $value) {
			$selected = $status === $key ? ' selected' : '';
			echo '<option value="'.sanitize_key($key).'"'.sanitize_key($selected).'>'.esc_html($value).'</option>';
		}
		echo '</select>';
		wp_nonce_field('change_status', 'change_status__nonce');
		echo '</div>';
	}

	public function status_render_meta_box($post) {
		$status = get_post_meta($post->ID, 'item_status', true);
		$status = array_key_exists($status, $this->statuses) ? $status : 'active';
		$this->render_select($status);
	}

	public function status_save($post_ID) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (!current_user_can('edit_post', $post_ID)) {
			return;
		}
		if (!array_key_exists('cpdir_status', $_REQUEST)) {
			return;
		}
		$status = sanitize_key(wp_unslash($_REQUEST['cpdir_status']));
		if (!array_key_exists($status, $this->statuses)) {
		return;
		}
		update_post_meta($post_ID, 'item_status', $status);
	}

}

new Status;




