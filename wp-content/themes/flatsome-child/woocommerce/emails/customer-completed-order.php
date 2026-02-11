<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
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
<style>
	.chide {
		display: none;
	}
</style>
<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/processed1.jpg">
<?php /* translators: %s: Customer first name */ ?>
<?php
$cust_name      = $order->get_billing_first_name();
//$prefix    = esc_attr(get_option('customer_append_string')); change now with ACF 
//$cust_name = str_replace($prefix, '', $name);
?>
<p>
	<?php printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($cust_name)); ?>
</p>

<?php 
$has_only_virtual_products = true; // Assume true initially
foreach ( $order->get_items() as $item_id => $item ) {
    $product = $item->get_product();
    if ( $product && ! $product->is_virtual() ) {
        $has_only_virtual_products = false;
        break;
    }
}
?>

<?php
/* CUSTOM START */
if ($order->get_status() == 'completed') {
	$customer_note = $order->get_customer_note();
	/*
	   if(strpos($customer_note, 'https://auspost.com.au') !== false){
		   $data = explode(',', $customer_note);
		   $tracking_code1 = $data[count($data)-1];
		   $tracking_link  = $tracking_code1;
		   $text = 'You can track your order via this link:'; 
	   }else{
		   $text ='';
		   $tracking_link = '';
	   }
	   */
	$notes             = wc_get_order_notes(['order_id' => $order->get_id()]);
	$tracking_base_url = 'https://auspost.com.au/mypost/track/details/';
	$tracking_link     = '';
	$text              = '';

	foreach ($notes as $note) {
		if (strpos($note->content, 'tracking ID:') !== false) {
			// Extract the tracking ID from the note
			$start         = strpos($note->content, 'tracking ID:') + strlen('tracking ID:');
			$tracking_code = trim(substr($note->content, $start));

			// Construct the full tracking URL
			$tracking_link = $tracking_base_url . $tracking_code;
			//$tracking_link = '';
			//$text          = 'Use following tracking code : ' . $tracking_code . ' on ' . $tracking_base_url . ' to track your package.';
			$text = 'You can track your order via this link:';
			break; // Stop the loop after finding the tracking ID
		}
	}

	if (empty($tracking_code)) {
		$text = " ";
	}


}
$static_location_name    = 'Melbourne, Australia';
$static_support_guy_name = 'Joana';
$link_facebook           = 'https://www.messenger.com/t/plus2clothing';
$image_facebook          = get_stylesheet_directory_uri() . '/images/facebookemailimage.jpg';

if (!$sent_to_admin): ?>
	<p>
		<?php echo "Good news! Your order is on it’s way."; ?>
	</p>
	<p>
		<?php printf(__($text . " %s", 'woocommerce'), $tracking_link); ?>
	</p>

	<?php 

		if ( !$has_only_virtual_products ) { ?>
	<p>
		<?php printf(__("All our orders are shipped direct from our warehouse in %s. Should you have any further questions on the whereabouts of your order please contact our customer service specialist %s, who will be more than happy to assist you.", 'woocommerce'), $static_location_name, $static_support_guy_name); ?>
	</p>
	<?php } ?>

 
	<p>
		<?php echo "This order helped restore nature by planting two trees with Our Forest."; ?>
	</p>
	<p><a href="<?php echo esc_url($link_facebook); ?>"><img src="<?php echo esc_url($image_facebook); ?>" alt="<?php esc_attr_e('Message us on Facebook', 'woocommerce'); ?>" /></a></p>
	<p>
		<?php echo "For your reference your order details are below:"; ?>
	</p>
<?php else: ?>
	<h2><a class="link" href="<?php echo esc_url(admin_url('post.php?post=' . $order->get_id() . '&action=edit')); ?>">
			<?php printf(__('Order #%s', 'woocommerce'), $order->get_order_number()); ?>
		</a>
		(
		<?php printf('<time datetime="%s">%s</time>', $order->get_date_created()->format('c'), wc_format_datetime($order->get_date_created())); ?>
		)
	</h2>
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
