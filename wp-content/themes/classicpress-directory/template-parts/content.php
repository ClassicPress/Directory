<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bedrock
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	if ( is_singular() ) :
	?>

		<header>
			<?php the_title( '<h1>', '</h1>' ); ?>
		</header>

		<div id="software" class="software">
			<h2 class="h5 description">
			<?php
			switch (get_post_type()) {
				case 'plugin':
					esc_html_e( 'Plugin Description', 'classicpress' );
					break;
				case 'theme':
					esc_html_e( 'Theme Description', 'classicpress' );
					break;
				case 'snippet':
					esc_html_e( 'Snippet Description', 'classicpress' );
					break;
			}
			?>
			</h2>
			<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'classicpress' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) );

			if ( $post->post_type === 'plugin' ) {
			?>

				<h2 class="h5"><?php _e( 'Categories', 'classicpress' ); ?></h2>
				<?php the_category(); ?>

			<?php
			}
			elseif ( $post->post_type === 'snippet' ) {
			?>

				<h2 class="h5"><?php _e( 'Tags', 'classicpress' ); ?></h2>
				<?php the_tags( '' ); ?>

			<?php
			}
			?>

			<h2 class="h5"><?php _e( 'Developer', 'classicpress' ); ?></h2>
			<?php the_author_posts_link(); ?>

			<h2 class="h5"><?php _e( 'Current Version', 'classicpress' ); ?></h2>
			<?php echo esc_html( get_post_meta( $post->ID, 'current_version', true ) ); ?>

			<h2 class="h5"><?php _e( 'Minimum ClassicPress Version', 'classicpress' ); ?></h2>
			<?php echo esc_html( get_post_meta( $post->ID, 'requires_cp', true ) ) . '.0'; ?>

			<h2 class="h5"><?php _e( 'Download Link', 'classicpress' ); ?></h2>
			<?php echo make_clickable( esc_url( get_post_meta( $post->ID, 'download_link', true ) ) ); ?>

		</div>
	
	<?php
	else :
	?>
		
		<header>
			<?php the_title( '<h2  class="h3"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );	?>
			<span class="software-author-link"><?php echo get_the_author_posts_link(); ?></span>
		</header>

		<p><?php kts_excerpt_fallback( $post ); ?></p>
		
		<div class="software-item-meta">
			<div class="meta-item left">
				<a href="<?php echo esc_url( get_post_meta( $post->ID, 'download_link', true ) ); ?>" title="<?php _e( 'Download', 'classicpress' ) . ' ' . esc_attr( $post->post_title ); ?>" target="_blank" rel="noopener noreferrer">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/images/download.svg' ); ?>" alt="download" width="18" height="18">
					<span><?php _e( 'Download', 'classicpress' ); ?></span>
				</a>
			</div>
			<div class="meta-item right">
				<a href="#info-<?php echo absint( $post->ID ); ?>" title="<?php _e( 'More information about', 'classicpress' ); ?> <?php echo esc_attr( $post->post_title ); ?>" class="info-button" data-info="info-<?php echo absint( $post->ID ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/images/info.svg' ); ?>" alt="download" width="18" height="18">
					<span><?php _e( 'More Info', 'classicpress' ); ?></span>
				</a>
			</div>
		</div>
		
	<?php
	endif;

	if ( ! is_singular() ) {
	?>

	<footer>

		<?php // Add update button if author archive is of the current user
		kts_render_software_update_link_form( $post );
		?>

		<div id="info-<?php echo absint( $post->ID ); ?>" class="info-panel" hidden>

			<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'classicpress' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
			get_the_title()
			) );
			?>

		</div>
	</footer>

	<?php
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->
