<?php
/**
 * The template for displaying author archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */
$author = get_queried_object();
$big = 999999999; // need an unlikely integer

$cached_count = kts_get_user_stat( $author-> ID );
get_header();
?>

	<div id="primary">
		<main id="main">

			<header>
				<h1><span class="brand"><?php echo get_avatar( $author->ID, 32 ); ?></span><?php echo esc_html( $author->display_name ); ?></h1>
				<div class="developer-profile">
					<div class="dev-bio"><?php echo esc_html( get_user_meta($author->ID, 'description', true) ); ?></div>
					<div class="dev-url"><a href="<?php echo esc_url( get_user_meta($author->ID, 'website', true) ); ?>" rel="noffolow noopener noreferrer" target="_blank"><?php echo esc_url( get_user_meta($author->ID, 'website', true) ); ?></a></div>
				</div>
			</header>

			<?php
			if ( class_exists( 'SearchAndFilter' ) ) {
				echo do_shortcode( '[searchandfilter fields="search,post_types" post_types="plugin,theme,snippet"]' );
			}
			?>

			<div class="clear"></div>

			<div id="tabs" class="ui-tabs">
				<div id="ui-tabs-nav" class="ui-tabs-nav" role="tablist">
					<?php $active_tab = kts_render_user_tabs ( $cached_count ); ?>
				</div><!-- #ui-tabs-nav -->

				<div id="tabs-1" class="ui-panel" role="tabpanel"<?php echo $active_tab === 'plugin' ? '' : ' hidden' ?>>
					<?php if ($cached_count > 0) :?>
					<ul class="software-grid">

					<?php
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
					$plugin_args = array(
						'post_type'		=> 'plugin',
						'post_status'	=> 'publish',
						'author'		=> $author->ID,
						'paged'			=> $paged,
					);
					$plugin_post_loop = new WP_Query( $plugin_args );

					if ( $plugin_post_loop->have_posts() ) :

						/* Start the Loop */
						while ( $plugin_post_loop->have_posts() ) :
						?>

							<li>

								<?php
								$plugin_post_loop->the_post();

								get_template_part( 'template-parts/content', get_post_type() );
								?>
							</li>

						<?php
						endwhile;

					endif;
					?>
				
					</ul>
					
					<nav class="navigation pagination">
						<h2 class="screen-reader-text"><?php _e( 'Plugins navigation', 'classicpress' ); ?></h2>
						<div class="nav-links">

						<?php
						echo paginate_links( array(
							'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'classicpress' ) . ' </span>',
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'current' => max(
								1,
								get_query_var( 'paged' ),
							),
							'total' => $plugin_post_loop->max_num_pages,
						) );
						?>

						</div>
					</nav>

					

					<?php wp_reset_postdata(); ?>
					
					<?php else: ?>
						<p>Empty</p>
					<?php endif; ?>

				</div><!-- #tabs-1 -->

				<div id="tabs-2" class="ui-panel" role="tabpanel"<?php echo $active_tab === 'theme' ? '' : ' hidden' ?>>
					<ul class="software-grid">

					<?php
					$theme_args = array(
						'post_type'		=> 'theme',
						'post_status'	=> 'publish',
						'author'		=> $author->ID,
						'numberposts'	=> -1,
					);
					$theme_post_loop = new WP_Query( $theme_args );

					if ( $theme_post_loop->have_posts() ) :

						/* Start the Loop */
						while ( $theme_post_loop->have_posts() ) :
						?>

							<li>
								<?php
								$theme_post_loop->the_post();

								get_template_part( 'template-parts/content', get_post_type() );
								?>
							</li>

						<?php
						endwhile;

					endif;
					?>
				
					</ul>
					
					<nav class="navigation pagination">
						<h2 class="screen-reader-text"><?php _e( 'Themes navigation', 'classicpress' ); ?></h2>
						<div class="nav-links">

						<?php
						echo paginate_links( array(
							'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'classicpress' ) . ' </span>',
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'current' => max(
								1,
								get_query_var( 'paged' ),
							),
							'total' => $theme_post_loop->max_num_pages,
						) );
						?>

						</div>
					</nav>

					<?php wp_reset_postdata(); ?>

				</div><!-- #tabs-2 -->

				<div id="tabs-3" class="ui-panel" role="tabpanel"<?php echo $active_tab === 'snippet' ? '' : ' hidden' ?>>
					<ul class="software-grid">

					<?php
					$snippet_args = array(
						'post_type'		=> 'snippet',
						'post_status'	=> 'publish',
						'author'		=> $author->ID,
						'numberposts'	=> -1,
					);
					$snippet_post_loop = new WP_Query( $snippet_args );

					if ( $snippet_post_loop->have_posts() ) :

						/* Start the Loop */
						while ( $snippet_post_loop->have_posts() ) :
						?>

							<li>
								<?php
								$snippet_post_loop->the_post();

								get_template_part( 'template-parts/content', get_post_type() );
								?>
							</li>

						<?php
						endwhile;

					endif;
					?>
				
					</ul>
					
					<nav class="navigation pagination">
						<h2 class="screen-reader-text"><?php _e( 'Snippets navigation', 'classicpress' ); ?></h2>
						<div class="nav-links">

						<?php
						echo paginate_links( array(
							'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'classicpress' ) . ' </span>',
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'current' => max(
								1,
								get_query_var( 'paged' ),
							),
							'total' => $snippet_post_loop->max_num_pages,
						) );
						?>

						</div>
					</nav>

					<?php wp_reset_postdata(); ?>

				</div><!-- #tabs-3 -->

			</div>

			<div class="dialog-container" id="my-dialog" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-description" aria-hidden="true">
				<div class="dialog-overlay" data-a11y-dialog-hide></div>
				<div class="dialog-content" role="document">
					<button id="top-close" data-a11y-dialog-hide class="dialog-close" aria-label="Close this dialog window">&times;</button>

					<h2 id="my-dialog-title"></h2>

					<div id="my-dialog-description"></div>
					<button id="bottom-close" data-a11y-dialog-hide aria-label="Close this dialog window">Close</button>
				</div>
			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
