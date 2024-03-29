<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bedrock
 */

?>

<section>
	<header>
		<h1><?php esc_html_e( 'Nothing Found', 'classicpress' ); ?></h1>
	</header><!-- .page-header -->

	<div>
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'classicpress' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'classicpress' ); ?></p>
			<?php
			echo do_shortcode('[search-form]');

		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps a search might help.', 'classicpress' ); ?></p>
			<?php
			echo do_shortcode('[search-form]');

		endif;
		?>
	</div>
</section>
