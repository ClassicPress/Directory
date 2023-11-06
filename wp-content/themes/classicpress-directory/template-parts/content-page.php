<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<?php the_title( '<h1>', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php bedrock_post_thumbnail(); ?>

	<div>
		<?php
		the_content();

		if ( is_page( 'software-submission-form' ) ) {
			kts_render_software_submit_form();
		}
		elseif ( is_page( 'image-submit-form' ) ) {
			xsx_render_image_submit_form();
		}
		elseif ( is_page( 'code-review-response-form' ) ) {
			kts_render_review_response_form();
		}
		elseif ( is_page( 'contact-us-form' ) ) {
			kts_render_contact_us_form();
		}
		elseif ( is_page( 'developers' ) ) {
			kts_developers_alphabet();
			kts_list_developers();
		}

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'classicpress' ),
			'after'  => '</div>',
		) );
		?>
	</div>

</article><!-- #post-<?php the_ID(); ?> -->
