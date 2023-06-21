<?php
/**
 * Plugin Name: Fix Dir Images
 * Plugin URI: https://simonefioravanti.it
 * Description: Fix relative paths for images that comes from a GitHub repo README.md
 * Version: 0.0.1
 * Requires PHP: 5.6
 * Requires CP: 1.4
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author: Simone Fioravanti
 * Author URI: https://simonefioravanti.it
 * Text Domain: fix-dir-images
 * Domain Path: /languages
 */

namespace XXSimoXX\FixDirImages;

if (!defined('ABSPATH')) {
	die('-1');
}

class FixDirImages {

	public function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'load_fix_images_script'], 10);
	}

	public function load_fix_images_script() {

		if (!is_singular(['plugin','theme'])) {
			return;
		}

		$post_ID = get_the_ID();
		if ($post_ID === false) {
			return;
		}

		$github_repo = get_post_meta($post_ID, 'download_link', true);
		if (empty($github_repo)) {
			return;
		}

		$prefix = preg_replace(
			'~https://github.com/([a-zA-Z0-9\-_]+)/([a-zA-Z0-9\-_]+)/releases/download/([a-zA-Z0-9\-_\.]+)/.*$~',
			'https://raw.githubusercontent.com/$1/$2/$3/',
			$github_repo
		);

		wp_enqueue_script('fix-image-script', plugins_url('/js/fix-image-script.js', __FILE__), [], '0.0.1');
		wp_localize_script(
			'fix-image-script',
			'github_data',
			[
				'prefix' => $prefix,
			]
		);

	}

}

new FixDirImages;

