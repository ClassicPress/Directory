<?php if ( ! defined( 'ABSPATH' ) ) { die(); }
/*
 * Plugin Name: Show/Hide Admin Bar
 * Plugin URI: https://timkaye.org
 * Description: Shows and hides the adminbar on hover
 * Version: 1.0
 * Author: Tim Kaye
 * License: MIT
*/

function kts_adminbar() {
	add_theme_support( 'admin-bar', array(
		'callback' => '__return_false' // prevents pushing header down
	) );
}
add_action( 'after_setup_theme', 'kts_adminbar' );

function kts_override_admin_bar_css() {
	if ( ! is_admin_bar_showing() ) {
		return;
	} ?>

	<style>
	#wpadminbar,
	#wpadminbar .qm-alert,
	#wpadminbar .qm-strict,
	#wpadminbar .qm-deprecated,
	#wpadminbar .qm-notice,
	#wpadminbar .qm-expensive,
	#wpadminbar .qm-error,
	#wpadminbar .qm-warning {
		overflow: hidden;
		background: transparent;
		transition: 600ms 2s;
	}		
	#wpadminbar .ab-sub-wrapper,
	#wpadminbar ul,
	#wpadminbar li,
	#wpadminbar a,
	#wpadminbar .ab-item,
	#wpadminbar .ab-empty-item,
	#wpadminbar .ab-label,
	#wpadminbar .ab-icon,
	#wpadminbar .ab-item::before,
	#wpadminbar .ab-icon::before {
		color: transparent !important;
		transition: 600ms 2s;
	}
	#wpadminbar .admin-bar-search,
	#wpadminbar #wp-admin-bar-wp-logo > .ab-item .cp-logo,
	#wpadminbar #wp-admin-bar-my-account.with-avatar > a img {
		visibility: hidden;
		transition: 400ms 1.8s;
	}
	#wpadminbar:hover,
	#wpadminbar:hover .qm-alert,
	#wpadminbar:hover .qm-strict,
	#wpadminbar:hover .qm-deprecated,
	#wpadminbar:hover .qm-notice,
	#wpadminbar:hover .qm-expensive,
	#wpadminbar:hover .qm-error,
	#wpadminbar:hover .qm-warning {
		overflow: visible;
		background: #23282d;
		transition: 0s;
	}
	#wpadminbar:hover .qm-alert {
		background: #f60;
	}
	#wpadminbar:hover .qm-strict,
	#wpadminbar:hover .qm-deprecated,
	#wpadminbar:hover .qm-notice {
		background: #740;
	}
	#wpadminbar:hover .qm-expensive {
		background: #915700;
	}
	#wpadminbar:hover .qm-error,
	#wpadminbar:hover .qm-warning {
		background: #c00;
	}
	#wpadminbar:hover .ab-sub-wrapper,
	#wpadminbar:hover ul,
	#wpadminbar:hover li,
	#wpadminbar:hover a,
	#wpadminbar:hover .ab-item,
	#wpadminbar:hover .ab-empty-item,
	#wpadminbar:hover .ab-label,
	#wpadminbar:hover .ab-icon,
	#wpadminbar:hover .ab-item::before,
	#wpadminbar:hover .ab-icon::before {
		color: #eee !important;
		transition: 0s;
	}
	#wpadminbar:hover .admin-bar-search,
	#wpadminbar:hover #wp-admin-bar-wp-logo > .ab-item .cp-logo,
	#wpadminbar:hover #wp-admin-bar-my-account.with-avatar > a img {
		visibility: visible;
		transition: 0s;
	}
	#wpadminbar .ab-sub-wrapper,
	#wpadminbar ul,
	#wpadminbar li,
	#wpadminbar a:hover,
	#wpadminbar .ab-item:hover,
	#wpadminbar .ab-empty-item:hover,
	#wpadminbar .ab-label:hover,
	#wpadminbar .ab-icon:hover,
	#wpadminbar .ab-item:hover::before,
	#wpadminbar .ab-icon:hover::before {
		color: #00b9eb !important;
	}
	</style> <?php
}
add_action( 'wp_head', 'kts_override_admin_bar_css' );

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
 * Remove New, dashboard and comments menus
 */
function remove_menu_items_for_contributor($wp_admin_bar) {
    if (current_user_can('contributor')) {
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('comments');
		$wp_admin_bar->remove_node('dashboard');
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