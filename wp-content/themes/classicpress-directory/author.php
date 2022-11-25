<?php
/**
 * The template for displaying author archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */
$author = get_queried_object();
$big = 999999999; // need an unlikely integer

$plugins_count  = count_user_posts( $author-> ID, 'plugin', true );
$themes_count   = count_user_posts( $author-> ID, 'theme', true );
$snippets_count = count_user_posts( $author-> ID, 'snippet', true );

get_header();
?>

	<div id="primary">
		<main id="main">

			<header>
				<h1><span class="brand"><?php echo get_avatar( $author->ID, 32 ); ?></span><?php echo esc_html( $author->display_name ); ?></h1>
			</header>

			<?php
			if ( class_exists( 'SearchAndFilter' ) ) {
				echo do_shortcode( '[searchandfilter fields="search,post_types,category,post_tag" post_types="plugin,theme,snippet"]' );
			}
			?>

			<div class="clear"></div>

			<div id="tabs" class="ui-tabs">
				<div id="ui-tabs-nav" class="ui-tabs-nav" role="tablist">

					<button id="ui-id-1" class="ui-button ui-state-active plugins" aria-controls="tabs-1" aria-selected="true" role="tab" tabindex="0">Plugins (<?php echo $plugins_count; ?>)</button>

					<button id="ui-id-2" class="ui-button" aria-controls="tabs-2" aria-selected="false" role="tab" tabindex="-1">Themes (<?php echo $themes_count; ?>)</button>

					<button id="ui-id-3" class="ui-button" aria-controls="tabs-3" aria-selected="false" role="tab" tabindex="-1">Snippets (<?php echo $snippets_count; ?>)</button>

				</div><!-- #ui-tabs-nav -->

				<div id="tabs-1" class="ui-panel" role="tabpanel">
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
						<h2 class="screen-reader-text">Plugins navigation</h2>
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

				</div><!-- #tabs-1 -->

				<div id="tabs-2" class="ui-panel" role="tabpanel" hidden>
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
						<h2 class="screen-reader-text">Themes navigation</h2>
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

				<div id="tabs-3" class="ui-panel" role="tabpanel" hidden>
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
						<h2 class="screen-reader-text">Snippets navigation</h2>
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
