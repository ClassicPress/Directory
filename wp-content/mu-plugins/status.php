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
		add_filter('manage_plugin_posts_columns', [$this, 'status_column'], 10, 1);
		add_action('manage_plugin_posts_custom_column', [$this, 'status_render'], 10, 2);
		add_filter('manage_edit-plugin_sortable_columns', [$this, 'status_sortable'], 10, 1);
		add_filter('manage_theme_posts_columns', [$this, 'status_column'], 10, 1);
		add_action('manage_theme_posts_custom_column', [$this, 'status_render'], 10, 2);
		add_filter('manage_edit-theme_sortable_columns', [$this, 'status_sortable'], 10, 1);
		add_action('pre_get_posts', [$this, 'sort_query'], 10, 1);
	}

	public function sort_query($query) {
		$orderby = $query->get('orderby');
		if ($orderby !== 'status') {
			return;
		}
		$meta_query = [
			'relation' => 'OR',
			[
				'key'     => 'item_status',
				'compare' => 'NOT EXISTS',
			],
			[
				'key'     => 'item_status',
				'compare' => 'meta_value',
				'order' => 'DESC',
			],
		];
        $query->set( 'meta_query', $meta_query );
        $query->set( 'orderby', 'meta_value' );
	}

	public function status_sortable($columns) {
		$columns['status'] = 'status';
		return $columns;
	}

	public function status_render($column, $id) {
		if ($column !== 'status') {
			return;
		}
		$status = get_post_meta($id, 'item_status', true);
		if (!array_key_exists($status,$this->statuses)) {
			return;
		}
		esc_html_e($this->statuses[$status]);
	}

	public function status_column($columns) {
		$columns['status'] = esc_html__('Status', 'classicpress-directory');
		return $columns;
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




