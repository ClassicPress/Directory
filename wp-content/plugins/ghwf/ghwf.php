<?php
/**
 * Plugin Name: GitHub Workflow Status
 * Plugin URI: https://classicpress.net
 * Description: GitHub Workflow Status.
 * Version: 1.0.0
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author: ClassicPress
 * Author URI: https://classicpress.net
 * Requires CP: 2.0
 * Requires PHP: 7.4
 */


if (!defined('ABSPATH')) {
	die('-1');
}


function ghwf_get_workflow_status($post_id, $force = false) {
	$saved_data = get_transient('ghwf_cache_'.$post_id);
	if ($saved_data !== false || $force) {
		return $saved_data === 'success';
	}

	$workflow = get_post_meta($post_id, 'workflow', true);
	if ($workflow === '') {
		return null;
	}
	$download_link = get_post_meta($post_id, 'download_link', true);
	$github_api    = str_replace('https://github.com', 'https://api.github.com/repos', $download_link);
	$github_api    = substr($github_api, 0, strpos($github_api, '/releases'));
	$github_api    = $github_api.'/actions/workflows/'.$workflow.'/runs';

	if (defined('GITHUB_API_TOKEN')) {
		$auth = [
			'headers' => [
				'Authorization' => 'token '.GITHUB_API_TOKEN,
			],
		];
	} else {
		$auth = [];
	}
	$github_info = wp_remote_get($github_api, $auth);
	if (is_wp_error($github_info)) {
		set_transient('ghwf_cache_'.$post_id, 'GitHubError', HOUR_IN_SECONDS);
		return null;
	}

	$data = json_decode(wp_remote_retrieve_body($github_info));
	if (!isset($data->workflow_runs[0]->conclusion)) {
		set_transient('ghwf_cache_'.$post_id, 'NoConclusionData', HOUR_IN_SECONDS);
		return null;
	}

	set_transient('ghwf_cache_'.$post_id, $data->workflow_runs[0]->conclusion, HOUR_IN_SECONDS);
	return $data->workflow_runs[0]->conclusion === 'success';
}

function ghwf_render_page() {
	echo '<h1>Workflow status</h1>';
	$args = [
		'post_type'      => [
			'plugin',
			'theme',
		],
		'posts_per_page' => -1,
		'post_status'    => [
			'publish',
			'draft',
		],
		'meta_key'       => 'workflow',
	];
	$posts = get_posts($args);
	?>
<style>

	.ghwf th {
		border:1px solid #b3adad;
		padding:5px;
		background: #f0f0f0;
		color: #313030;
	}
	.ghwf td {
		border:1px solid #b3adad;
		text-align:left;
		padding:5px;
		background: #ffffff;
		color: #313030;
	}
</style>
<table class="ghwf">
<tr><th>Name</th><th>Type</th><th>Status</th></tr>
	<?php
	foreach ($posts as $post) {
		$title  = get_the_title($post);
		$type   = $post->post_type;
		$status = ghwf_get_workflow_status($post->ID);
		if ($status === null) {
			$status_icon = '<i class="dashicons dashicons-warning"></i>';
		} elseif ($status === true) {
			$status_icon = '<i class="dashicons dashicons-yes-alt" style="color:green !important;"></i>';
		} else {
			$status_icon = '<i class="dashicons dashicons-no" style="color:red !important;"></i>';
		}
		echo "<tr><td>$title</td><td>$type</td><td>$status_icon</td></tr>";

	}
	?>
</table>
	<?php
}

add_action('admin_menu', 'ghwf_create_menu', 100);
function ghwf_create_menu() {
	if (!current_user_can('manage_options')) {
		return;
	}
	$page = add_menu_page(
		'Workflows',
		'Workflows',
		'manage_options',
		'workflows',
		'ghwf_render_page',
		'dashicons-welcome-learn-more',
	);
}

add_filter('manage_plugin_posts_columns', 'ghwf_status_column', 10, 1);
add_filter('manage_theme_posts_columns', 'ghwf_status_column', 10, 1);
function ghwf_status_column($columns) {
	$columns['ghwf'] = esc_html__('GHWF', 'classicpress-directory');
	return $columns;
}

add_action('manage_plugin_posts_custom_column', 'ghwf_status_render', 10, 2);
add_action('manage_theme_posts_custom_column', 'ghwf_status_render', 10, 2);
function ghwf_status_render($column, $id) {
	if ($column !== 'ghwf') {
		return;
	}
	$status = ghwf_get_workflow_status($id);
	if ($status === null) {
		$status_icon = '<i class="dashicons dashicons-warning"></i>';
	} elseif ($status === true) {
		$status_icon = '<i class="dashicons dashicons-yes-alt" style="color:green !important;"></i>';
	} else {
		$status_icon = '<i class="dashicons dashicons-no" style="color:red !important;"></i>';
	}
	echo "$status_icon";
}

