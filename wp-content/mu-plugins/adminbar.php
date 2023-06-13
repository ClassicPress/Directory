<?php if ( ! defined( 'ABSPATH' ) ) { die(); }
/*
 * Plugin Name: Show/Hide Admin Bar
 * Plugin URI: https://timkaye.org
 * Description: Shows and hides the adminbar on hover
 * Version: 1.0
 * Author: Tim Kaye
 * License: MIT
*/

/**
 * Display author's public profile link in admin bar
 */
function add_author_page_menu_item($wp_admin_bar) {
    if (current_user_can_for_blog(get_current_blog_id(), 'edit_posts')) {
        $author_id = get_current_user_id();
        $author_url = get_author_posts_url($author_id);

        $args = array(
            'id'    => 'author_page',
			'title' => 'My Public Profile',
            'href'  => $author_url,
            'meta'  => array(
                'class' => 'author-page',
            ),
        );

        $wp_admin_bar->add_node($args);

        $wp_admin_bar->add_menu(array(
            'id'     => 'forms',
            'title'  => 'Forms',
        ));

        $form1_url = home_url('/software-submission-form');
        $wp_admin_bar->add_menu(array(
            'id'     => 'form_1',
            'title'  => 'Software Submission Form',
            'href'   => $form1_url,
            'parent' => 'forms',
        ));

        $form2_url = home_url('/code-review-response-form');
        $wp_admin_bar->add_menu(array(
            'id'     => 'form_2',
            'title'  => 'Code Review Response Form',
            'href'   => $form2_url,
            'parent' => 'forms',
        ));

		$form3_url = home_url('/contact-us-form');
        $wp_admin_bar->add_menu(array(
            'id'     => 'form_3',
            'title'  => 'Contact Us Form',
            'href'   => $form3_url,
            'parent' => 'forms',
        ));
    }
}
add_action('admin_bar_menu', 'add_author_page_menu_item', 999);

/**
 * Remove New, dashboard, site name, logo, and comments menus
 */
function remove_menu_items_for_contributor($wp_admin_bar) {
    if (current_user_can('contributor')) {
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('comments');
		$wp_admin_bar->remove_node('dashboard');
		$wp_admin_bar->remove_node('wp-logo');
		$wp_admin_bar->remove_node('site-name');
    }
}
add_action('admin_bar_menu', 'remove_menu_items_for_contributor', 999);

/**
 * Remove dashboard menu
 */
function remove_dashboard_menu() {
    if (current_user_can('contributor')) {
        remove_menu_page('index.php');
    }
}
add_action('admin_menu', 'remove_dashboard_menu');

/**
 * Show adminbar
 */
function show_admin_bar_on_frontend() {
    if (is_user_logged_in()) {
        show_admin_bar(true);
    }
}
add_action('after_setup_theme', 'show_admin_bar_on_frontend');