<?php
/**
 * Remove functions that add bloat
 */

/* REMOVE FEEDS */
add_filter( 'feed_links_show_posts_feed', '__return_false' );
add_filter( 'feed_links_show_comments_feed', '__return_false' );
remove_action( 'wp_head', 'feed_links', 2 ); // remove rss feed links
remove_action( 'wp_head', 'feed_links_extra', 3 ); // removes all extra rss feed links


/* REMOVE EDIT LINKS */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'index_rel_link' ); // remove link to index page
remove_action( 'wp_head', 'wlwmanifest_link' ); // Windows Live Writer


/* REMOVE OTHER LINKS */
remove_action( 'wp_head', 'wp_shortlink_wp_head' ); // remove shortlink
remove_action( 'wp_head', 'start_post_rel_link' ); // remove random post link
remove_action( 'wp_head', 'parent_post_rel_link' ); // remove parent post link
remove_action( 'wp_head', 'adjacent_posts_rel_link' ); // remove the next and previous post links
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );


/* REMOVE WP VERSION */
remove_action( 'wp_head', 'wp_generator' );


/* REMOVE HINTS for DNS PREFETCH, etc. */
remove_action( 'wp_head', 'wp_resource_hints', 2 );


/* REMOVE EMOJI (with thanks to Ryan Hellyer) */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'embed_head', 'print_emoji_detection_script' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );	
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'emoji_svg_url', '__return_false' ); // remove DNS prefetch
add_filter( 'option_use_smilies', '__return_false' ); // remove smilies

function kts_disable_emoji_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}
add_filter( 'tiny_mce_plugins', 'kts_disable_emoji_tinymce' );

# https://github.com/humanmade/altis-cms/issues/272
function kts_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( $relation_type === 'dns-prefetch' ) {

		// Strip out any URLs referencing the ClassicPress.org emoji location
		$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
		foreach ( $urls as $key => $url ) {
			if ( is_array( $url ) ) {
				if ( isset( $url['href'] ) ) {
					$url = $url['href'];
				} else {
					continue;
				}
			}
			if ( strpos( $url, $emoji_svg_url_bit ) !== false ) {
				unset( $urls[$key] );
			}
		}
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'kts_disable_emojis_remove_dns_prefetch', 10, 2 );


/* REMOVE USELESS PARTS OF REST API */
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
add_filter( 'rest_jsonp_enabled', '__return_false' );


# REMOVE OEMBED STUFF
remove_action( 'rest_api_init', 'wp_oembed_register_route' );
add_filter( 'embed_oembed_discover', '__return_false' );
remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result' );


/* REMOVE <p> FROM AROUND IMAGES */
# http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function kts_filter_ptags_on_images( $content ) {
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}
add_filter( 'the_content', 'kts_filter_ptags_on_images' );
add_filter( 'use_default_gallery_style', '__return_false' );


/* REMOVE SPACES AT THE END OF PARAGRAPHS */
function kts_trim_paras( $post_id, $post, $update ) {

	# Unhook this function so it doesn't loop infinitely
	remove_action( 'save_post_post', 'kts_trim_paras', 20, 3 );

	# Delete spaces at ends of paragraphs and move newlines
	$args = array(
		'ID'			=> $post_id,
		'post_content'	=> str_replace( [" </p>", "<p>\r\n", "\r\n</p>"], ["</p>", "\n<p>", "</p>\n"], $post->post_content )
	);

	# Update the post, which calls save_post again
	wp_update_post( $args );

	# Re-hook this function
	add_action( 'save_post_post', 'kts_trim_paras', 20, 3 );
}
add_action( 'save_post_post', 'kts_trim_paras', 20, 3 );


/* DISABLE INTERNAL PINGBACKS */
function kts_internal_pingbacks( &$links ) {
	# Unset each internal ping
	foreach ( $links as $key => $link ) {
		if ( 0 === strpos( $link, get_option( 'home' ) ) ) {
			unset( $links[$key] );
		}
	}
}
add_action( 'pre_ping', 'kts_internal_pingbacks' );
add_filter( 'pings_open', '__return_false', 20, 2 );


/* SET PINGBACK URI TO BLANK FOR BLOGINFO */
function kts_pingback_url( $output, $show ) {
	if ( $show == 'pingback_url' ) {
		$output = '';
	}
	return $output;
}
add_filter( 'bloginfo', 'kts_pingback_url', 1, 2 );
add_filter( 'bloginfo_url', 'kts_pingback_url', 1, 2 );


/* DISABLE XML-RPC & REMOVE FROM HEADERS */
function kts_remove_x_pingback( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'kts_remove_x_pingback' );
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'pre_option_enable_xmlrpc', '__return_zero' );
add_filter( 'pre_update_option_enable_xmlrpc', '__return_false' );
add_filter( 'enable_post_by_email_configuration', '__return_false' );

function kts_remove_xmlrpc_methods( $methods ) {

	# Unset Pingback Ping
	unset( $methods['pingback.ping'] );
	unset( $methods['pingback.extensions.getPingbacks'] );

	# Unset discovery of existing users
	unset( $methods['wp.getUsersBlogs'] );

	# Unset list of available methods
	unset( $methods['system.multicall'] );
	unset( $methods['system.listMethods'] );

	# Unset list of capabilities
	unset( $methods['system.getCapabilities'] );
	return $methods;
}
add_filter( 'xmlrpc_methods', 'kts_remove_xmlrpc_methods' );


/* REMOVE TEXT= ATTRIBUTES FOR HTML5 */
function kts_remove_type_attr( $tag ) {
	return preg_replace( "/\ type=['\"]text\/(javascript|css)['\"]/", '', $tag );
}
add_filter( 'style_loader_tag', 'kts_remove_type_attr' );
add_filter( 'script_loader_tag', 'kts_remove_type_attr' );
