<?php
/* Template Name: Homepage */

get_header();
?>

	<div id="primary" class="home-page">
		<main id="main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'home' );

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
