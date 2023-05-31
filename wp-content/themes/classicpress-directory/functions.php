<?php

/**
 * Basic Bedrock functions and definitions
 */

if (!function_exists('bedrock_setup')) :
	/**
	 * Sets up theme defaults and registers support for various ClassicPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function bedrock_setup()
	{
		load_theme_textdomain('classicpress', get_template_directory() . '/languages');

		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/*
		 * Let ClassicPress manage the document title.
		 * By adding theme support, we declare that this theme does not
		 * use a hard-coded <title> tag in the document head, and expect
		 * ClassicPress to provide it for us.
		 */
		add_theme_support('title-tag');

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://docs.classicpress.net/reference/functions/the_post_thumbnail/
		 */
		add_theme_support('post-thumbnails');

		// Set the default content width.
		$GLOBALS['content_width'] = 525;

		// This theme uses wp_nav_menu() in three locations.
		register_nav_menus(array(
			'menu-1' => esc_html__('Primary', 'classicpress'), // main nav in header
			'footer-links' => esc_html__('Footer Links', 'classicpress') // secondary nav in footer
		));

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support('html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		));

		// Set up the ClassicPress core custom background feature.
		add_theme_support('custom-background', apply_filters(
			'bedrock_custom_background_args',
			array(
				'default-color' => 'fffefc',
				'default-image' => '',
			)
		));

		// Add theme support for selective refresh for widgets.
		add_theme_support('customize-selective-refresh-widgets');

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://docs.classicpress.net/reference/functions/get_custom_logo/
		 */
		add_theme_support('custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		));
	}
endif;
add_action('after_setup_theme', 'bedrock_setup');

/**
 * Enqueue scripts and styles. Note that it is no longer recommended
 * to concatenate such files. Instead, it is better to keep such files
 * as small as possible and let the browser load them concurrently.
 * 
 * This block first loads normalize.css to account for differences
 * across browsers.
 * 
 * Then it loads new-css-reset to reset many properties to values nicer
 * than browser defaults.
 */
function bedrock_scripts()
{

	# https://elad2412.github.io/the-new-css-reset/
	wp_enqueue_style('css-reset', get_template_directory_uri() . '/css/new-css-reset.css');

	# https://necolas.github.io/normalize.css/
	wp_enqueue_style('normalize-css', get_template_directory_uri() . '/css/normalize.css');

	# Theme-specific styles and scripts
	wp_enqueue_style('bedrock-style', get_stylesheet_uri());
	wp_enqueue_script('bedrock-js', get_template_directory_uri() . '/js/scripts.js', null, null, true);

	wp_deregister_script('wp-embed');

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	# Remove dashicons in frontend for unauthenticated users
	if (!is_user_logged_in()) {
		wp_deregister_style('dashicons');
	}

	if (is_author()) {
		wp_enqueue_style('modal-css', get_template_directory_uri() . '/css/modal.css');
		wp_enqueue_script('tabs-js', get_template_directory_uri() . '/js/tabs.js', null, null, true);
		wp_enqueue_script('archive-js', get_template_directory_uri() . '/js/archive.js', null, null, true);
	} elseif (is_archive() || is_search()) {
		wp_enqueue_style('modal-css', get_template_directory_uri() . '/css/modal.css');
		wp_enqueue_script('archive-js', get_template_directory_uri() . '/js/archive.js', null, null, true);
	} elseif (is_page('software-submission-form')) {
		wp_enqueue_script('form-js', get_template_directory_uri() . '/js/form.js', null, null, true);
	} elseif (is_page('developers')) {
		wp_enqueue_script('developers-js', get_template_directory_uri() . '/js/developers.js', null, null, true);
	}
}
add_action('wp_enqueue_scripts', 'bedrock_scripts');


/* ENQUEUE CSS ON LOGIN PAGE */
function kts_login_stylesheet()
{
	wp_enqueue_style('custom-login', get_stylesheet_directory_uri() . '/css/login.css');
}
add_action('login_enqueue_scripts', 'kts_login_stylesheet');


/* ENQUEUE SCRIPTS AND STYLES IN ADMIN */
function kts_admin_css($hook)
{
	wp_enqueue_style('admin-css', get_template_directory_uri() . '/css/admin.css');

	//echo '<p style="text-align:center;">' . $hook . '</p>'; // identify correct $hook
}
add_action('admin_enqueue_scripts', 'kts_admin_css');


/* ADD MENU ITEMS PROGRAMMATICALLY */
function bedrock_modify_menu($items, $args)
{
	if ($args->theme_location === 'menu-1') {
		if (is_user_logged_in()) {
			$new_item = '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="' . esc_url(wp_logout_url()) . '"><span>Log Out</span></a></li>';
			$items = $items . $new_item;
		} else {
			$new_item = '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="' . esc_url(wp_login_url()) . '"><span>Log In</span></a></li>';
			$get_btn = '<li class="menu-item menu-item-type-post_type menu-item-object-page get-btn"><a href="https://www.classicpress.net/download?ref=directory"><span>Get ClassicPress</span></a></li>';
			$items = $items . $new_item . $get_btn;
		}
	}
	return $items;
}
add_filter('wp_nav_menu_items', 'bedrock_modify_menu', 10, 2);

/**
 * Register sidebars
 * 
 * If you do not wish to have either or both sidebars, remove any
 * widgets from the relevant sidebar(s) and then comment out the same
 * sidebar(s) below
 */
function bedrock_register_sidebars()
{
	register_sidebar(array(
		'id' => 'sidebar-1',
		'name' => __('Left Sidebar', 'classicpress'),
		'description' => __('The first (primary) sidebar. Add widgets here.', 'classicpress'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'id' => 'sidebar-2',
		'name' => __('Right Sidebar', 'classicpress'),
		'description' => __('The second (secondary) sidebar. Add widgets here.', 'classicpress'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	));
}
add_action('widgets_init', 'bedrock_register_sidebars');


# TIME FORMATTING HELPER FUNCTION
# https://stackoverflow.com/questions/20288789/php-date-with-timezone
function kts_ts2time($timestamp, $timezone)
{ // unix time, timezone
	$date = new DateTime();
	$date->setTimestamp($timestamp);
	$date->setTimezone(new DateTimeZone($timezone));
	return $date->format('l, F jS, Y \a\t g:ia');
}

/**
 * Remove useless stuff from head and elsewhere.
 */
require get_template_directory() . '/inc/disable-remove.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into ClassicPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Limit author bio HTML tags
 */
add_filter('pre_user_description', 'classicpress_whitelist_tags_in_bio');
function classicpress_whitelist_tags_in_bio($description)
{
	$allowed_tags = array(
		'strong' => array(),
		'em' => array(),
		'b' => array(),
		'i' => array(),
	);
	$description = wp_kses($description, $allowed_tags);

	return $description;
}

/** 
 * Search shortcode
 */
add_shortcode('search-form', 'classicpress_search_form');
function classicpress_search_form()
{
	return '<form action="/" method="get" class="searchandfilter">
	<div>
		<ul>
			<li><label for="ofsearch" class="screen-reader-text">Search</label><input type="text" id="ofsearch" name="s" placeholder="Search…" value="" required="required"></li>
			<li><select class="postform" name="post_types">
					<option class="level-0" value="plugin,theme">All Software</option>
					<option class="level-0" value="plugin">Plugins</option>
					<option class="level-0" value="theme">Themes</option>
				</select></li>
			<li>
				<input type="submit" value="Search">
			</li>
		</ul>
	</div>
</form>';
}

/**
 * Add body class when logged in developer visits his/her profile
 */
add_filter(
	'body_class',
	function ($classes) {
		if (is_author()) {
			$post = get_queried_object();
			$user = wp_get_current_user();

			if ($user->ID == $post->ID) {
				$classes[] = 'developer-visit';
			}
		}

		return $classes;
	}
);

/** 
 * Make all external links nofollow to prevent spam
 */
add_filter('the_content', 'add_nofollow_external_links', 13);
function add_nofollow_external_links($content)
{
	return preg_replace_callback('/<a[^>]+/', 'classicpress_nofollow_callback', $content);
};

function classicpress_nofollow_callback($matches)
{
	$link = $matches[0];
	$site_link = get_bloginfo('url');

	preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*/i', $link, $result);

	if (isset($result['href'][0])) {
		if (filter_var($result['href'][0], FILTER_VALIDATE_URL) && is_singular(array('plugin', 'theme'))) {
			if (strpos($link, 'rel') === false) {
				$link = preg_replace("%(href=\S(?!$site_link))%i", 'rel="nofollow noreferrer noopener external ugc" $1', $link);
			} elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
				$link = preg_replace('/rel=(?!nofollow|noffolow)\S*/i', 'rel="nofollow noreferrer noopener external ugc"', $link);
			}
		}
	}
	return $link;
};

/**
 * Remove H1 headings from the_content, which is the README.md content to improve accessability/SEO
 */
add_filter('the_content', 'remove_h1_tags');

function remove_h1_tags($content) {
    global $post;
    $post_type = get_post_type($post);

    if ($post_type == 'plugin' || $post_type == 'theme') {
        $content = preg_replace('#<h1[^>]*>.*?</h1>#si', '', $content);
    }
    
    return $content;
}

/**
 * Developer profile bio label
 */
add_filter('gettext', 'dev_bio_label');
function dev_bio_label($text)
{

	$text = str_ireplace('Share a little biographical information to fill out your profile. This may be shown publicly.', 'Fill out your bio. Up to 200 characters. Only <code>&lt;b&gt; &lt;strong&gt; &lt;i&gt; &lt;em&gt;</code> tags are allowed.', $text);

	return $text;
}

/**
 * Add pending review counters to the menu
 */
add_action('admin_menu', function () {

	global $menu;

	$theme_args = array(
		'numberposts'   => -1,
		'post_type'     => array('theme'),
		'fields' => 'ids',
		'no_found_rows' => true,
		'post_status'   => array('draft'),

	);
	$plugin_args = array(
		'numberposts'   => -1,
		'post_type'     => array('plugin'),
		'fields' => 'ids',
		'no_found_rows' => true,
		'post_status'   => array('draft'),

	);
	$theme_drafts = count(get_posts($theme_args));
	$plugin_drafts = count(get_posts($plugin_args));

	if ($theme_drafts > 0) {

		$menu_item = wp_list_filter(
			$menu,
			array(2 => 'edit.php?post_type=theme')
		);

		if (!empty($menu_item)) {
			$menu_item_position = key($menu_item);
			$menu[$menu_item_position][0] .= ' <span class="awaiting-mod">' . $theme_drafts . '</span>';
		}
	}

	if ($plugin_drafts > 0) {

		$menu_item = wp_list_filter(
			$menu,
			array(2 => 'edit.php?post_type=plugin')
		);
		if (!empty($menu_item)) {
			$menu_item_position = key($menu_item);
			$menu[$menu_item_position][0] .= ' <span class="awaiting-mod">' . $plugin_drafts . '</span>';
		}
	}
});

/**
 * Featured plugins on the homepage
 */
function get_random_plugin_posts()
{
	// Try to get data from the transient
	$cached_posts = get_transient('random_plugin_posts');

	// If the transient doesn't exist or has expired
	if (false === $cached_posts) {
		// Query to get 4 random plugin posts
		$args = array(
			'post_type' => 'plugin',
			'posts_per_page' => 4,
			'orderby' => 'rand'
		);

		$plugin_query = new WP_Query($args);

		// If the query has posts
		if ($plugin_query->have_posts()) {
			$cached_posts = array();

			// Loop through the posts and store the IDs in the transient
			while ($plugin_query->have_posts()) {
				$plugin_query->the_post();

				$cached_posts[] = get_the_ID();
			}

			// Store the IDs in a transient, set to expire after 7 days
			set_transient('random_plugin_posts', $cached_posts, 7 * DAY_IN_SECONDS);

			// Reset the query
			wp_reset_postdata();
		}
	}

	// Output the posts
	if (!empty($cached_posts)) {
		echo '<ul class="software-grid" id="home-featured-themes">';
		foreach ($cached_posts as $post_id) {
			$post = get_post($post_id);

			$title = get_the_title($post_id);
			$permalink = get_permalink($post_id);
			$author = get_author_posts_url($post->post_author);
			$author_name = get_the_author_meta('display_name', $post->post_author);

			echo '<li><div class="software-item-info">';
			echo '<h3 class="h3"><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h3>';
			echo '<span class="software-author-link"><a href="' . esc_url($author) . '">' . esc_html($author_name) . '</a></span><p>';
			kts_excerpt_fallback($post);
			echo '</p></div></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No plugins found.</p>';
	}
}


/**
 * Featured themes on the homepage
 */
function get_random_theme_posts()
{
	// Try to get data from the transient
	$cached_posts = get_transient('random_theme_posts');

	// If the transient doesn't exist or has expired
	if (false === $cached_posts) {
		// Query to get 4 random theme posts
		$args = array(
			'post_type' => 'theme',
			'posts_per_page' => 4,
			'orderby' => 'rand'
		);

		$theme_query = new WP_Query($args);

		// If the query has posts
		if ($theme_query->have_posts()) {
			$cached_posts = array();

			// Loop through the posts and store the IDs in the transient
			while ($theme_query->have_posts()) {
				$theme_query->the_post();

				$cached_posts[] = get_the_ID();
			}

			// Store the IDs in a transient, set to expire after 7 days
			set_transient('random_theme_posts', $cached_posts, 7 * DAY_IN_SECONDS);

			// Reset the query
			wp_reset_postdata();
		}
	}

	// Output the posts
	if (!empty($cached_posts)) {
		echo '<ul class="software-grid" id="home-featured-themes">';
		foreach ($cached_posts as $post_id) {
			$post = get_post($post_id);

			$title = get_the_title($post_id);
			$permalink = get_permalink($post_id);
			$author = get_author_posts_url($post->post_author);
			$author_name = get_the_author_meta('display_name', $post->post_author);

			echo '<li><div class="software-item-info">';
			echo '<h3 class="h3"><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h3>';
			echo '<span class="software-author-link"><a href="' . esc_url($author) . '">' . esc_html($author_name) . '</a></span><p>';
			kts_excerpt_fallback($post);
			echo '</p></div></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No themes found.</p>';
	}
}

/**
 * Total counts and shortcodes
 */
function get_total_plugin_count() {
    // Try to get data from the transient
    $count = get_transient('total_plugin_count');

    // If the transient doesn't exist or has expired
    if (false === $count) {
        // Query to get total plugin posts count
        $args = array(
            'post_type' => 'plugin',
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);
        $count = $query->found_posts;

        // Store the count in a transient, set to expire after 12 hours
        set_transient('total_plugin_count', $count, 12 * HOUR_IN_SECONDS);
    }

    return $count;
}

function get_total_theme_count() {
    // Try to get data from the transient
    $count = get_transient('total_theme_count');

    // If the transient doesn't exist or has expired
    if (false === $count) {
        // Query to get total theme posts count
        $args = array(
            'post_type' => 'theme',
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);
        $count = $query->found_posts;

        // Store the count in a transient, set to expire after 12 hours
        set_transient('total_theme_count', $count, 12 * HOUR_IN_SECONDS);
    }

    return $count;
}

function total_plugin_count_shortcode() {
    return get_total_plugin_count();
}
add_shortcode('total_plugin_count', 'total_plugin_count_shortcode');

function total_theme_count_shortcode() {
    return get_total_theme_count();
}
add_shortcode('total_theme_count', 'total_theme_count_shortcode');

/**
 * Human readable numbers
 */
function human_readable_number($number) {
    if ($number >= 1000000000) {
        return number_format($number / 1000000000, 1) . 'B';
    } elseif ($number >= 1000000) {
        return number_format($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return number_format($number / 1000, 1) . 'K';
    } else {
        return $number;
    }
}

/**
 * Remove <a> wrapped around <img>, which link to non-existent files 
 */
add_filter('the_content', 'remove_link_from_images');

function remove_link_from_images($content) {
    $pattern = '/<a(.*?)><img(.*?)><\/a>/i';
    $replacement = '<img$2>';

    $content = preg_replace($pattern, $replacement, $content);

    return $content;
}