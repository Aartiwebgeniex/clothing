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
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product)) {
	return;
}

// Check stock status.
$out_of_stock = !$product->is_in_stock();

// Extra post classes.
$classes   = array();
$classes[] = 'product-small';
$classes[] = 'col';
$classes[] = 'has-hover';

if ($out_of_stock)
	$classes[] = 'out-of-stock';

/* CUSTOM START */
global $wpdb, $post;
$product_cats          = wp_get_post_terms($post->ID, 'product_cat');
$product_category_name = !empty($product_cats[0]) ? $product_cats[0]->name : '';
$request_uri           = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$url_path              = trim(parse_url($request_uri, PHP_URL_PATH), '/');
$url_parts             = !empty($url_path) ? explode('/', $url_path) : array();
$category              = isset($url_parts[0]) ? $url_parts[0] : '';
$style                 = isset($args['style']) ? $args['style'] : '';
$product_id            = isset($args['product_id']) ? $args['product_id'] : $post->ID;

// Custom Code - Get sales data with prepared statement
$sales = array();
if (is_product_category() || is_shop()) {
	$sql   = $wpdb->prepare("SELECT * FROM {$wpdb->options} WHERE option_name = %s", 'category_sales');
	$data  = $wpdb->get_results($sql);
	if (!empty($data) && isset($data[0]->option_value)) {
		$sales = json_decode($data[0]->option_value, true);
		if (!is_array($sales)) {
			$sales = array();
		}
	}
}
// Custom Code
if (($category == 'buy' && $product_category_name != 'Wholesale Tall Clothing') || $category != 'buy') {
	?>
	<div <?php post_class($classes); ?> product-id="<?php echo esc_attr($product_id); ?>" data-name="<?php echo esc_attr(!empty($sales[$product_id]) && $sales[$product_id] > 0 ? $sales[$product_id] : 0); ?>" <?php echo esc_attr($style); ?>>
		<?php // CUSTOM END ?>

		<!-- <div <?php //fl_woocommerce_version_check( '3.4.0' ) ? wc_product_class( $classes, $product ) : post_class( $classes ); ?>> -->
		<div class="col-inner">
			<?php do_action('woocommerce_before_shop_loop_item'); ?>
			<div class="product-small box <?php echo flatsome_product_box_class(); ?>">
				<div class="box-image">
					<div class="<?php echo flatsome_product_box_image_class(); ?>">

						<?php // CUSTOM START ?>
						<?php
						if (is_cart()) { ?>
							<a class="quick-view-popup">
								<?php
								/**
								 *
								 * @hooked woocommerce_get_alt_product_thumbnail - 11
								 * @hooked woocommerce_template_loop_product_thumbnail - 10
								 */
								do_action('flatsome_woocommerce_shop_loop_images');
								?>
							</a>
						<?php } else { ?>
							<?php // CUSTOM END ?>

							<a href="<?php echo get_the_permalink(); ?>">
								<?php
								/**
								 *
								 * @hooked woocommerce_get_alt_product_thumbnail - 11
								 * @hooked woocommerce_template_loop_product_thumbnail - 10
								 */
								do_action('flatsome_woocommerce_shop_loop_images');
								?>
							</a>


							<?php
							//CUSTOM START 
						} ?>
					<?php
					$maxQ = get_post_meta($post->ID, 'maximum_allowed_quantity', true);
					if ($maxQ >= 1) {
						echo '<input type="hidden" value="' . esc_attr($maxQ) . '" class="mxq">';
					}
					?>


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
						<div class="out-of-stock-label"><?php _e('Out of stock', 'woocommerce'); ?></div><?php } ?>
				</div>

				<div class="box-text <?php echo flatsome_product_box_text_class(); ?>">
					<?php
					do_action('woocommerce_before_shop_loop_item_title');

					echo '<div class="title-wrapper">';
					do_action('woocommerce_shop_loop_item_title');
					echo '</div>';


					/* CUSTOM START */
					if ($product_category_name != 'Wholesale Tall Clothing') {
						/* CUSTOM END */

						echo '<div class="price-wrapper">';
						do_action('woocommerce_after_shop_loop_item_title');
						echo '</div>';

						/* CUSTOM START */
					}
					$single_link_url = get_the_permalink(get_the_ID());
					if ($product->is_type('variable') || $product->is_type('simple')) {
						// Variable or simple products - no action needed
					} else {
						echo "<div class='bspace'></div>";
						echo '<a href="' . esc_url($single_link_url) . '" class="button bunbutton" style="font-size:15px;margin:0;">SELECT OPTIONS</a>';
					}

					/* CUSTOM END */

					do_action('flatsome_product_box_after');

					?>
				</div>
			</div>
			<?php do_action('woocommerce_after_shop_loop_item'); ?>
		</div><!-- .col-inner -->
	</div><!-- col -->
<?php // CUSTOM START ?>
<?php } ?>
<?php // CUSTOM END ?>