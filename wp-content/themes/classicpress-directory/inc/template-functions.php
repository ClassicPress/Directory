<?php
/**
 * Functions which enhance the theme by hooking into ClassicPress
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function bedrock_wp_body_classes( $classes ) {

	# Adds a class of hfeed to non-singular pages
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	# Adds classes according to whether sidebar exists
	$sidebars = wp_get_sidebars_widgets();
	if ( ! is_active_sidebar( 'sidebar-1' ) && ! is_active_sidebar( 'sidebar-2' ) ) {
		$classes[] = 'no-sidebars';
	}
	elseif ( is_active_sidebar( 'sidebar-1' ) && is_active_sidebar( 'sidebar-2' ) ) {
		$classes[] = 'two-sidebars';
	}
	else {
		$classes[] = 'one-sidebar';
	}

	# Adds a class for when logged in or out
	if ( is_user_logged_in() ) {
		$classes[] = 'logged-in';
	}
	else {
		$classes[] = 'logged-out';
	}

	return $classes;
}
add_filter( 'body_class', 'bedrock_wp_body_classes' );


/**
 * Sets alt and loading attributes for featured images.
 *
 * @param array $attr	Attributes of the featured post.
 * @return array
 */
function bedrock_check_featured_image_attributes( $attr, $attachment, $size ) {

	if ( is_singular() ) {

		/*
		 * Ensure featured image is loaded without delay on singular pages.
		 */
		$attr['loading'] = 'eager';

	} else {

		global $wp_query;

		/*
		 * First featured image is loaded without delay on archive pages.
		 * Other featured images are lazy-loaded.
		 */
		$attr['loading'] = 'lazy';
		if ( $wp_query->current_post === 0 ) {
			$attr['loading'] = 'eager';
		}

		/*
		 * If no alt tag is set, use the post title.
		 */
		if ( empty( $attr['alt'] ) ) {
			$attr['alt'] = get_the_title();
		}
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'bedrock_check_featured_image_attributes', 10, 3 );


/* DEVELOPERS ALPHABET */
function kts_developers_alphabet() {
	$list = '<div class="alphadevelopers" role="tablist">
		<button id="letter-all" class="letter" role="tab" aria-selected="true" aria-controls="developers">ALL</button>
		<button id="letter-a" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-a-panel">A</button>
		<button id="letter-b" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-b-panel">B</button>
		<button id="letter-c" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-c-panel">C</button>
		<button id="letter-d" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-d-panel">D</button>
		<button id="letter-e" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-e-panel">E</button>
		<button id="letter-f" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-f-panel">F</button>
		<button id="letter-g" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-g-panel">G</button>
		<button id="letter-h" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-h-panel">H</button>
		<button id="letter-i" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-i-panel">I</button>
		<button id="letter-j" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-j-panel">J</button>
		<button id="letter-k" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-k-panel">K</button>
		<button id="letter-l" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-l-panel">L</button>
		<button id="letter-m" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-m-panel">M</button>
		<button id="letter-n" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-n-panel">N</button>
		<button id="letter-o" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-o-panel">O</button>
		<button id="letter-p" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-p-panel">P</button>
		<button id="letter-q" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-q-panel">Q</button>
		<button id="letter-r" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-r-panel">R</button>
		<button id="letter-s" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-s-panel">S</button>
		<button id="letter-t" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-t-panel">T</button>
		<button id="letter-u" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-u-panel">U</button>
		<button id="letter-v" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-v-panel">V</button>
		<button id="letter-w" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-w-panel">W</button>
		<button id="letter-x" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-x-panel">X</button>
		<button id="letter-y" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-y-panel">Y</button>
		<button id="letter-z" class="letter" tabindex="-1" role="tab" aria-selected="false" aria-controls="letter-z-panel">Z</button>
	</div>';
	echo $list;
}


/* DISPLAY LIST OF DEVELOPERS */
function kts_list_developers() {
	$developers = get_transient( 'developers' );

	if ( empty( $developers ) ) {
		$args = array(
			'role__not_in'	=> 'subscriber',
			'orderby'		=> 'display_name',
		);
		$users = get_users( $args );

		$previous_initial = 'A';
		$developers = '<div id="developers" class="developers"><ul id="letter-a-panel" class="developer-panel" role="tabpanel">';

		if ( ! empty( $users ) ) {
			foreach ( $users as $user ) {
				if ( count_user_posts( $user->ID, [ 'plugin', 'theme' ], true ) === '0' ) {
					continue;
				}

				$initial = strtoupper( remove_accents( $user->display_name[0] ) ); // first letter
				if ( $initial !== $previous_initial ) {
					$developers .= '</ul><ul id="letter-' . strtolower( $initial ) . '-panel" class="developer-panel" role="tabpanel">';
					$previous_initial = $initial;
				}

				$developers .= '<li><span>' . get_avatar( $user->ID, 24 ) . '</span> <a href="' . esc_url( get_author_posts_url( $user->ID ) ) . '" class="developer">' . esc_html( $user->display_name ) . '</a>&emsp;<a href="' . esc_url( 'https://github.com/' . get_user_meta( $user->ID, 'github_username', true ) . '/' ) . '" title="' . __( 'GitHub Repository for ', 'classicpress' ) . esc_attr( $user->display_name ) . '"><i class="cpicon-github dev-github"></i></a></li>';

				if ( $user === end( $users ) ) { // last name in list
					$developers .= '</ul>';
				}
			}
		}

		$developers .= '</div>';

		set_transient( 'developers', $developers, MONTH_IN_SECONDS );
	}

	echo $developers;
}


/* REFRESH DEVELOPERS TRANSIENT AS NECESSARY */
function kts_purge_developers_cache( $user_id ) {
	delete_transient( 'developers' );
	delete_transient( 'dir-user-stats' );
}

add_action( 'user_register', 'kts_purge_developers_cache' );
add_action( 'delete_user', 'kts_purge_developers_cache' );
add_action( 'profile_update', 'kts_purge_developers_cache' );

function kts_purge_developers_cpt_cache( $post_id ) {

	# Bail if an autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	# Bail if not a plugin or theme CPT
	if ( ! in_array( get_post_type( $post_id ), ['plugin', 'theme'] ) ) {
		return;
	}

	delete_transient( 'developers' );
	delete_transient( 'dir-user-stats' );
}
add_action( 'save_post', 'kts_purge_developers_cpt_cache' );
add_action( 'delete_post', 'kts_purge_developers_cpt_cache' );


/* SPECIAL EXCERPT FUNCTION */
function kts_excerpt_fallback( $post ) {
	$excerpt = $post->post_excerpt;
	if ( empty( $excerpt ) ) {
		$excerpt = substr( strip_tags( $post->post_content ), 0, 150 );
		if ( strlen( $post->post_content ) > 150 ) {
			$excerpt = $excerpt . ' ...';
		}
	}
	echo $excerpt;
}


/* SHOW CPTs ON CATEGORY, TAGS, AND AUTHOR ARCHIVE PAGES */
function kts_query_post_type( $query ) {

	# Don't run on admin pages
	if ( is_admin() ) {
		return;
	}

	# Don't run if not the main query
	if ( ! $query->is_main_query() ) {
		return;
	}

    if ( is_category() ) {
        $query->set( 'post_type', 'plugin' );
    }
    elseif ( is_tag() ) {
        $query->set( 'post_type', 'theme' );
    }
    elseif ( is_author() ) {
		$query->set( 'post_type', ['plugin', 'theme'] );
	}
}
add_action( 'pre_get_posts', 'kts_query_post_type' );


/* CACHE USER POST COUNT, USEFUL UNTIL COUNT_USER_POSTS WILL IMPLEMENT A CACHING SYSTEM. */
function kts_get_user_stat ($id) {
	$saved = get_transient( 'dir-user-stats' );
	if ( $saved !== false && isset($saved[$id]) ) {
		return $saved[$id];
	}
	if ( $saved === false ) {
		$saved = [];
	}
	foreach ( [ 'theme', 'plugin' ] as $item_type ) {
		$saved[$id][$item_type] = count_user_posts( $id, $item_type, true );
	}
	set_transient( 'dir-user-stats', $saved, 5 * MINUTE_IN_SECONDS );
	return $saved[$id];
}

/* RENDER USER TABS. RETURN THE ACTIVE ITEM TYPE*/
function kts_render_user_tabs ( $cached_count ) {
	$activated   = false;
	$item_number = 0;
	$active_item = '';
	foreach ( [ 'plugin', 'theme' ] as $item_type ) {
		$item_number++;
		$class         = 'ui-button ' . $item_type;
		$aria_selected = 'aria-selected="false"';
		$tabindex      = 'tabindex="-1"';
		$name          = ucfirst($item_type) . 's (' . $cached_count[$item_type] . ')';
		if ( $cached_count[$item_type] > 0 && $activated === false) {
			$activated     = true;
			$class        .= ' ui-state-active';
			$aria_selected = 'aria-selected="true"';
			$tabindex      = 'tabindex="0"';
			$active_item   = $item_type;
		}
		echo '<button id="ui-id-' . $item_number . '" class="' . $class . '" aria-controls="tabs-' . $item_number .'" ' . $aria_selected .' role="tab" ' . $tabindex . '>' . $name . '</button>';
	}
	return $active_item;
}
