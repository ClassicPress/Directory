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
