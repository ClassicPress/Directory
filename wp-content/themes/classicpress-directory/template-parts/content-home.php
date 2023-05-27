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
        <h1>ClassicPress Directory</h1>
        <p class="home-lead">Extend your ClassicPress website with <mark><?php echo do_shortcode('[total_plugin_count]'); ?> plugins</mark> and <mark><?php echo do_shortcode('[total_theme_count]'); ?> themes</mark>.</p>
        <div class="home-search-form">
            <form action="/" method="get">
                <div class="home-search-input">
                    <label for="ofsearch" class="screen-reader-text">Search</label>
                    <input type="text" id="ofsearch" name="s" placeholder="Search…" value="" required="required">
                    <input type="hidden" name="post_types" value="plugin,theme">
                </div>
                <div class="home-search-btn">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </section>

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