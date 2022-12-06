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
				<a id="footer-logo" href="https://www.classicpress.net/"><img src="https://www.classicpress.net/wp-content/themes/classicpress-susty-child/images/icon-white.svg" alt="ClassicPress"></a>
				<p class="registration">The ClassicPress project is under the direction of The ClassicPress Initiative, a nonprofit organization registered under section 501(c)(3) of the United States IRS code.</p>

				<ul  class="social-menu">
					<li><a href="https://forums.classicpress.net/" target="_blank" title="Forums" rel="noreferrer noopener"><i class="cpicon-discourse"></i></a></li>
					<li><a href="https://www.classicpress.net/join-slack/" target="_blank" title="Slack" rel="noreferrer noopener"><i class="cpicon-slack"></i></a></li>
					<li><a href="https://github.com/ClassicPress" target="_blank" title="GitHub" rel="noreferrer noopener"><i class="cpicon-github"></i></a></li>
					<li><a href="https://twitter.com/GetClassicPress" target="_blank" title="Twitter" rel="noreferrer noopener"><i class="cpicon-twitter"></i></a></li>
					<li><a href="https://www.facebook.com/GetClassicPress" target="_blank" title="Facebook" rel="noreferrer noopener"><i class="cpicon-facebook-f"></i></a></li>
				</ul>
			</div>
			<div class="footerright">
				<div class="menu-footermenu-container">
					<ul id="footmenu" class="nav">
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://docs.classicpress.net/plugin-guidelines/">Plugin Guidelines</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://docs.classicpress.net/theme-guidelines/">Theme Guidelines</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://docs.classicpress.net/plugin-guidelines/directory-requirements/">Directory Requirements</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://forums.classicpress.net/c/support/directory-support/75">Directory Support</a></li>
						<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/developers/">Developer Directory</a></li>
					</ul>
				</div>
			</div>
			<div class="footerright">
				<div class="menu-footermenu-container">
					<ul id="footmenu" class="nav">
						<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/contact/">Contact Us</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://forums.classicpress.net/c/support">Forum Support</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://www.classicpress.net/join-slack/">Join on Slack</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://github.com/ClassicPress/ClassicPress/issues/new/choose">Feature Requests</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://opencollective.com/classicpress">Make a Donation</a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>

	<footer id="legal">
		<div class="cplegal">
			<div class="cpcopyright">
				<p>© 2018-<?php echo date( 'Y' ); ?> ClassicPress. All Rights Reserved.</p>
			</div>
			<div class="cppolicy">
				<p><a href="https://www.classicpress.net/privacy-policy/">Privacy Policy</a></p>
			</div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
