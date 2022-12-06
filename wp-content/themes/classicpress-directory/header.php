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
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'bedrock'); ?></a>

		<section class="home-hero-container">
			<div id="inner-header" class="outer-width">
				<span class="logo" role="banner">
					<a href="https://www.classicpress.net/" rel="home"><img src="https://www.classicpress.net/wp-content/themes/classicpress-susty-child/images/logo-white.svg" alt="ClassicPress logo" width="250"> <span class="screen-reader-text">Home</span></a>
				</span>

				<nav id="site-navigation" class="main-navigation nav--toggle-sub nav--toggle-small" aria-label="Main menu">
					<div class="menu-mainmenu-container">
						<ul id="general-menu" class="primary-menu menu">
							<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="https://www.classicpress.net/community/">Community</a>
								<ul class="sub-menu">
									<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-605"><a href="https://www.classicpress.net/blog/">Blog</a></li>
									<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/community/">Get Involved</a></li>
									<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://forums.classicpress.net">Forums</a></li>
									<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://forums.classicpress.net/c/governance/petitions/77">Petitions</a></li>
								</ul>
							</li>
							<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="https://www.classicpress.net/about/">About</a>
								<ul class="sub-menu">
									<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/about/">About ClassicPress</a></li>
									<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/roadmap/">Roadmap</a></li>
									<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://docs.classicpress.net/">Documentation</a></li>
									<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/brand-guidelines/">Brand Guidelines</a></li>
									<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/reasons-to-switch-to-classicpress-from-wordpress-4-9/">For WordPress Users</a></li>
									<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/governance/">Our Governance</a></li>
									<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/faq/">FAQs</a></li>
								</ul>
							</li>
							<li class="switchbutton donate"><a id="donate" href="https://www.classicpress.net/donate/">Donate</a></li>
							<li class="menu-item menu-item-type-post_type menu-item-object-page"><a id="get" href="https://www.classicpress.net/get-classicpress/">Get ClassicPress</a></li>
						</ul>
					</div>
				</nav><!-- #site-navigation -->

			</div>
		</section>

		<header id="masthead" class="masthead">
		<div id="search-form-wrapper" style="display: none;">
			<div class="search-form-flex">
				<div class="nav-search">
					<form action="/" method="get" class="searchandfilter">
						<div>
							<ul>
								<li><label for="ofsearch" class="screen-reader-text">Search</label><input type="text" id="ofsearch" name="s" placeholder="Search…" value="" required="required"></li>
								<li><select class="postform" name="post_types">
										<option class="level-0" value="plugin,theme,snippet">All Software</option>
										<option class="level-0" value="plugin">Plugins</option>
										<option class="level-0" value="theme">Themes</option>
										<option class="level-0" value="snippet">Snippets</option>
									</select></li>
								<li>
									<input type="submit" value="Search">
								</li>
							</ul>
						</div>
					</form>
				</div>
				<div class="close-search"><button type="button" title="Close search">Close</button></div>
			</div>
		</div>

		<script>
			(function($) {
				$( ".open-search a" ).click(function(e) {
					console.log('open');
					e.preventDefault();
					$( "#masthead-inner" ).slideUp();
					$( "#search-form-wrapper" ).slideDown();
					$( "#masthead" ).css('background', '#fff');
				});
				$( ".close-search button" ).click(function(e) {
					console.log('close');
					$( "#search-form-wrapper" ).slideUp();
					$( "#masthead-inner" ).slideDown();
					$( "#masthead" ).removeAttr('style');
				});
				
			})( jQuery );
		</script>

			<div id="masthead-inner" class="masthead-inner outer-width aligncenter">

				<div class="site-introduction">
					<h1 class="masthead-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php _e('Directory', 'classicpress'); ?></a></h1>
				</div>

				<div class="menu-toggles">
					<button id="menu-toggle" class="menu-toggle" type="button" aria-haspopup="true" aria-controls="primary-menu" aria-expanded="false" tabindex="0">
						<svg class="icon icon-menu-toggle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0z" fill="none" />
							<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
						</svg>
						<span id="menu-toggle-text" class="menu-toggle-text screen-reader-text"><?php esc_html_e('Primary Menu', 'bedrock'); ?></span>
					</button>

					<button id="menu-toggle-close" class="menu-toggle" type="button" aria-haspopup="true" aria-controls="primary-menu" aria-expanded="true" tabindex="0" hidden>
						<svg class="icon icon-menu-toggle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
							<path d="M0 0h24v24H0z" fill="none" />
						</svg>
						<span id="menu-toggle-close-text" class="menu-toggle-text screen-reader-text"><?php esc_html_e('Close menu', 'bedrock'); ?></span>
					</button>
				</div><!-- .menu-toggles -->

				<div id="primary-menu" class="primary-menu" role="navigation" aria-labelledby="nav-label">
					<span id="primary-label" class="screen-reader-text">Primary Menu</span>

					<div class="main-navigation-wrapper" id="main-navigation-wrapper">
						<nav id="primary-menu-header" class="main-navigation">

							<?php wp_nav_menu(['theme_location' => 'menu-1']); ?>

						</nav><!-- #menu-header -->
					</div><!-- .main-navigation-wrapper -->
				</div>

			</div><!-- #masthead-inner -->

			<div id="main-menu" role="navigation" aria-labelledby="nav-label">
				<span id="nav-label" class="screen-reader-text">Primary Menu</span>

				<div class="main-navigation-wrapper" id="main-navigation-wrapper">
					<nav id="menu-header" class="main-navigation">

						<?php wp_nav_menu(['theme_location' => 'menu-1']); ?>

					</nav><!-- #menu-header -->
				</div><!-- .main-navigation-wrapper -->
			</div>

		</header><!-- #masthead -->

		<div id="outer-content" class="outer-content">

			<?php get_sidebar(); ?>

			<div id="content" class="content">