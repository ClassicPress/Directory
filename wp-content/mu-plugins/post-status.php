<?php

/**
 * Plugin Name: Custom Post status
 * Plugin URI: https://directory.classicpress.net/
 * Description: Adds Suspended and closed post status
 * Author: Simone Fioravanti
 * Version: 0.1.0
 */

namespace ClassicPress\PostStatus;

if (!defined('ABSPATH')) {
	die('-1');
}


class PostStatus{

	public function __construct() {
		add_action('init', [$this, 'create_status']);
		add_action('admin_footer', [$this, 'display_status']);
		add_filter('display_post_states', [$this, 'status_in_list'], 100, 3);
	}

	public function create_status() {

		register_post_status('suspended', [
			'label'                     => __('Suspended', 'classicpress-directory'),
			'label_count'               => _n_noop('Suspended <span class="count">(%s)</span>', 'Suspended <span class="count">(%s)</span>', 'classicpress-directory'),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
		]);

		register_post_status('closed', [
			'label'                     => __('Closed', 'classicpress-directory'),
			'label_count'               => _n_noop('Closed <span class="count">(%s)</span>', 'Closed <span class="count">(%s)</span>', 'classicpress-directory'),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
		]);

	}

	public function status_in_list($states, $post) {

		$post_status_object = get_post_status_object($post->post_status);
		if (!in_array($post_status_object->name, ['suspended', 'closed'])) {
			return $states;
		}

		$states[ $post_status_object->name ] = $post_status_object->label;

		return $states;

	}

	public function display_status() {

		global $post;
		$current_screen = get_current_screen();
		if (!in_array($current_screen->id, ['plugin', 'theme'])) {
			return;
		}

		$selected_suspended = $post->post_status === 'suspended' ? 'selected' : '';
		$status_suspended   = $post->post_status === 'suspended' ? '$("span#post-status-display").html("'.__('Suspended', 'classicpress-directory').'");' : '';
		$selected_closed    = $post->post_status === 'closed' ? 'selected' : '';
		$status_closed      = $post->post_status === 'closed' ? '$("span#post-status-display").html("'.__('Suspended', 'classicpress-directory').'");' : '';
		echo '<script>
		$(document).ready(function(){
			$("select#post_status").append("<option value=\"suspended\" '.$selected_suspended.'>'.__('Suspended', 'post', 'classicpress-directory').'</option>");
			$("select#post_status").append("<option value=\"closed\" '.$selected_closed.'>'.__('Closed', 'classicpress-directory').'</option>");
			'.$status_suspended.'
		    '.$status_closed.'
			;
		});
		</script>
		';

	}

}

new PostStatus;




