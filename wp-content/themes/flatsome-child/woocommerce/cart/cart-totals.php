<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2><?php esc_html_e( 'Cart totals', 'woocommerce' ); ?></h2>

	<table cellspacing="0" class="shop_table shop_table_responsive">
                <?php 
                $products_array = array();
				                
                foreach(WC()->cart->get_cart() as $key => $val ) {
					$_product = $val['data'];

				   $type = $_product->get_type();
				 
					if($type == 'gift-card'){
                            $products_array[$_product->get_id()] = array(
                                'product_id' => $_product->get_id(),
                                'name' => $_product->get_name(),
                                'quantity' => $val['quantity'],
                                'price' => ($val['quantity'] * $_product->get_price()),
                                'price_after_discount' => ($val['quantity'] * $_product->get_price()),
                                'discount' =>''
                            );
					 
					  }else{
						  
						    if($_product->get_regular_price() > 0) {
                            $products_array[$_product->get_id()] = array(
                                'product_id' => $_product->get_id(),
                                'name' => $_product->get_name(),
                                'quantity' => $val['quantity'],
                                'price' => ($val['quantity'] * $_product->get_regular_price()),
                                'price_after_discount' => ($val['quantity'] * $_product->get_price()),
                                'discount' => ($val['quantity'] * ($_product->get_regular_price() - $_product->get_price()))
                            );
					  }
						   
					  }				
						
		}
		
	$total_quantity = 0;
	$price_of_all_items = 0;
	$discounts = 0;
	$price_after_discount = 0;

	foreach($products_array as $arr) {
	$total_quantity += $arr['quantity'];
	$price_of_all_items += floatval($arr['price']);
	$discounts += floatval($arr['discount']);
	$price_after_discount += floatval($arr['price_after_discount']);
	} 
                    if($discounts > 0){
                ?>
                <tr class="cart-subtotal">
			<th><?php esc_html_e( $total_quantity.' ITEM'.((count($products_array) > 1) ? 'S': '').' BEFORE DISCOUNT', 'woocommerce' ); ?></th>
                        <td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><span class="amount">$<?php echo wc_format_decimal($price_of_all_items,2); ?></span></td>
		</tr>
                <tr class="cart-subtotal cart_discounts" onclick= "toggle_discount_items()">
			<th><?php esc_html_e( 'DISCOUNTS', 'woocommerce' ); ?><i class="fa fa-angle-down"></i></th>
			<td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php echo '-<span class="amount">$'.wc_format_decimal($discounts,2).'</span>'; ?></td>
		</tr>
                <?php foreach($products_array as $arr) {
                    if($arr['discount'] > 0 ) { ?>
                        <tr class="cart-subtotal-items discounted_items">
                                <th><?php esc_html_e( $arr['name'], 'woocommerce' ); ?></th>
                                <td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php echo 'SAVE <span class="amount">$'.wc_format_decimal($arr['discount'],2).'</span>'; ?></td>
                        </tr>
                    <?php }
                } ?>
		<tr class="cart-subtotal">
			<th><?php esc_html_e( 'SUBTOTAL AFTER DISCOUNT', 'woocommerce' ); ?></th>
		
                        <td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><span class="amount">$<?php echo number_format((float)$price_after_discount, 2, '.', '');?></span></td>
		</tr>
<?php } ?>
		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

		<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>

			<tr class="shipping">
				<th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
			</tr>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				/* translators: %s location. */
				$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
						<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
					<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
				<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
		<div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem;">
			<svg width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M20.6251 29.684C26.1222 27.3991 30 21.8654 30 15.4018C30 6.89561 23.2843 0 15 0C6.71573 0 0 6.89561 0 15.4018C0 22.5657 4.76348 28.5872 11.2156 30.3092C11.2148 30.279 11.2014 30.3424 11.2156 30.3092C14.8472 21.7341 15.4007 19.3055 13.8525 18.4825C11.4959 17.2297 8.94503 17.7492 6.57594 19.5967C7.1504 18.0893 9.12913 17.3028 9.76743 17.1062C9.74324 17.0881 9.71862 17.0698 9.69362 17.0512C8.99298 16.5292 7.99379 15.7848 7.7887 14.6812C9.769 16.7271 11.4182 17.0377 14.6823 17.1062C11.778 11.9571 5.46218 10.8087 5.46218 10.8087C8.94902 10.8087 13.8525 12.9704 15.2568 15.6643C14.9359 12.7002 14.9359 11.9571 14.9359 10.5385C14.9359 9.11991 13.8495 8.06597 12.5017 7.22847C13.4921 7.39426 14.3609 7.89962 15.1332 8.7146C15.3054 7.24251 15.7911 5.66716 16.2781 4.58814C15.7019 8.93907 15.528 10.8087 16.7122 14.6812C18.0938 11.9571 21.8438 9.66033 24.0148 9.66033C22.3705 10.2022 21.6616 10.7428 21.1201 11.4842C21.9411 11.4486 22.828 11.5115 24.278 12.2273C18.8404 10.7666 17.1465 16.2128 17.4359 18.3069C17.7252 20.401 17.812 21.3356 17.1727 23.576C16.5334 25.8163 17.6629 28.8787 20.528 29.6556C20.5626 29.665 20.595 29.6745 20.6251 29.684ZM16.8168 4.4326C16.4756 4.04252 16.8581 3.38235 17.2075 3.01538C17.6902 2.50831 18.4784 2.244 18.8199 2.58683C19.1615 2.92966 18.9468 3.72856 18.4011 4.23116C17.8555 4.73375 17.6136 4.75497 17.253 4.7351C17.1377 4.72875 16.9874 4.62764 16.9021 4.53012C17.8093 3.6055 18.3365 3.03123 16.8168 4.4326ZM14.5131 4.17046C14.8912 4.54244 14.9936 4.91967 14.9255 5.45558C14.6285 5.33565 14.2127 5.16775 14.8555 5.63856C14.8321 5.69955 14.7026 5.85854 14.48 5.90256C14.3758 5.92316 13.9298 5.9517 13.3429 5.4434C12.756 4.9351 12.4955 4.11586 12.8306 3.75683C13.1657 3.3978 13.9908 3.65647 14.5131 4.17046ZM20.1689 9.76779C20.1689 10.2462 19.9416 10.7356 19.6412 10.9821C19.4613 10.5112 19.3698 10.2718 19.3414 10.2791C19.3119 10.2866 19.3498 10.5579 19.4269 11.1095C19.3766 11.1286 19.289 11.1196 19.2622 11.1095C18.8309 10.9475 18.5093 10.3633 18.5093 9.76779C18.5093 9.1723 18.8284 8.39146 19.2737 8.39146C19.719 8.39146 20.1689 9.10676 20.1689 9.76779ZM22.8895 13.0991C23.1886 13.2021 23.4977 13.5138 23.6287 13.8826C23.8097 14.3922 23.6586 15.0736 23.3154 15.2021C22.9721 15.3306 22.5123 14.8207 22.3493 14.3616C22.1862 13.9025 22.2742 13.3593 22.5623 13.1099C22.5803 13.0944 22.6453 13.0622 22.6893 13.0624C22.7808 13.5095 22.8259 13.7295 22.8507 13.7273C22.8747 13.7251 22.8796 13.5143 22.8895 13.0992V13.0991ZM13.1781 9.08615C13.6289 9.30784 13.9847 9.76036 14.0778 10.173H14.0777C13.5506 10.1336 13.2827 10.1135 13.2764 10.1452C13.27 10.1779 13.5431 10.2659 14.0986 10.445C14.0931 10.5038 14.0441 10.5867 14.0222 10.6086C13.6697 10.9619 12.9702 11.0107 12.4091 10.7348C11.848 10.4588 11.2602 9.77991 11.4665 9.33761C11.6728 8.8953 12.5553 8.77983 13.1781 9.08615ZM7.4725 12.5343C7.13414 12.2919 6.57474 12.2007 6.09545 12.3462C5.43317 12.5472 4.85335 13.24 4.98876 13.7103C5.12416 14.1806 6.00353 14.2803 6.60014 14.0992C7.19675 13.9181 7.68426 13.4007 7.71546 12.8959C7.7174 12.8645 7.69973 12.7693 7.66536 12.7219C7.13718 12.9706 6.87694 13.0931 6.85949 13.065C6.84253 13.0376 7.05456 12.8682 7.4725 12.5343ZM3.89716 9.95877C4.3959 9.99209 5.11006 10.2208 5.11006 10.7452C3.06751 10.7452 3.83347 10.8107 5.11006 10.8762C5.11006 11.0073 5.06406 11.1856 4.9824 11.2695C4.72708 11.5316 4.53327 11.6818 3.80026 11.6823C3.06725 11.6827 2.38761 11.2356 2.41861 10.7462C2.44962 10.2568 3.20801 9.91274 3.89716 9.95877ZM12.1638 6.86902C12.0361 6.3447 11.8063 6.03214 11.3219 5.82802C10.6526 5.54598 9.79252 5.61531 9.60864 6.07536C9.42476 6.53541 9.95917 7.19992 10.6865 7.45241C11.4139 7.7049 11.8188 7.51074 11.9084 7.45241C12.0999 7.32779 12.1638 7.13117 12.1638 7.06563C11.3978 6.86902 11.8446 6.86902 12.1638 6.86902ZM7.49267 8.19952C7.78497 8.73074 7.78497 9.18951 7.68002 9.53887C7.54336 9.49601 7.47387 9.47422 7.45966 9.49339C7.44496 9.51322 7.48942 9.5769 7.57987 9.70645C7.54649 9.76231 7.39195 9.89563 7.16526 9.89903C7.05916 9.90062 6.61514 9.84869 6.12377 9.2429C5.63239 8.63711 5.51535 7.78374 5.90639 7.49036C6.29744 7.19697 7.20037 7.66831 7.49267 8.19952ZM8.0273 16.6161C7.93036 16.3596 7.7217 16.0888 7.30758 15.9154C6.89345 15.742 6.14613 15.8967 6.04875 16.2574C5.95136 16.6181 6.40857 17.0657 6.97411 17.1876C7.53966 17.3096 7.82536 17.1273 7.88726 17.0755C8.01952 16.9648 8.05011 16.812 8.04441 16.763C7.93259 16.7301 7.87738 16.7138 7.87656 16.6952C7.87576 16.677 7.92669 16.6565 8.0273 16.6161ZM5.45872 17.3663C5.90527 17.3917 6.18847 17.5779 6.36153 17.788C6.27902 17.86 6.23736 17.8964 6.24415 17.9133C6.25108 17.9305 6.30845 17.9274 6.42433 17.9212C6.44527 17.9656 6.46483 18.1203 6.37492 18.2692C6.33284 18.3389 6.12056 18.6068 5.54718 18.6807C4.9738 18.7546 4.39951 18.4844 4.37713 18.1108C4.35474 17.7372 5.01217 17.3409 5.45872 17.3663ZM6.36202 19.7081C6.10464 19.6314 5.7684 19.6373 5.3828 19.8699C4.9972 20.1025 4.66327 20.8062 4.88328 21.1046C5.10329 21.4031 5.72723 21.3017 6.16495 20.9143C6.60267 20.5269 6.63507 20.1832 6.63255 20.1012C6.62717 19.9261 6.52749 19.8084 6.48612 19.7833C6.39306 19.8543 6.34695 19.8895 6.33185 19.8792C6.31702 19.869 6.33211 19.8151 6.36202 19.7081ZM8.77454 20.4886C8.81615 20.0314 8.67868 19.7163 8.50192 19.5095C8.42086 19.5821 8.37965 19.619 8.36416 19.6103C8.34815 19.6013 8.35961 19.5435 8.38291 19.426C8.34325 19.3982 8.19714 19.3554 8.04042 19.4247C7.96707 19.4571 7.67763 19.6331 7.52163 20.2044C7.36563 20.7757 7.54093 21.3989 7.89744 21.4769C8.25395 21.5549 8.73294 20.9459 8.77454 20.4886ZM11.3188 18.7181C10.7955 18.6601 10.4305 18.7737 10.0767 19.17C9.58783 19.7177 9.35432 20.5704 9.71135 20.9064C10.0684 21.2424 10.8603 20.9568 11.3421 20.343C11.824 19.7293 11.7863 19.272 11.7639 19.1655C11.7161 18.938 11.5584 18.8086 11.4985 18.786C11.0544 19.4562 11.2086 19.0257 11.3188 18.7181ZM9.13173 9.75585C9.33369 10.096 9.58323 10.2322 9.97177 10.2669C9.93454 10.0271 9.88242 9.69145 10.1119 10.244C10.1586 10.2363 10.2913 10.1654 10.3577 10.007C10.3888 9.9329 10.48 9.60603 10.2152 9.08901C9.95041 8.57198 9.41452 8.24799 9.10813 8.43981C8.80174 8.63162 8.85266 9.28589 9.13173 9.75585ZM24.722 12.4903C25.0586 12.288 25.3389 12.251 25.6952 12.4086C26.1875 12.6263 26.5975 13.1299 26.4555 13.4683C26.3136 13.8067 25.6939 13.8605 25.1867 13.5959C24.6795 13.3313 24.5698 13.0105 24.5535 12.9315C24.5185 12.7628 24.5868 12.6262 24.6206 12.5922C25.119 12.9068 24.8874 12.6639 24.722 12.4903ZM16.7755 11.5913C16.786 11.9653 17.0581 12.3243 17.455 12.4766C17.4222 12.3959 17.4255 12.2334 17.5848 12.4591C17.5848 12.4591 17.6448 12.3976 17.6616 12.3823C17.7088 12.3397 17.9432 12.1137 17.9617 11.5653C17.9801 11.017 17.6899 10.4965 17.353 10.5065C17.0161 10.5165 16.7609 11.0745 16.7755 11.5913ZM11.3744 15.9856C10.9306 16.0687 10.491 15.893 10.2827 15.557C9.99477 15.0927 9.9315 14.4395 10.2342 14.2417C10.5369 14.0438 11.0788 14.357 11.3533 14.8687C11.6278 15.3803 11.5336 15.7183 11.5132 15.7837C11.5059 15.807 11.4838 15.8969 11.4838 15.8969C11.2182 15.7798 11.3016 15.9302 11.3744 15.9856ZM22.9685 7.93244C22.9335 8.44436 22.7071 9.17648 22.1965 9.17381C22.2069 7.07657 22.1391 7.8627 22.0688 9.17314C21.9412 9.17248 21.7677 9.12434 21.6865 9.04007C21.4325 8.77658 21.2872 8.57682 21.2905 7.82418C21.2938 7.07154 21.7326 6.37597 22.2091 6.4103C22.6856 6.44463 23.0168 7.22507 22.9685 7.93244ZM24.359 9.46502C24.4198 9.04606 24.7395 8.56597 25.1717 8.30823C25.7689 7.95209 26.6575 7.99525 26.8974 8.41934C27.1373 8.84342 26.6038 9.56809 26.0658 9.88892C25.5278 10.2097 24.8267 10.2181 24.448 9.89473C24.4245 9.8746 24.3692 9.79602 24.3593 9.73782C24.8993 9.514 25.1647 9.40397 25.1556 9.37188C25.1468 9.34086 24.8813 9.38271 24.359 9.46502ZM16.9657 6.92265C16.6529 7.27715 16.5012 7.79646 16.5626 8.18628C17.0036 7.96155 17.2279 7.84722 17.2445 7.87203C17.2617 7.89772 17.0563 8.07258 16.6381 8.42848C16.663 8.47729 16.7337 8.53104 16.7602 8.54206C17.1857 8.71929 17.8061 8.50812 18.1955 8.06685C18.5849 7.62557 18.859 6.8269 18.5291 6.51992C18.1992 6.21294 17.398 6.4328 16.9657 6.92265Z" fill="#164B22"/>
			</svg>
			<p style="margin: 0; line-height: 1.25; font-size: 1rem; max-width:300px;">Every order supports reforestation by planting two trees with Our Forest.</p>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
