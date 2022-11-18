<?php
/**
 * The left sidebar containing a widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * 
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="left-sidebar" class="widget-area" role="complementary" aria-labelledby="sidebar-1-header">

	<h2 class="screen-reader-text" id="sidebar-1-header"><?php esc_attr_e( 'Left Sidebar', 'bedrock' ); ?></h2>

	<?php dynamic_sidebar( 'sidebar-1' ); ?>

</aside><!-- #secondary -->
