<?php
/**
 * Template part for displaying homepage
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<section class="featured-software">
    <?php
			echo "<h2>Featured Plugins</h2>";
			get_random_plugin_posts();
			echo "<hr>";
			echo "<h2>Featured Themes</h2>";
			get_random_theme_posts();
	?>
    </section>
    <section class="home-content">
		<?php the_content(); ?>
    </section>

</article><!-- #post-<?php the_ID(); ?> -->
