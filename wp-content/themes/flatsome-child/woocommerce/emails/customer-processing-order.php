<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email); ?>

<!-- CUSTOM START -->
<style>
	.chide {
		display: none;
	}
</style>
<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/processing1.jpg">
<?php
$static_location_name    = 'Melbourne, Australia';
$static_support_guy_name = 'Joana';
$link_button             = 'https://www.messenger.com/t/plus2clothing';
$image_facebook          = get_stylesheet_directory_uri() . '/images/facebookemailimage.jpg';
$cust_name      = $order->get_billing_first_name();
//$prefix    = esc_attr(get_option('customer_append_string'));change now with ACF 
//$cust_name = str_replace($prefix, '', $cust_name);
?>
<?php
$has_only_virtual_products = true; // Assume true initially
foreach ($order->get_items() as $item_id => $item) {
	$product = $item->get_product();
	if ($product && !$product->is_virtual()) {
		$has_only_virtual_products = true;
		break;
	}
}
?>
<p>
	<?php printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($cust_name)); ?>
</p>
<p>
	<?php printf(__("Thanks for choosing to shop with %s", 'woocommerce'), get_option('blogname')); ?>
</p>

<?php 

		if ( !$has_only_virtual_products ) { ?>
<p>
	<?php printf(__("Your order is currently being processed with our team in %s. We will notify you with tracking details as soon as it’s all packed.", 'woocommerce'), $static_location_name); ?>
</p>
<?php }else{ ?>

<p>
	<?php printf(__("Your order is currently being processed with our team in %s. We will notify you ssoon with details.", 'woocommerce'), $static_location_name); ?>
</p>
	<?php } ?>
<?php if (!$sent_to_admin): ?>
	<h2>
		<?php printf(__('Your order number is: #%s', 'woocommerce'), $order->get_order_number()); ?>
	</h2>
	<p>
		<?php printf(__("In the mean time we’d like to introduce you to our customer service specialist/rock-star %s. If you have any questions about anything please don’t hesitate to flick her a message here: ", 'woocommerce'), $static_support_guy_name); ?>
	</p>
	<p><a href="<?php echo esc_url($link_button); ?>"><img src="<?php echo esc_url($image_facebook); ?>" alt="<?php esc_attr_e('Message us on Facebook', 'woocommerce'); ?>" /></a></p>
	<p>
		<?php echo "For your reference your order details are below:"; ?>
	</p>
<?php endif; ?>

<!-- CUSTOM END -->

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
	echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
