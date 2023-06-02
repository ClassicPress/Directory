<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>
		</div><!-- #content -->

		<?php get_sidebar( 'sidebar2' ); ?>

	</div><!-- #outer-content -->
	
	<footer id="colophon">
		<div class="classic">
			<div class="footerleft">
				<a id="footer-logo" href="<?php echo esc_attr('https://www.classicpress.net/'); ?>"><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/classicpress-logo-feather-white.svg'); ?>" alt="<?php esc_attr_e('ClassicPress feather logo', 'classicpress'); ?>" width="90"></a>
				<p class="registration"><?php _e('The ClassicPress project is under the direction of The ClassicPress Initiative, a nonprofit organization registered under section 501(c)(3) of the United States IRS code.', 'classicpress'); ?></p>

				<ul  class="social-menu">
					<li><a href="<?php echo esc_url('https://forums.classicpress.net/'); ?>" target="_blank" title="<?php esc_attr_e('Forums', 'classicpress'); ?>" rel="noreferrer noopener"><i class="cpicon-discourse"></i><span class="screen-reader-text"><?php _e('Support forums', 'classicpress'); ?></span></a></li>
					<li><a href="<?php echo esc_url('https://www.classicpress.net/join-slack/'); ?>" target="_blank" title="<?php esc_attr_e('Slack', 'classicpress'); ?>" rel="noreferrer noopener"><i class="cpicon-slack"></i><span class="screen-reader-text"><?php _e('Join on Slack', 'classicpress'); ?></span></a></li>
					<li><a href="<?php echo esc_url('https://github.com/ClassicPress'); ?>" target="_blank" title="<?php esc_attr_e('GitHub', 'classicpress'); ?>" rel="noreferrer noopener"><i class="cpicon-github"></i><span class="screen-reader-text"><?php _e('Visit GitHub', 'classicpress'); ?></span></a></li>
					<li><a href="<?php echo esc_url('https://fosstodon.org/@classicpress'); ?>" target="_blank" title="<?php esc_attr_e('Mastodon', 'classicpress'); ?>" rel="noreferrer noopener"><i class="cpicon-facebook-f"></i><span class="screen-reader-text"><?php _e('Follow on Mastodon', 'classicpress'); ?></span></a></li>
					<li><a href="<?php echo esc_url('https://twitter.com/GetClassicPress'); ?>" target="_blank" title="<?php esc_attr_e('Twitter', 'classicpress'); ?>" rel="noreferrer noopener"><i class="cpicon-twitter"></i><span class="screen-reader-text"><?php _e('Follow on Twitter', 'classicpress'); ?></span></a></li>
					<li><a href="<?php echo esc_url('https://www.facebook.com/GetClassicPress'); ?>" target="_blank" title="<?php esc_attr_e('Facebook', 'classicpress'); ?>" rel="noreferrer noopener"><i class="cpicon-facebook-f"></i><span class="screen-reader-text"><?php _e('Like on Facebook', 'classicpress'); ?></span></a></li>
				</ul>
			</div>
			<div class="footerright">
				<div class="menu-footermenu-container">
					<ul id="footmenu" class="nav">
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://docs.classicpress.net/plugin-guidelines/'); ?>"><?php _e('Plugin Guidelines', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://docs.classicpress.net/theme-guidelines/'); ?>"><?php _e('Theme Guidelines', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://docs.classicpress.net/plugin-guidelines/directory-requirements/'); ?>"><?php _e('Directory Requirements', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://forums.classicpress.net/c/support/directory-support/75'); ?>"><?php _e('Directory Support', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo esc_url(home_url() . '/developers/'); ?>"><?php _e('Developer Directory'); ?></a></li>
					</ul>
				</div>
			</div>
			<div class="footerright">
				<div class="menu-footermenu-container">
					<ul id="footmenu" class="nav">
						<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="<?php echo esc_url('https://www.classicpress.net/contact/'); ?>"><?php _e('Contact Us', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://forums.classicpress.net/c/support'); ?>"><?php _e('Forum Support', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://www.classicpress.net/join-slack/'); ?>"><?php _e('Join on Slack', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://github.com/ClassicPress/ClassicPress/issues/new/choose'); ?>"><?php _e('Feature Requests', 'classicpress'); ?></a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="<?php echo esc_url('https://opencollective.com/classicpress'); ?>"><?php _e('Make a Donation', 'classicpress'); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>

	<footer id="legal">
		<div class="cplegal">
			<div class="cpcopyright">
				<?php printf(__('© 2018-%s ClassicPress Initiative. All Rights Reserved.', 'classicpress'), date( 'Y' )); ?>
			</div>
			<div class="cppolicy">
				<p><a href="<?php echo esc_url('https://www.classicpress.net/privacy-policy/'); ?>"><?php _e('Privacy Policy', 'classicpress'); ?></a></p>
			</div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
