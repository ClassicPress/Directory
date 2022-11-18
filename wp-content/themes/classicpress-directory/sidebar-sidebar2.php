<?php
/**
 * The right sidebar containing a widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * 
 */

if ( ! is_active_sidebar( 'sidebar-2' ) ) {
	return;
}
?>

<aside id="right-sidebar" class="widget-area" role="complementary" aria-labelledby="sidebar-2-header">

	<h2 class="screen-reader-text" id="sidebar-2-header"><?php esc_attr_e( 'Right Sidebar', 'classicpress' ); ?></h2>

	<?php dynamic_sidebar( 'sidebar-2' ); ?>

</aside><!-- #secondary -->
