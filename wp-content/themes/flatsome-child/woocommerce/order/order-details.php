<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 *
 * @var bool $show_downloads Controls whether the downloads table should be rendered.
 */

 // phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&family=Outfit:wght@100;200;800;900&display=swap" rel="stylesheet">

<img style="display:none;" class="img-border" src="<?php echo get_stylesheet_directory_uri();?>/images/thankyouheader.jpg">
<section class="woocommerce-order-details">
<?php // CUSTOM CODE ?>


<p style="display:none; color:red;font-weight:bold;margin:20px 0;">Please check your email inbox/spam folder for order receipt. Once order is processed we will email you with tracking details.</p>
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
<p class="woocommerce-notice woocommerce-notice–success woocommerce-thankyou-order-received" style="text-align:center;" ><b><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'THANKS FOR SHOPPING WITH US!', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></b></p>
<br/><br/>


<?php
$all_virtual = true; // Initialize as true (we assume all items are virtual until proven otherwise)

// Loop through order items to check if any item is non-virtual
foreach ($order->get_items() as $item) {
    $product = $item->get_product();
    if ($product && !$product->is_virtual()) {
        $all_virtual = false; // Set to false if any item is not virtual
        break; // No need to check further, exit loop
    }
}
?>


  <p class="client-first-name">Hey <?php $billing_first_name = $order->get_billing_first_name();
  echo($billing_first_name); ?></p>


<?php if ($all_virtual) { // Only show this section if all items are virtual ?>
    <p class="thankyou-note"> Your order has been received and should be processed within one business day.</p>
<?php }else{ ?>
<p class="thankyou-note"> Your order has been received by our Melbourne warehouse and should be processed within one business day.</p>
    <?php } ?>
 
<div class="row order-via">
   
					<div class="col-md-3 order-via_col">
						<h6>Order Number</h6>
							<span><?php $order_number = $order->get_order_number(); 
							echo($order_number);?></span>
					</div>
		
					<div class="col-md-3 order-via_col">
					<h6>Date</h6>
					<span>
    <?php
    $date_created = $order->get_date_created();
    // Convert the date string to a DateTime object
    $date = new DateTime($date_created);
    // Format and display only the date
    echo $date->format('d-m-Y'); // Adjust the format as needed
    ?> 
</span>

					</div>
				
					<div class="col-md-3 order-via_col">
					<h6>Payment Method</h6>
					<span><?php 
					$payment_method_title = $order->get_payment_method_title();
					echo($payment_method_title);
					?></span>
					</div>
					
	<div class="col-md-3 order-via_col">
    <h6>Address</h6>
    <span>
        <?php 
        $shipping_address_1 = $order->get_shipping_address_1();
        $shipping_address_2 = $order->get_shipping_address_2();

        if (empty($shipping_address_1) && $shipping_address_1 != '#') {
            // Display shipping address
            echo $shipping_address_1;
            if (!empty($shipping_address_2)) {
                echo ', &nbsp;' . $shipping_address_2;
            }
        } else {
            // If shipping address is blank or just '#', show billing address
            echo '<h6>Billing Address</h6>';
            $billing_address_1 = $order->get_billing_address_1();
            if (!empty($billing_address_1) && $billing_address_1 != '#') {
                echo $billing_address_1;
                $billing_address_2 = $order->get_billing_address_2();
                if (!empty($billing_address_2)) {
                    echo ', &nbsp;' . $billing_address_2;
                }
            } else {
                echo 'No address available';
            }
        }
        ?>
    </span>
</div>



</div>
	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

	<table class="woocommerce-table table-layout woocommerce-table--order-details shop_table order_details">
	

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>


		</tfoot>
	</table>
	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
	
 

	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

    <?php if ($all_virtual) { // Only show this section if all items are virtual ?>
<p class="woocommerce-notice woocommerce-notice–success woocommerce-thankyou-order-received successfuly-action"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Please check your email inbox/spam folder for order receipt. Once order is processed we will email you with details.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php }else{ ?>
    <p class="woocommerce-notice woocommerce-notice–success woocommerce-thankyou-order-received successfuly-action"><?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Please check your email inbox/spam folder for order receipt. Once order is processed we will email you with tracking details.', 'woocommerce'), $order); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
    <?php } ?>

<p class="thanks-msg">We really appreciate you supporting our dreams of ridding the world of plumbers crack, one tall tee at a time.</p><br/>
<p class="team-msg" style="margin-top:-20px;">Sincerely,<br/>Plus2 Team</p>
 
</section>

<div class="woocommerce-footer">
<span>Need Help? <a href="<?php echo esc_url(home_url('/contact/')); ?>" style="text-decoration:underline;">Drop us a line.</a></span>
<a href="<?php echo esc_url(home_url('/')); ?>"><span><?php echo esc_html(get_bloginfo('name')); ?></span></a> 

</div>

<?php // CUSTOM CODE ?>
<script>
 pintrk('track', 'Checkout', {
   value: <?php echo esc_js($order->get_total()); ?>,
   order_quantity: <?php echo absint($order->get_item_count()); ?>,
   currency: <?php echo wp_json_encode($order->get_currency()); ?>,
 }, function(didInit, error) { if (!didInit) { console.log(error); }
 });
</script>

<!-- Event snippet for Purchase website conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': 'AW-991401870/IWpqCNmy6-EBEI6v3tgD',
      'value': <?php echo esc_js($order->get_total()); ?>,
      'currency': <?php echo wp_json_encode($order->get_currency()); ?>,
      'transaction_id': ''
  });
</script>


<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
?>
<?php // CUSTOM CODE ?>
<div class="iframe-container">
<iframe class="full-width-section hh" src="https://docs.google.com/forms/d/e/1FAIpQLSeYuvCPAegBl1Bd3_ErxOGIzZQZNceNumcVTf0q0Ex1hJJ7eQ/viewform?embedded=true" width="1200" height="649" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe>
</div>
<a class="welcome-footer" href="<?php echo esc_url('http://instagram.com/_u/plus2clothing/'); ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/thankyoufooter.jpg'); ?>" alt="<?php esc_attr_e('Thank you footer', 'woocommerce'); ?>"> </a>
<!--
<script>
jQuery(document).ready(function($) {
    var iframe = $('.iframe-container iframe');

    // Wait for the iframe to load
    iframe.on('load', function() {
        setTimeout(function() {
            // Remove the inline height attribute
            iframe.removeAttr('height');

            try {
                var iframeContentHeight = iframe.contents().find("body").height();
                iframe.height(iframeContentHeight);
                console.log(iframeContentHeight); // Use console.log for debugging
            } catch (error) {
                console.error("Error adjusting iframe height:", error);
            }
        }, 2000); // Adjust after 2 seconds, consider adjusting this based on actual load times
    });
});


</script>
-->
<style>

   @import url('https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&family=Outfit:wght@100;200;800;900&display=swap');
  section.woocommerce-order-details{font-family: 'Lato', sans-serif; padding:7% 7% 0 7%;}
  .woocommerce-order-received .large-7.col>p {
    display: none;
}
/*
.iframe-container iframe {
    width: 100%; /* Ensure iframe is responsive and fits the container's width */
}
*/

@media screen and (min-width: 768px) {
    .woocommerce-column--billing-address.col-1 {
        width: 50%;
        border-right: 1px solid #ffd8d8;
    }
    .woocommerce-column--shipping-address.col-2 {
        width: 50%;
        text-align: right;
    }
}

.woocommerce-footer {
    background-color: #fbfbfd;
    padding: 3% 7%;
    font-family: 'Lato', sans-serif;
    display: flex;
    justify-content: space-between;
}
.woocommerce-footer span {
    font-size: 14px;
}
.woocommerce-footer span a {
    color: #9e3333;
}
.main-justify-content h2.pr-name a {
    color: #000;
}
.woocommerce-order-details .successfuly-action {
    font-size: 18px;
    font-weight: 500;
    margin-bottom: 15px;
    margin-top: 30px;
    color: #b3b1bb;
}
.order-via_col h6 {
    text-transform: capitalize;
    letter-spacing: normal;
    font-weight: 500;
    color: #b3b1bb;
}
.order-via_col span {
    font-weight: 600;
    font-size: 14px;
    color: #000;
}
.team-msg {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 30px;
    color: #000;
}
.thanks-msg {
    font-size: 20px;
    font-weight: 500;
    margin: 0;
    color: #000;
}
.order-via_col {
    display: flex;
    flex-direction: column;
    width: 25%;
	margin: 0!important;
}
table.table-layout tfoot tr:last-child th {
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
}
table.table-layout tfoot tr:last-child td {
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
}
.order-via {
    padding: 15px!important;
    border-top: 1px solid #e9e7e7;
	margin: 0!important;
}
.main-justify-content h3.qunty-ord strong {
    font-family: open_sanscondensed_light;
}
.main-justify-content h3.qunty-ord {
    color:#b3b1bb    
    font-size: 15px;
}
.thankyou-note {
    color: #000;
}
table.table-layout thead {
    display: none;
}
.shop_table tfoot th {
    color: #b3b1bb;
    font-weight: 500;
	width: 50%;
}
.main-justify-content ul.wc-item-meta {
    font-size: 15px;color: #b3b1bb;
}
.main-justify-content ul.wc-item-meta strong { 
    font-weight: normal;
	font-family: 'Lato', sans-serif;
}
.main-justify-content {
    padding: 0px 15px!important;
}
.table-row {
    border-top: 1px solid #e9e7e7;
    padding: 1.2em 0 0!important;
    margin: 0!important;
}
table.table-layout td, th {
    border: none;
}
.woocommerce-thankyou-order-received {
    font-size: 30px;
    font-weight: 700;
    color: #000;
    margin-bottom: 30px;
    width: 100%;
    display: inline-block;
    line-height: 23px;
}
.client-first-name {
    font-size: 20px;
    font-weight: 600;
    color: #000;
    text-transform: capitalize;
    margin-bottom: 10px;
    line-height: 20px;
}
.woocommerce-order-details__title {
    display: none;
}

.woocommerce-order-received tr.mbold td{
font-weight:bold;
		    font-size: 85%;
    text-transform: inherit;
    letter-spacing: 0;
color:#777777;
}
	
.woocommerce-order-received .woocommerce-info.message-wrapper {
    display: none;
}
.woocommerce-order-received .large-5 {
    display: none;
}
.woocommerce-order-received .large-7.col {
    margin: 0 auto;
    background-color: #ffffff;
    padding: 0;
    max-width:75%;
    flex-basis: 75%;
}
.woocommerce-order-received #main {
    background-color:#f1f1f1;
}
 .welcome-footer {
    margin: 25px 0 0;
    display: inline-block;
}
.img-border {
    border: 1px solid #000;
}	
.aw-referrals-well {
    margin: 0;
    padding: 25px 25px 0;
}	

@media only screen and (max-width:767px) and (min-width: 320px)  {
.woocommerce-order-received .large-7 p {
    text-align: center;
    padding-bottom:0;
    margin-bottom: 0;
}
 section.woocommerce-customer-details {
    margin: 0 0 20px;
}
}

@media only screen and (max-width: 767px) {
.woocommerce-order-received .large-7.col {
    max-width: 95%;
    flex-basis: 95%;
}
section.woocommerce-order-details { 
    padding: 15px;
}
.woocommerce-thankyou-order-received {
    font-size: 24px;
    line-height: 28px;
}
.client-first-name {
    font-size: 17px;
	margin-top: 15px;
}
.thankyou-note { 
    font-size: 15px;
    line-height: 18px;
}
.woocommerce-order-details .successfuly-action {
    font-size: 16px;
    line-height: 22px;
}
.order-via_col { 
    width: 100%;
    margin: 0 0 18px!important;
    text-align: center;
}
.order-via_col h6 { 
    margin: 0;
}
.order-via {
    padding: 20px 0 8px!important;
    margin: 20px 0 5px!important;
	margin-bottom: 15px!important;
}
.thanks-msg {
    font-size: 18px;
}
.row.main-justify-content {
    display: inline-block;
    width: 100%!important;
}
.main-justify-content .col-md-3-main {
    width: auto;
    flex: 100%;
    display: inline-block;
}



}


</style>
 
 


<?php
