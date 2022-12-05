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
					<?php
					if ($post->post_type === 'snippet') {
						_e('Posted by', 'classicpress');
					} else {
						_e('Developed by', 'classicpress');
					} ?> <span><?php echo get_the_author_posts_link(); ?></span></div>
			</div>
			<div class="software-action">
				<a href="<?php echo get_post_meta($post->ID, 'download_link', true); ?>" title="<?php _e('Download', 'classicpress'); ?> <?php echo esc_attr($post->post_title); ?>" class="btn" role="button"><img src="<?php echo esc_url(get_template_directory_uri() . '/images/download-white.svg'); ?>" alt="download" width="18" height="18" aria-hidden="true"> <?php _e('Download', 'classicpress'); ?></a>
			</div>
		</header>

		<section id="software" class="software">
			<h2 class="h5 description">
				<?php
				switch (get_post_type()) {
					case 'plugin':
						esc_html_e('Plugin Description', 'classicpress');
						break;
					case 'theme':
						esc_html_e('Theme Description', 'classicpress');
						break;
					case 'snippet':
						esc_html_e('Snippet Description', 'classicpress');
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

		</section>

		<aside>
			<h2 class="screen-reader-text">Meta</h2>
			<ul class="aside-items">
				<li class="aside-item">
					<?php _e('Version', 'classicpress'); ?>
					<span class="item-data"><?php echo esc_html(get_post_meta($post->ID, 'current_version', true)); ?></span>
				<li>
				<li class="aside-item">
					<?php _e('ClassicPress Version', 'classicpress'); ?>
					<span class="item-data"><?php echo esc_html(get_post_meta($post->ID, 'requires_cp', true)) . '.0'; ?></span>
				</li>
				<?php
				if ($post->post_type === 'plugin') {
				?>
					<li class="aside-item">
						<?php _e('Categories', 'classicpress'); ?>
						<span class="item-data"><?php the_category(); ?></span>
					</li>

				<?php
				} elseif ($post->post_type === 'snippet') {
				?>
					<li class="aside-item">
						<?php _e('Tags', 'classicpress'); ?>
						<span class="item-data"><?php the_tags('<div class="tags"><span class="tag">', '</span><span class="tag">', '</span></div>'); ?></span>
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
					}else{
						// TO-DO: Add support for other Git platforms
					}
				?>
				<li class="aside-item">
					<?php _e('Repository', 'classicpress'); ?>
					<span class="item-data repo-link"><a href="<?php esc_url($repo['url']); ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url(get_template_directory_uri() . '/images/'.$repo['name'].'.svg'); ?>" alt="<?php echo $repo['name']; ?>" aria-hidden="true"> <?php echo $repo['name']; ?></a></span>
				</li>
			</ul>
		</aside>

	<?php
	else :
	?>

		<header>
			<?php the_title('<h2  class="h3"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');	?>
			<span class="software-author-link"><?php echo get_the_author_posts_link(); ?></span>
		</header>

		<p><?php kts_excerpt_fallback($post); ?></p>

		<div class="software-item-meta">
			<div class="meta-item left">
				<a href="<?php echo esc_url(get_post_meta($post->ID, 'download_link', true)); ?>" title="<?php _e('Download', 'classicpress') . ' ' . esc_attr($post->post_title); ?>" target="_blank" rel="noopener noreferrer">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/images/download.svg'); ?>" alt="<?php _e('Download', 'classicpress'); ?>" width="18" height="18" aria-hidden="true">
					<span><?php _e('Download', 'classicpress'); ?></span>
				</a>
			</div>
			<div class="meta-item right">
				<a href="#info-<?php echo absint($post->ID); ?>" title="<?php _e('More information about', 'classicpress'); ?> <?php echo esc_attr($post->post_title); ?>" class="info-button" data-info="info-<?php echo absint($post->ID); ?>">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/images/info.svg'); ?>" alt="<?php _e('Info', 'classicpress'); ?>" width="18" height="18" aria-hidden="true">
					<span><?php _e('More Info', 'classicpress'); ?></span>
				</a>
			</div>
		</div>

	<?php
	endif;

	if (!is_singular()) {
	?>

		<footer>

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