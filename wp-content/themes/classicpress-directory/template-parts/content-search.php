<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bedrock
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<?php the_title( '<h2  class="h3"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );	?>
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

	<footer class="entry-footer">
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
</article><!-- #post-<?php the_ID(); ?> -->
