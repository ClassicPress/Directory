<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bedrock
 */

?>

<?php if (is_singular()) {
	$class = "single-software";
} else {
	$class = "grid-item-software";
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>

	<?php
	if (is_singular()) :
	?>

		<header>
			<div class="header-content">
				<?php the_title('<h1>', '</h1>'); ?>
				<div class="software-author-link">
					<?php _e('Developed by', 'classicpress'); ?>
					<span><?php echo get_the_author_posts_link(); ?></span>
				</div>
			</div>
			<div class="software-action">
				<a href="<?php echo esc_url(get_post_meta($post->ID, 'download_link', true)); ?>" title="<?php esc_attr_e('Download', 'classicpress'); ?> <?php echo esc_attr($post->post_title); ?>" class="btn" role="button"><img src="<?php echo esc_url(get_template_directory_uri() . '/images/download-white.svg'); ?>" alt="<?php esc_attr_e('Download', 'classicpress'); ?>" width="18" height="18" aria-hidden="true"> <?php _e('Download', 'classicpress'); ?></a>
			</div>
		</header>

		<section id="software" class="software">
			<h2 class="screen-reader-text">
				<?php
				switch (get_post_type()) {
					case 'plugin':
						esc_html_e('Plugin Description', 'classicpress');
						break;
					case 'theme':
						esc_html_e('Theme Description', 'classicpress');
						break;
				}
				?>
			</h2>

			<div class="readme-md-content">
				<?php
				the_content(sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'classicpress'),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				));
				?>
			</div>

			<?php
			if (function_exists('display_author_themes') && function_exists('display_author_plugins')) :
				$display_author_themes = display_author_themes();
				$display_author_plugins = display_author_plugins();
				if ($display_author_themes || $display_author_plugins) : ?>
					<footer class="developer-items-wrapper">
						<?php
						// Note: functions exclude current post
						if (get_post_type() === 'plugin') {
							if ($display_author_plugins) {
								echo $display_author_plugins;
							}
							if ($display_author_themes) {
								echo $display_author_themes;
							}
						} elseif (get_post_type() === 'theme') {
							if ($display_author_themes) {
								echo $display_author_themes;
							}
							if ($display_author_plugins) {
								echo $display_author_plugins;
							}
						}
						?>
					</footer>
			<?php endif;
			endif;
			?>

		</section>

		<aside>
			<h2 class="screen-reader-text"><?php _e('Meta', 'classicpress'); ?></h2>
			<ul class="aside-items">
				<li class="aside-item">
					<?php _e('Version', 'classicpress'); ?>
					<span class="item-data"><?php echo esc_html(get_post_meta($post->ID, 'current_version', true)); ?></span>
					<?php
					$published_at = get_post_meta($post->ID, 'published_at', true);
					$published_at_atom = date(DateTimeInterface::ATOM, $published_at);
					$published_at_human = date("F j, Y - g:i a", $published_at);
					$published_at_diff = human_time_diff($published_at, current_time('timestamp'));
					if (isset($published_at)) : ?>
				<li class="aside-item">
					<?php _e('Last Updated', 'classicpress'); ?>
					<span class="item-data"><time datetime="<?php echo esc_attr($published_at_atom); ?>" title="<?php echo esc_attr($published_at_human); ?>"><?php echo esc_html($published_at_diff); ?></time> <?php _e('ago', 'classicpress'); ?></span>
				</li>
			<?php endif; ?>
			<li class="aside-item">
				<?php _e('ClassicPress Version', 'classicpress'); ?>
				<span class="item-data"><?php echo esc_html(get_post_meta($post->ID, 'requires_cp', true) . '.0'); ?></span>
			</li>
			<li class="aside-item">
				<?php _e('PHP Version', 'classicpress'); ?>
				<span class="item-data"><?php echo esc_html(get_post_meta($post->ID, 'requires_php', true) . '.0'); ?></span>
			</li>
			<?php
			if ($post->post_type === 'plugin') {
			?>
				<li class="aside-item">
					<?php _e('Categories', 'classicpress'); ?>
					<span class="item-data"><?php the_category(); ?></span>
				</li>

			<?php
			} elseif ($post->post_type === 'theme') {
			?>
				<li class="aside-item">
					<?php _e('Tags', 'classicpress'); ?>
					<span class="item-data"><?php the_tags('<div class="tags"><span class="tag-badge">', '</span><span class="tag-badge">', '</span></div>'); ?></span>
				</li>

			<?php
			}
			?>

			<?php

			$download_url = get_post_meta($post->ID, 'download_link', true);

			// check if GitHub and get repo URL
			if (strpos($download_url, 'github.com') !== false) {
				$url_parts = explode('releases', $download_url);
				$repo = array(
					"url" => $url_parts[0],
					"name" => "GitHub"
				);
			} else {
				// TO-DO: Add support for other Git platforms
			}
			?>
			<li class="aside-item">
				<?php _e('Repository', 'classicpress'); ?>
				<span class="item-data repo-link"><a href="<?php echo esc_url($repo['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e('Visit repository', 'classicpress'); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/images/' . $repo['name'] . '.svg'); ?>" alt="<?php echo esc_attr($repo['name']); ?>" width="14" height="14" aria-hidden="true"><?php echo esc_html($repo['name']); ?></a></span>
			</li>
			<?php $active_installations = filter_var(get_post_meta($post->ID, 'active_installations', true), FILTER_VALIDATE_INT);
			if ($active_installations) : ?>
				<li class="aside-item">
					<?php _e('Active Installations', 'classicpress'); ?>
					<span class="item-data repo-link" title="<?php esc_attr_e($active_installations); ?>"><?php echo esc_html(human_readable_number($active_installations)); ?></span>
				</li>
			<?php endif; ?>
			</ul>

			<?php
			$premium_link = filter_var(get_post_meta($post->ID, 'premium_uri', true), FILTER_VALIDATE_URL);
			if ($premium_link !== FALSE) :
			?>
				<div class="premium-notice">
					<h3><?php printf(__('Commercial %s', 'classicpress'), get_post_type()); ?></h3>
					<p><?php printf(__('This %s is free, but it offers a paid version, addons, support, or requires a paid account.', 'classicpress'), get_post_type()); ?>
						<a href="<?php echo esc_url($premium_link); ?>" target="blank" class="external" title="<?php esc_attr_e('Learn more', 'classicpress'); ?>" rel="nofollow ugc external noopener"><?php _e('Learn more', 'classicpress'); ?></a>
					</p>
				</div>
			<?php endif; ?>
			<?php
			// Tip box
			$donation_link = filter_var(get_the_author_meta('donation_url'), FILTER_VALIDATE_URL);
			$author_id = get_post_field('post_author', $post->ID);
			$author_display_name = get_the_author_meta('display_name', $author_id);
			if ($donation_link !== FALSE) :
			?>
				<div class="donation-box">
					<p><?php printf(__('Thank developer and support future development of this %s by tipping them.', 'classicpress'), get_post_type()); ?></p>
					<a href="<?php echo esc_url($donation_link); ?>" target="blank" class="external btn btn-alt" title="<?php esc_attr_e('Click to tip', 'classicpress'); ?>" rel="nofollow ugc external noopener"><?php printf(__('Tip %s', 'classicpress'), $author_display_name); ?></a>
				</div>
			<?php endif; ?>
		</aside>

	<?php
	else :
	?>
		<div class="software-item-info">
			<header>
				<?php the_title('<h2  class="h3"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');	?>
				<span class="software-author-link"><?php echo get_the_author_posts_link(); ?></span>
			</header>

			<p><?php kts_excerpt_fallback($post); ?></p>
		</div>
		<div class="software-item-meta">
			<div class="meta-item left">
				<a href="<?php echo esc_url(get_post_meta($post->ID, 'download_link', true)); ?>" title="<?php esc_attr_e('Download', 'classicpress') . ' ' . esc_attr($post->post_title); ?>" target="_blank" rel="noopener noreferrer">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/images/download.svg'); ?>" alt="<?php esc_attr_e('Download', 'classicpress'); ?>" width="18" height="18" aria-hidden="true">
					<span><?php _e('Download', 'classicpress'); ?></span>
				</a>
			</div>
			<div class="meta-item right">
				<a href="#info-<?php echo absint($post->ID); ?>" title="<?php esc_attr_e('More information about', 'classicpress'); ?> <?php echo esc_attr($post->post_title); ?>" class="info-button" data-info="info-<?php echo absint($post->ID); ?>">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/images/info.svg'); ?>" alt="<?php _e('Info', 'classicpress'); ?>" width="18" height="18" aria-hidden="true">
					<span><?php _e('More Info', 'classicpress'); ?></span>
				</a>
			</div>
		</div>

	<?php
	endif;

	if (!is_singular()) {
	?>

		<footer class="software-item-footer">

			<?php // Add update button if author archive is of the current user
			kts_render_software_update_link_form($post);
			?>

			<div id="info-<?php echo absint($post->ID); ?>" class="info-panel" hidden>

				<?php
				the_content(sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'classicpress'),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				));
				?>

			</div>
		</footer>

	<?php
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->