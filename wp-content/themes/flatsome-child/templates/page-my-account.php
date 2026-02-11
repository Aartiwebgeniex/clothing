<?php
/*
Template name: WooCommerce - My Account
This templates add My account to the sidebar.
*/

get_header(); ?>

<?php do_action('flatsome_before_page'); ?>

<?php wc_get_template('myaccount/header.php'); ?>

<div class="page-wrapper my-account mb">
	<div class="container" role="main">

		<?php if (is_user_logged_in()) { ?>

			<div class="row vertical-tabs">
				<div class="large-3 col col-border">

					<?php wc_get_template('myaccount/account-user.php'); ?>

					<ul id="my-account-nav" class="account-nav nav nav-line nav-uppercase nav-vertical mt-half">
						<!-- CUSTOM CODE START -->
						<!--<li class="menu-item">
							<a href="<?php echo get_site_url(); ?>/affiliate-area" class="nav-top-link">Affiliate Area</a>
						</li>-->
						<!-- CUSTOM CODE END -->
						<?php wc_get_template('myaccount/account-links.php'); ?>
					</ul><!-- .account-nav -->
				</div><!-- .large-3 -->

				<div class="large-9 col">
					<?php while (have_posts()):
						the_post(); ?>
						<?php the_content(); ?>
					<?php endwhile; // end of the loop.  ?>
				</div><!-- .large-9 -->
			</div><!-- .row .vertical-tabs -->

		<?php } else { ?>
			<!-- CUSTOM CODE START -->
		<!--	<a class="button button-primary" href="<?php echo get_site_url(); ?>/affiliate-area">Affiliate Area</a> -->
			<!-- CUSTOM CODE END -->
			<?php while (have_posts()):
				the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; // end of the loop.  ?>

		<?php } ?>


	</div><!-- .container -->
</div><!-- .page-wrapper.my-account  -->


<?php do_action('flatsome_after_page'); ?>

<?php get_footer(); ?>