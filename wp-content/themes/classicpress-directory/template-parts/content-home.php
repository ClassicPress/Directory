<?php

/**
 * Template part for displaying homepage
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <section class="home-search">
        <h1><?php _e('ClassicPress Directory', 'classicpress'); ?></h1>
        <p class="home-lead"><?php printf(__('Extend your ClassicPress website with <mark>%s plugins</mark> and <mark>%s themes</mark>.', 'classicpress'), do_shortcode('[total_plugin_count]'), do_shortcode('[total_theme_count]')); ?></p>
        <div class="home-search-form">
            <form action="/" method="get">
                <div role="search">
                    <div class="home-search-input">
                        <label for="ofsearch" class="screen-reader-text"><?php _e('Search plugins and themes', 'classicpress'); ?></label>
                        <input type="text" id="ofsearch" name="s" placeholder="Search plugins and themes" value="" required="required">
                        <input type="hidden" name="post_types" value="plugin,theme">
                    </div>
                    <div class="home-search-btn">
                        <input type="submit" value="Search">
                    </div>
                </div>
            </form>
        </div>
    </section>
    <hr>
    <section class="featured-software">
        <header class="featured-heading">
            <h2><?php _e('Featured Plugins', 'classicpress'); ?></h2>
            <a href="/plugins"><?php _e('Explore all plugins', 'classicpress'); ?></a>
        </header>
        <?php get_random_plugin_posts(); ?>
    </section>
    <hr>

    <section class="featured-software">
        <header class="featured-heading">
            <h2><?php _e('Featured Themes', 'classicpress'); ?></h2>
            <a href="/themes"><?php _e('Explore all themes', 'classicpress'); ?></a>
        </header>
        <?php get_random_theme_posts(); ?>
    </section>

    <section class="home-content">
        <?php the_content(); ?>
    </section>

</article><!-- #post-<?php the_ID(); ?> -->