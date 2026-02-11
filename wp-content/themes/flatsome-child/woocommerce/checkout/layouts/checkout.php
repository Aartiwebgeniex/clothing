<?php
/**
 * Default checkout layout.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.16.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php
	wc_get_template( 'checkout/header.php' );

	echo '<div class="cart-container container page-wrapper page-checkout">';
	wc_print_notices();
	?>
<!-- CUSTOM START -->
	<iframe src="<?php echo esc_url('https://app.ravecapture.com/merchant/TrustModule/badge/Plus2Clothing'); ?>" seamless allowtransparency="true" scrolling="no" frameborder="0" style="border:none; overflow: hidden; margin-bottom:20px;" height="100" width="180" class="iframe-styles"><p><?php esc_html_e('View Our Reviews On RaveCapture', 'woocommerce'); ?></p></iframe>	
	<!-- CUSTOM END --> 
<?php
	the_content();
	echo '</div>';
	?>

<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
