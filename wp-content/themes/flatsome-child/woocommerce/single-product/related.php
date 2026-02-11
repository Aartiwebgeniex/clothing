<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          10.3.0
 * @flatsome-version 3.20.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;
// Get related products (using new function name, fallback to old for compatibility)
if (function_exists('p2c_get_related_products')) {
	$relatedProducts = p2c_get_related_products($product->get_id());
} elseif (function_exists('get_related_products')) {
	$relatedProducts = get_related_products($product->get_id());
} else {
	$relatedProducts = array();
}


$custom_related = get_post_meta($product->get_id(), '_subscription_toggle_ids');

if(!$custom_related){
	$custom_related = $relatedProducts;
}

if (isset($custom_related[0]) && is_array($custom_related[0]) && count($custom_related[0]) != 0) {
	?>

	<div class="customrelated related related-products-wrapper product-section">
		<?php $heading = apply_filters('woocommerce_product_related_products_heading', __('PAIRS WELL WITH', 'woocommerce'));
		if ($heading): ?>
			<h3 class="product-section-title container-width product-section-title-related pt-half pb-half uppercase text-center">
				<?php echo esc_html($heading); ?>
			</h3>
		<?php endif; ?>
		<div class="row large-columns-4 medium-columns-3 small-columns-2 row-small slider row-slider slider-nav-reveal slider-nav-push is-draggable flickity-enabled slider-lazy-load-active" tabindex="0" data-flickity-options='{
				"contain": true,
				"cellAlign": "left",
				"dragThreshold": 4,
				"wrapAround": false,
				"prevNextButtons":true,
				"adaptiveHeight": true,
				"dragThreshold" : 15,
				"pageDots": false,
				"rightToLeft": false       }'>




			<?php foreach ($custom_related as $related_prod):
				foreach ($related_prod as $custom) {
					$product = wc_get_product($custom);
					// Check if the product is in stock
					if (!$product || !$product->is_in_stock()) {
						continue; // Skip out of stock products
					}
					?>

					<div class="product-small fslider-item col has-hover product type-product status-publish has-post-thumbnail product_cat-bundles product_cat-long-sleeve-tall-tees product_cat-all-products product_tag-notforestore instock sale virtual taxable purchasable product-type-bundle is-selected">

						<?php //wc_get_template( 'content-product.php', array( 'product' => $related_prod ) ); ?>

						<div class="col-inner">
							<div class="product-small box ">
								<div class="box-image">
									<div class="image-zoom">
										<?php if (isset($product) && $product): ?>
											<a href="<?php echo $product->get_permalink(); ?>">
												<?php echo $product->get_image(); ?>
											</a>
										<?php endif; ?>

									</div>
									<div class="image-tools is-small top right show-on-hover">
										<?php do_action('flatsome_product_box_tools_top'); ?>
									</div>
									<div class="image-tools is-small hide-for-small bottom left show-on-hover">
										<?php do_action('flatsome_product_box_tools_bottom'); ?>
									</div>
									<div class="image-tools grid-tools text-center hide-for-small bottom hover-slide-in show-on-hover">
										<?php do_action('flatsome_product_box_actions'); ?>
									</div>
								</div>
								<div class="box-text box-text-products flex-row align-top grid-style-3 flex-wrap">
									<div class="title-wrapper">
										<p class="name product-title woocommerce-loop-product__title">
											<?php if (isset($product) && $product): ?>
												<a href="<?php echo $product->get_permalink(); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><?php echo $product->get_title(); ?></a>
											<?php endif; ?>
										</p>
									</div>
									<div class="price-wrapper">
										<span class="price"><span class="woocommerce-Price-amount amount"><?php
										if ($product !== false) {
											echo $product->get_price_html();
										}
										?></span></span>
									</div>
								</div>
							</div>
						</div>


					</div>




				<?php }
				wp_reset_query();
				wp_reset_postdata();
			endforeach; ?>


		</div>
	</div>
	<style>
		.customrelated.related .product_cat-bundles .image-tools {
			display: block !important;
		}
	</style>
<?php } else {


	// Get Type.
	$type             = get_theme_mod('related_products', 'slider');
	$repeater_classes = array();

	if ($type == 'hidden')
		return;
	if ($type == 'grid')
		$type = 'row';

	if (get_theme_mod('category_force_image_height'))
		$repeater_classes[] = 'has-equal-box-heights';
	if (get_theme_mod('equalize_product_box'))
		$repeater_classes[] = 'equalize-box';

	$repeater['type']         = $type;
	$repeater['columns']      = get_theme_mod('related_products_pr_row', 4);
	$repeater['columns__md']  = get_theme_mod('related_products_pr_row_tablet', 3);
	$repeater['columns__sm']  = get_theme_mod('related_products_pr_row_mobile', 2);
	$repeater['class']        = implode(' ', $repeater_classes);
	$repeater['slider_style'] = 'reveal';
	$repeater['row_spacing']  = 'small';


	if ($related_products): ?>

		<div class="related related-products-wrapper product-section">

			<?php
			// CUSTOM START word change below
			$heading = apply_filters('woocommerce_product_related_products_heading', __('PAIRS WELL WITH', 'woocommerce'));

			if ($heading):
				?>
				<h3 class="product-section-title container-width product-section-title-related pt-half pb-half uppercase text-center">
					<?php echo esc_html($heading); ?>
				</h3>
			<?php endif; ?>


			<?php get_flatsome_repeater_start($repeater); ?>

			<?php foreach ($related_products as $related_product): ?>

				<?php
				$post_object = get_post($related_product->get_id());

				setup_postdata($GLOBALS['post'] =& $post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
	
				wc_get_template_part('content', 'product');
				?>

			<?php endforeach; ?>

			<?php get_flatsome_repeater_end($repeater); ?>

		</div>

		<?php
	endif;

	wp_reset_postdata();

}

