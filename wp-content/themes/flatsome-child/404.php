<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

get_header();
if (is_page('404.php') || is_404()) {
	$url = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';

	if ($url === '/buy/crewneck-jumper-and-track-pants-free-beanie' || $url === '/buy/crewneck-jumper-and-track-pants-free-beanie/') {
		wp_safe_redirect(home_url('/'), 301);
		exit();
	}

	if ($url === '/buy/hoodie-and-track-pants-free-beanie' || $url === '/buy/hoodie-and-track-pants-free-beanie/') {
		wp_safe_redirect(home_url('/'), 301);
		exit();
	}

}

?>
<?php do_action('flatsome_before_404'); ?>
<?php
if (get_theme_mod('404_block')):
	echo do_shortcode('[block id="' . get_theme_mod('404_block') . '"]');
else:
	?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main container pt" role="main">
			<section class="error-404 not-found mt mb">
				<div class="row">
					<div class="col medium-3"><span class="header-font" style="font-size: 6em; font-weight: bold; opacity: .3">404</span></div>
					<div class="col medium-9">
						<header class="page-title">
							<h1 class="page-title">
								<?php esc_html_e('Oops! That page can&rsquo;t be found.', 'flatsome'); ?>
							</h1>
						</header>
						<div class="page-content">
							<p>
								<?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'flatsome'); ?>
							</p>

							<?php get_search_form(); ?>
							<p style="text-align: center; float: left;"><a href="<?php echo esc_url('https://m.me/plus2clothing'); ?>" target="_self" class="button primary is-primary is-medium">
									<span>Message Us?</span>
								</a>

							</p>
							<p style="text-align: left;"><a href="<?php echo esc_url('mailto:support@plus2clothing.com'); ?>" target="_self" class="button primary is-primary is-medium">
									<span>Email Us ?</span>
								</a>

							</p>
						</div>
					</div>
				</div>
			</section>
		</main>
	</div>
<?php endif; ?>
<?php do_action('flatsome_after_404'); ?>
<?php get_footer(); ?>