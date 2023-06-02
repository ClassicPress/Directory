<?php

/**
 * The header for our theme
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div id="page">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'classicpress'); ?></a>

		<header id="masthead" class="masthead">
			<div id="search-form-wrapper" style="display: none;">
				<div class="search-form-flex">
					<div class="nav-search">
						<form action="<?php echo esc_url(home_url()); ?>" method="get" class="searchandfilter">
							<div>
								<ul>
									<li><label for="ofsearch" class="screen-reader-text"><?php _e('Search', 'classicpress'); ?></label><input type="text" id="ofsearch" name="s" placeholder="<?php esc_attr_e('Search…', 'classicpress'); ?>" value="" required="required"></li>
									<li><select class="postform" name="post_types">
											<option class="level-0" value="<?php echo esc_attr('plugin,theme'); ?>"><?php _e('All Software', 'classicpress'); ?></option>
											<option class="level-0" value="<?php echo esc_attr('plugin'); ?>"><?php _e('Plugins', 'classicpress'); ?></option>
											<option class="level-0" value="<?php echo esc_attr('theme'); ?>"><?php _e('Themes', 'classicpress'); ?></option>
										</select></li>
									<li>
										<input type="submit" value="<?php echo esc_attr('Search'); ?>" id="search-submit">
									</li>
								</ul>
							</div>
						</form>
					</div>
					<div class="close-search"><button type="button" title="<?php esc_attr_e('Close search', 'classicpress'); ?>"><?php _e('Close', 'classicpress'); ?></button></div>
				</div>
			</div>

			<div id="masthead-inner" class="masthead-inner outer-width aligncenter">

				<div class="site-introduction">
					<span class="logo" role="banner">
						<a href="<?php echo esc_url(home_url()); ?>" rel="home" title="<?php esc_attr_e('Directory home', 'classicpress'); ?>"><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/logo-white.svg'); ?>" alt="<?php esc_attr_e('ClassicPress logo', 'classicpress'); ?>" width="250"> <span class="screen-reader-text"><?php _e('Directory Home', 'classicpress'); ?></span></a>
					</span>
				</div>

				<div class="menu-toggles">
					<button id="menu-toggle" class="menu-toggle" type="button" aria-haspopup="true" aria-controls="primary-menu" aria-expanded="false" tabindex="0">
						<svg class="icon icon-menu-toggle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0z" fill="none" />
							<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
						</svg>
						<span id="menu-toggle-text" class="menu-toggle-text screen-reader-text"><?php esc_html_e('Primary Menu', 'classicpress'); ?></span>
					</button>

					<button id="menu-toggle-close" class="menu-toggle" type="button" aria-haspopup="true" aria-controls="primary-menu" aria-expanded="true" tabindex="0" hidden>
						<svg class="icon icon-menu-toggle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
							<path d="M0 0h24v24H0z" fill="none" />
						</svg>
						<span id="menu-toggle-close-text" class="menu-toggle-text screen-reader-text"><?php esc_html_e('Close menu', 'classicpress'); ?></span>
					</button>
				</div><!-- .menu-toggles -->

				<div id="primary-menu" class="primary-menu" role="navigation" aria-labelledby="nav-label">
					<span id="primary-label" class="screen-reader-text"><?php _e('Primary Menu', 'classicpress'); ?></span>

					<div class="main-navigation-wrapper" id="main-navigation-wrapper">
						<nav id="primary-menu-header" class="main-navigation">

							<?php wp_nav_menu(array(
								'menu' => 'Main Menu',
								'theme_location' => 'menu-1',
								'link_before' => '<span>',
								'link_after' => '</span>'
							)); ?>

						</nav><!-- #menu-header -->
					</div><!-- .main-navigation-wrapper -->
				</div>

			</div><!-- #masthead-inner -->

			<div id="main-menu" role="navigation" aria-labelledby="nav-label">
				<span id="nav-label" class="screen-reader-text"><?php _e('Primary Menu', 'classicpress'); ?></span>

				<div class="main-navigation-wrapper" id="main-navigation-wrapper">
					<nav id="menu-header" class="main-navigation">

						<?php wp_nav_menu(array(
							'menu' => 'Main Menu',
							'theme_location' => 'menu-1'
						)); ?>

					</nav><!-- #menu-header -->
				</div><!-- .main-navigation-wrapper -->
			</div>

		</header><!-- #masthead -->

		<div id="outer-content" class="outer-content">

			<?php get_sidebar(); ?>

			<div id="content" class="content">