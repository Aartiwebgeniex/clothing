<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name">
				<?php esc_html_e('Product', 'woocommerce'); ?>
			</th>
			<th class="product-total">
				<?php esc_html_e('Total', 'woocommerce'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		do_action('woocommerce_review_order_before_cart_contents');

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
			//CUSTOM START
		
			$_product = $cart_item['data'];
			$type     = $_product->get_type();

			if ($type == 'gift-card') {

				$products_array[$_product->get_id()] = array(
					'product_id'           => $_product->get_id(),
					'name'                 => $_product->get_name(),
					'quantity'             => $cart_item['quantity'],
					'price'                => ($cart_item['quantity'] * $_product->get_price()),
					'price_after_discount' => ($cart_item['quantity'] * $_product->get_price()),
					'discount'             => ''
				);
			} else {

				if ($_product->get_regular_price() > 0) {
					$products_array[$_product->get_id()] = array(
						'product_id'           => $_product->get_id(),
						'name'                 => $_product->get_name(),
						'quantity'             => $cart_item['quantity'],
						'price'                => ($cart_item['quantity'] * $_product->get_regular_price()),
						'price_after_discount' => ($cart_item['quantity'] * $_product->get_price()),
						'discount'             => ($cart_item['quantity'] * ($_product->get_regular_price() - $_product->get_price()))
					);
				}

			}

			//CUSTOM END
			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
				?>
				<tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)) . ' checkout-product-' . $cart_item['product_id']; ?>">
					<td class="product-name">
						<?php
						/* CUSTOM START : Commented orignal following 3 lines and added custom ones. */
						?>
						<span class="checkout-p-image">
							<?php $thumbnail = apply_filters('woocommerce_in_cart_product_thumbnail', $_product->get_image(), '', $cart_item_key);
							echo $thumbnail; ?>
						</span>

						<?php
						$product_amount = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
						?>

						<?php $product_title = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;';
						$product_title = explode("-", $product_title);
						//CUSTOM START	
						$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
						$product_name      = apply_filters('woocommerce_cart_item_name', sprintf($_product->get_title()), $cart_item, $cart_item_key);

						?>
						<span>
							<?php echo $product_name . ' x ' . $cart_item['quantity']; ?>
						</span>
						<?php
						// following code is for attributes				 
						$attributes = $_product->is_type('variable') || $_product->is_type('variation') ? wc_get_formatted_variation($_product) : '';

						echo $attributes ? $attributes : '';
						//CUSTOM END 		
						?>
						<!-- CUSTOM END -->

					</td>
					<td class="product-total">
						<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
					</td>
				</tr>
				<?php
			}
		}
		$total_quantity       = 0;
		$price_of_all_items   = 0;
		$discounts            = 0;
		$price_after_discount = 0;

		foreach ($products_array as $arr) {
			$total_quantity += $arr['quantity'];
			$price_of_all_items += floatval($arr['price']);
			$discounts += floatval($arr['discount']);
			$price_after_discount += floatval($arr['price_after_discount']);
		}

		do_action('woocommerce_review_order_after_cart_contents');
		?>
	</tbody>
	<tfoot>
		<?php if ($discounts > 0) { ?>
			<tr class="cart-subtotal">
				<th>
					<?php esc_html_e($total_quantity . ' ITEM' . ((count($products_array) > 1) ? 'S' : '') . ' BEFORE DISCOUNT', 'woocommerce'); ?>
				</th>
				<td data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>"><span class="amount">$
						<?php echo number_format((float) $price_of_all_items, 2, '.', ''); ?>
					</span></td>
			</tr>
			<tr class="cart-subtotal cart_discounts" onclick="toggle_discount_items()">
				<th>
					<?php esc_html_e('DISCOUNTS', 'woocommerce'); ?><i class="fa fa-angle-down"></i>
				</th>
				<td data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
					<?php echo '-<span class="amount">$' . number_format((float) $discounts, 2, '.', '') . '</span>'; ?>
				</td>
			</tr>
			<?php foreach ($products_array as $arr) {
				if ($arr['discount'] > 0) { ?>
					<tr class="cart-subtotal-items discounted_items">
						<th>
							<?php esc_html_e($arr['name'], 'woocommerce'); ?>
						</th>
						<td data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
							<?php echo 'SAVE <span class="amount">$' . number_format((float) $arr['discount'], 2, '.', '') . '</span>'; ?>
						</td>
					</tr>
				<?php }
			} ?>
			<tr class="cart-subtotal">
				<th>
					<?php esc_html_e('SUBTOTAL AFTER DISCOUNT', 'woocommerce'); ?>
				</th>

				<td data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>"><span class="amount">$
						<?php echo number_format((float) $price_after_discount, 2, '.', ''); ?>
					</span></td>
			</tr>
		<?php } ?>
		<!-- <tr class="cart-subtotal">
			<th><?php _e('Subtotal', 'woocommerce'); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr> -->

		<?php foreach (WC()->cart->get_coupons() as $code => $coupon): ?>
			<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
				<th>
					<?php wc_cart_totals_coupon_label($coupon); ?>
				</th>
				<td>
					<?php wc_cart_totals_coupon_html($coupon); ?>
				</td>
			</tr>
		<?php endforeach; ?>

		<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>

			<?php do_action('woocommerce_review_order_before_shipping'); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action('woocommerce_review_order_after_shipping'); ?>

		<?php endif; ?>

		<?php foreach (WC()->cart->get_fees() as $fee): ?>
			<tr class="fee">
				<th>
					<?php echo esc_html($fee->name); ?>
				</th>
				<td>
					<?php wc_cart_totals_fee_html($fee); ?>
				</td>
			</tr>
		<?php endforeach; ?>

		<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()): ?>
			<?php if ('itemized' === get_option('woocommerce_tax_total_display')): ?>
				<?php foreach (WC()->cart->get_tax_totals() as $code => $tax): // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited   ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
						<th>
							<?php echo esc_html($tax->label); ?>
						</th>
						<td>
							<?php echo wp_kses_post($tax->formatted_amount); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr class="tax-total">
					<th>
						<?php echo esc_html(WC()->countries->tax_or_vat()); ?>
					</th>
					<td>
						<?php wc_cart_totals_taxes_total_html(); ?>
					</td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action('woocommerce_review_order_before_order_total'); ?>

		<tr class="order-total">
			<th>
				<?php esc_html_e('Total', 'woocommerce'); ?>
			</th>
			<td>
				<?php wc_cart_totals_order_total_html(); ?>
			</td>
		</tr>

		<?php do_action('woocommerce_review_order_after_order_total'); ?>

	</tfoot>
</table>