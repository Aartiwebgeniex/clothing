<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          9.8.0
 * @flatsome-version 3.19.12
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<div class="woocommerce-form-coupon-toggle">
	<?php // wc_print_notice( apply_filters( 'woocommerce_checkout_coupon_message', __( 'Have a coupon/discount code?', 'woocommerce' ) . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>' ), 'notice' ); ?>
	<div class="message-container"><a href="#" class="showcoupon">Discount Code</a> 
	<!--/ <a href="#" class="ywgc-show-giftcard">Gift Card</a>
-->
</div>	
	<div class="message-container"></div>
</div>

<form class="checkout_coupon woocommerce-form-coupon has-border is-dashed" method="post" style="display:none" id="woocommerce-checkout-form-coupon">

	<p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'woocommerce' ); ?></p>
	<div class="coupon">
		<div class="flex-row medium-flex-wrap gap-half">
			<div class="flex-col flex-grow">
				<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
				<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Discount code', 'woocommerce' ); ?>" id="coupon_code" value="" />
			</div>
			<div class="flex-col">
				<button type="submit" class="button expand<?php if ( fl_woocommerce_version_check( '7.0.1' ) ) { echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); } ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
			</div>
		</div>
	</div>
</form>

