<?php
/**
 * Plugin Name: Dir wp-cli commands
 * Plugin URI: https://simonefioravanti.it
 * Description: Add commands to wp-cli
 * Version: 0.0.1
 * Requires PHP: 5.6
 * Requires CP: 1.4
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author: Simone Fioravanti
 * Author URI: https://simonefioravanti.it
 * Text Domain: dir-cli
 * Domain Path: /languages
 */

namespace XXSimoXX\dir;

if (!defined('ABSPATH')) {
	die('-1');
}

if (defined('WP_CLI') && WP_CLI) {
	\WP_CLI::add_command('dir', '\XXSimoXX\dir\Dir');
}

/**
* Commands to work with Icons for CP.
*
*
* ## EXAMPLES
*
*     wp dir update 45
*
*
*/

class Dir{

	/**
	* Force an update of an item.
	*
	*
	* ## EXAMPLES
	*
	*     wp dir update 45
	*
	*
	*/
	public function update($args, $assoc_args) {
	
		if(!function_exists('kts_maybe_update')) {
			\WP_CLI::error('Function kts_maybe_update not found.');
		}

		$id = \WP_CLI\Utils\get_flag_value($assoc_args, 'id');
		
		if ($id === null) {
			\WP_CLI::error('You must specify a post ID.');
		}

		if ($id != (int)$id) {
			\WP_CLI::error('You must specify a numeric post ID.');
		}
		
		$id = (int)$id;

		$post  = get_post($id);
		$type  = $post->post_type;
		$title = $post->post_title;
		
		$valid_types = ['plugin', 'theme', 'snippet'];
		if (!in_array($type, $valid_types)) {
			\WP_CLI::error('You can\'t update a '.$type.'.');
		}

		$update = kts_maybe_update($id, true);

		if ($update === false || $update === true || $update['description'] === '') {
			\WP_CLI::error('Something went wrong updating '.$title.'. Check logs for errors.');
		}

		wp_update_post( [
			'ID'			=> $id,
			'post_content'	=> $update['description'],
			'meta_input'	=> [
				'download_link'		=> $update['download_link'],
				'current_version'	=> $update['current_version'],
				'requires_php'		=> $update['requires_php'],
				'requires_cp'		=> $update['requires_cp'],
			],
		] );
	

		\WP_CLI::success($title.' update successfully.');

	}

}

