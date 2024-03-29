<?php if ( ! defined( 'ABSPATH' ) ) { die(); }

/**
 * Plugin Name: REST API Mods for Plugins, Themes, and Snippets
 * Description: Modifies the REST API for the above CPTs' endpoints
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Version: 0.1.0
 */

/* REGISTER META FIELDS WITH REST API */
function kts_register_meta_with_rest_api() {
	$plugin_args1 = array(
		'type'			=> 'string',
		'description'	=> 'Current Version of Software',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'current_version', $plugin_args1 );

	$plugin_args2 = array(
		'type'			=> 'string',
		'description'	=> 'Git Provider',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'git_provider', $plugin_args2 );

	$plugin_args3 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of ClassicPress',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'requires_cp', $plugin_args3 );

	$plugin_args4 = array(
		'type'			=> 'string',
		'description'	=> 'Download Link URL',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'download_link', $plugin_args4 );

	$plugin_args5 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of PHP',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'requires_php', $plugin_args5 );

	$plugin_args6 = array(
		'type'			=> 'string',
		'description'	=> 'Plugin slug',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'slug', $plugin_args6 );

	$plugin_args7 = array(
		'type'			=> 'string',
		'description'	=> 'Developer name',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'developer_name', $plugin_args7 );

	$plugin_args8 = array(
		'type'			=> 'string',
		'description'	=> 'Category names',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'category_names', $plugin_args8 );

	$plugin_args9 = array(
		'type'			=> 'string',
		'description'	=> 'Category slugs',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'category_slugs', $plugin_args9 );

	$plugin_args10 = array(
		'type'			=> 'string',
		'description'	=> 'Active installations',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'active_installations', $plugin_args10 );
	
	$plugin_args11 = array(
		'type'			=> 'string',
		'description'	=> 'Release date',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'published_at', $plugin_args11 );

	$plugin_args12 = array(
		'type'			=> 'string',
		'description'	=> 'Status',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'item_status', $plugin_args12 );
	
	$plugin_args13 = array(
		'type'			=> 'string',
		'description'	=> 'Premium URI',
		'single'		=> true,
		'object_subtype'=> 'plugin',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'premium_uri', $plugin_args13 );


	$theme_args1 = array(
		'type'			=> 'string',
		'description'	=> 'Current Version of Software',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'current_version', $theme_args1 );

	$theme_args2 = array(
		'type'			=> 'string',
		'description'	=> 'Git Provider',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'git_provider', $theme_args2 );

	$theme_args3 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of ClassicPress',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'requires_cp', $theme_args3 );

	$theme_args4 = array(
		'type'			=> 'string',
		'description'	=> 'Download Link URL',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'download_link', $theme_args4 );

	$theme_args5 = array(
		'type'			=> 'string',
		'description'	=> 'Minimum Required Version of PHP',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'requires_php', $theme_args5 );

	$theme_args6 = array(
		'type'			=> 'string',
		'description'	=> 'Theme slug',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'slug', $theme_args6 );

	$theme_args7 = array(
		'type'			=> 'string',
		'description'	=> 'Developer name',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'developer_name', $theme_args7 );

	$theme_args8 = array(
		'type'			=> 'string',
		'description'	=> 'Tags',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'tags', $theme_args8 );

	$theme_args9 = array(
		'type'			=> 'string',
		'description'	=> 'Active installations',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'active_installations', $theme_args9 );

	$theme_args10 = array(
		'type'			=> 'string',
		'description'	=> 'Release date',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'published_at', $theme_args10 );
	
	$theme_args11 = array(
		'type'			=> 'string',
		'description'	=> 'Status',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'item_status', $theme_args11 );

	$theme_args12 = array(
		'type'			=> 'string',
		'description'	=> 'Premium URI',
		'single'		=> true,
		'object_subtype'=> 'theme',
		'show_in_rest'	=> true,
	);
	register_meta( 'post', 'premium_uri', $theme_args12 );

}
add_action( 'init', 'kts_register_meta_with_rest_api' );


/* REMOVE FIELDS FROM REST RESPONSE */
function kts_prepare_rest( $response, $post, $request ) {

	# Remove the troublesome '_links'
	foreach( $response->get_links() as $key => $value ) {
        $response->remove_link( $key );
    }

	# Retrieve the modified data
	$data = $response->get_data();

	# Unset the unwanted fields
	unset( $data['id'] );
	unset( $data['modified'] );
	unset( $data['template'] );
	unset( $data['date_gmt'] );
	unset( $data['author'] );
	unset( $data['link'] );
	unset( $data['type'] );
	unset( $data['date'] );
	unset( $data['slug'] );
	unset( $data['featured_media'] );	
	unset( $data['guid'] );
	unset( $data['modified_gmt'] );
	unset( $data['status'] );

	# Unset cats and tags because they return only IDs; names set as meta above
	if ( $post->post_type === 'plugin' ) {
		unset( $data['categories'] );
	}
	elseif ( $post->post_type === 'theme' ) {
		unset( $data['tags'] );
	}

	# Set the modified data as the response
	$response->set_data( $data );

	# Return the response
	return $response;
}
add_filter( 'rest_prepare_plugin', 'kts_prepare_rest', 10, 3 );
add_filter( 'rest_prepare_theme', 'kts_prepare_rest', 10, 3 );


/* REST API SECURITY */
function kts_modify_rest_software_routes( $response, $handler, $request ) {

	# Prevent editing and deletion of plugin and theme CPTs
	$routes = array(
        '/wp/v2/plugins',
        '/wp/v2/themes',
	);

	if ( in_array( $request->get_route(), $routes ) ) {		

		$methods = array( 'POST', 'PUT', 'PATCH', 'DELETE' );
		if ( in_array( $request->get_method(), $methods ) ) {

			return new WP_Error(
				'rest_cpts_cannot_1',
				__( 'Sorry, you are not allowed to edit or delete posts.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
	}
}
add_filter( 'rest_request_before_callbacks', 'kts_modify_rest_software_routes', 10, 3 );


/* ENABLE SEARCHING REST API BY SLUG META */
function kts_filter_posts_by_slug_field( $args, $request ) {
	if ( empty( $request['byslug'] )  ) {
		return $args;
	}

	$slug_value = sanitize_text_field( $request['byslug'] );
	$slug_meta_query = array(
		'key' => 'slug',
		'value' => explode( ',', $slug_value ),
	);

	foreach ( explode( ',', $slug_value ) as $slug) {
		apply_filters( 'cpdir_rest_search', $slug, $_SERVER['HTTP_USER_AGENT'], $request->get_route() );
	}

	if ( isset( $args['meta_query'] ) ) {
		$args['meta_query']['relation'] = 'AND';
		$args['meta_query'][] = $slug_meta_query;
	} else {
		$args['meta_query'] = array();
		$args['meta_query'][] = $slug_meta_query;
	}

	return $args;
}
add_filter( 'rest_plugin_query', 'kts_filter_posts_by_slug_field', 999, 2 );
add_filter( 'rest_theme_query', 'kts_filter_posts_by_slug_field', 999, 2 );
