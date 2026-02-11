<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          9.4.0
 * @flatsome-version 3.20.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

// Check stock status.
$out_of_stock = ! $product->is_in_stock();

// Extra post classes.
$classes   = array();
$classes[] = 'product-small';
$classes[] = 'col';
$classes[] = 'has-hover';

if ($out_of_stock)
	$classes[] = 'out-of-stock';
// CUSTOM START
// Get product category IDs
$categories = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'ids'));
$category_ids = !empty($categories) ? implode(',', $categories) : '';
?>
<div <?php wc_product_class($classes, $product); ?> data-category-ids="<?php echo esc_attr($category_ids); ?>">
	<div class="col-inner">
		<?php do_action('woocommerce_before_shop_loop_item'); ?>
		<div class="product-small box <?php echo flatsome_product_box_class(); ?>">
			<div class="box-image">
				<div class="<?php echo flatsome_product_box_image_class(); ?>">
					<a href="<?php echo get_the_permalink(); ?>" aria-label="<?php echo esc_attr($product->get_title()); ?>">
						<?php
						/**
						 *
						 * @hooked woocommerce_get_alt_product_thumbnail - 11
						 * @hooked woocommerce_template_loop_product_thumbnail - 10
						 */
						do_action('flatsome_woocommerce_shop_loop_images');
						?>
					</a>
				</div>
				<div class="image-tools is-small top right show-on-hover">
					<?php do_action('flatsome_product_box_tools_top'); ?>
				</div>
				<div class="image-tools is-small hide-for-small bottom left show-on-hover">
					<?php do_action('flatsome_product_box_tools_bottom'); ?>
				</div>
				<div class="image-tools <?php echo flatsome_product_box_actions_class(); ?>">
					<?php do_action('flatsome_product_box_actions'); ?>
				</div>
				<?php if ($out_of_stock) { ?>
					<div class="out-of-stock-label">
						<?php _e('Out of stock', 'woocommerce'); ?>
					</div>
				<?php } ?>
			</div>

			<div class="box-text <?php echo flatsome_product_box_text_class(); ?>">
				<?php
				do_action('woocommerce_before_shop_loop_item_title');

				echo '<div class="title-wrapper">';
				do_action('woocommerce_shop_loop_item_title');
				echo '</div>';


				echo '<div class="price-wrapper">';
				do_action('woocommerce_after_shop_loop_item_title');
				echo '</div>';

				

				do_action('flatsome_product_box_after');
				

				?>
			</div>
		</div>
		<?php do_action('woocommerce_after_shop_loop_item');
// CUSTOM START
		if (!$product->is_type('variable') && !$product->is_type('simple')) {
					$single_link_url = get_the_permalink(get_the_ID());
					echo '<a href="' . $single_link_url . '" class="button bunbutton" style="font-size:15px;margin:0;">SELECT OPTIONS</a>';
				}
				?>
	</div>
</div>
<?php /* empty PHP to avoid whitespace */ ?>
