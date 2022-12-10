<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Plugin Name: Faceted Search
 * Plugin URI: https://directory.classicpress.net/
 * Description: Renames "All Post Types" to "All Software"
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 0.1.0
 */

/* MODIFY SEARCHANDFILTER SHORTCODE */
function kts_modify_searchandfilter( $output, $tag ) {

	# Target only searchandfilter shortcode
	if ( $tag !== 'searchandfilter' ) {
		return $output;
	}

	# Give the search input an ID and label for accessibility
	$pattern1 = '~<input type="text" name="ofsearch"~s';
	preg_match( $pattern1, $output, $copies );
	$output = str_replace( $copies[0], '<label for="ofsearch" class="screen-reader-text">Search</label><input type="text" id="ofsearch" name="ofsearch"', $output );

	# Replace "All Post Types" with "All Software"
	$pattern2 = '~value="plugin,theme">(.*?)</option>~s';
	preg_match( $pattern2, $output, $matches );
	$new_output = str_replace( $matches[1], 'All Software', $output );

	# Output shortcode and set in transient
	set_transient( 'kts-mod-searchandfilter', $new_output, MONTH_IN_SECONDS );

	return $new_output;
}
add_filter( 'do_shortcode_tag', 'kts_modify_searchandfilter', 10, 2 );


/* CACHE SEARCHANDFILTER SHORTCODE OUTPUT IN TRANSIENT */
function kts_searchandfilter_cache( $output, $tag ) {

	# Target only searchandfilter shortcode
	if ( $tag !== 'searchandfilter' ) {
		return $output;
	}

	# Returns transient or false
	return get_transient( 'kts-mod-searchandfilter' );
}
add_filter( 'pre_do_shortcode_tag', 'kts_searchandfilter_cache', 10, 2 );


/* REFRESH SEARCHANDFILTER CACHE AS NECESSARY */
function kts_purge_searchandfilter_cache( $post_id ) {

	# Only purge when a plugin or theme is involved
	if ( ! in_array( get_post_type( $post_id ), ['plugin', 'theme'] ) ) {
		return;
	}

	delete_transient( 'kts-mod-searchandfilter' );
}
add_action( 'save_post', 'kts_purge_searchandfilter_cache' );
add_action( 'delete_post', 'kts_purge_searchandfilter_cache' );
