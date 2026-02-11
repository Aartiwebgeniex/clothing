<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
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

if (!defined("ABSPATH")) {
    exit();
} ?>

<?php if (!apply_filters("woocommerce_order_item_visible", true, $item)) {
    return;
} ?>

</div>
<div class="row gap-3 main-justify-content table-row">

    <div class="col-md-1-main">
       <p class="ord-img"> <?php
       $is_visible = $product && $product->is_visible();
       $product_permalink = apply_filters(
           "woocommerce_order_item_permalink",
           $is_visible ? $product->get_permalink($item) : "",
           $item,
           $order
       );
       $thumbnail = $product->get_image([150, 150]);

       echo $thumbnail;
       ?></p>
    </div>
    <div class="col-md-7-main">
    <div class="main-inner-top">
                    <h2 class="pr-name"><?php echo wp_kses_post(
                        apply_filters(
                            "woocommerce_order_item_name",
                            $product_permalink
                                ? sprintf(
                                    '<a href="%s">%s</a>',
                                    $product_permalink,
                                    $item->get_name()
                                )
                                : $item->get_name(),
                            $item,
                            $is_visible
                        )
                    ); ?></h2>
                <?php
                $qty = $item->get_quantity();
                $refunded_qty = $order->get_qty_refunded_for_item($item_id);

                if ($refunded_qty) {
                    $qty_display =
                        "<del>" .
                        esc_html($qty) .
                        "</del> <ins>" .
                        esc_html($qty - $refunded_qty * -1) .
                        "</ins>";
                } else {
                    $qty_display = esc_html($qty);
                }
                ?>
                <h3 class="qunty-ord"><?php echo apply_filters(
                    "woocommerce_order_item_quantity_html",
                    ' <strong class="product-quantity">' .
                        sprintf("&times;&nbsp;%s", $qty_display) .
                        "</strong>",
                    $item
                );
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?></h3>
                <?php
                do_action("woocommerce_order_item_meta_start", $item_id, $item, $order, false);

                wc_display_item_meta($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                do_action("woocommerce_order_item_meta_end", $item_id, $item, $order, false);
                ?>
                </div>
				<div class="main-inner-bottom">
									<?php echo $order->get_formatted_line_subtotal($item);
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
     </div>
     </div>

    </div>
     
</div>

 
	<div class="col-md-6">
 
<!--<tr class="mbold">
	<td>  Order number: </td>
	<td> <?php
//echo $order->id;
?> </td>
</tr>-->
<!--<tr class="mbold">
	<td>  Order Date: </td>
	<td> <?php
//$olddate = $order->date_created;
//echo $newdate = substr($olddate,0,10);
?> </td>
</tr>-->
<?php if ($show_purchase_note && $purchase_note): ?>

<tr class="woocommerce-table__product-purchase-note product-purchase-note">

	<td colspan="2"><?php echo wpautop(do_shortcode(wp_kses_post($purchase_note)));
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?></td>

</tr>

<?php endif; ?>

	</div>

	

<style>

.main-justify-content .col-md-7-main {
    display: flex;
    justify-content: space-between;
}
 
.main-justify-content h2.pr-name {
    margin-bottom: 12px;
    font-size: 17px!important;
}
 .main-justify-content {
    justify-content: space-between;
}

 .main-justify-content    p.ord-img {
 width: 100px;
    height: 100px;
}

.main-justify-content  .col-md-3-main {
    width: 20%;
    flex: 20%;
}

.main-justify-content  .col-md-7-main {
    width: 65%;
    flex: 65%;
}

.row.main-justify-content {
    gap:3%;
    padding: 0px 12px;
}

.main-justify-content  .col-md-2-main {
    flex: 10%;
    width: 10%;
}

.main-justify-content  h2.pr-name {
    font-size: 20px;
    font-weight: 600;
    line-height: 16px;
}
.main-justify-content h3.qunty-ord {
    margin: 0px;
    padding: 0px;
    font-size: 16px;
    color: #a5a5a5;
    line-height: 24px;
}
.main-justify-content ul.wc-item-meta {
    margin: 0px;
    color: #a5a5a5;
    line-height: 24px;
    list-style: none;
}
.main-justify-content ul.wc-item-meta li p {
    display: inline-block;
}
 
table.table-layout {
    border-color: #ececec;
    border-spacing: 0;
    margin-bottom: 1em;
    width:50%;
    float: right;
}
.shipped_via {
    width: 100%;
    line-height: 20px;
}
.woocommerce-customer-details {
    padding: 3% 7%;
    font-family: 'Lato', sans-serif;
}
h2.woocommerce-column__title {
    font-size: 18px;
    font-weight: 500;
    color: #000;
}
.woocommerce-customer-details address {
    color: #b3b1bb;
    font-size: 16px;
    font-style: normal;
}
.main-justify-content p.ord-img img {
    border-radius: 5px;
}
/*
iframe.full-width-section {
    height: 700px;
}
 */

@media screen and (max-width: 576px) {
     table.table-layout {
	    width: 100%;
	    float: right;
	}

	.main-justify-content  .col-md-3-main {
    width: 100%;
    flex: 100%;
}

.main-justify-content  .col-md-7-main {
    width: 100%;
    flex: 100%;
}

.row.main-justify-content {
    gap: 2%;
    padding: 0px 12px;
}

.main-justify-content  .col-md-2-main {
    flex: 100%;
    width: 100%;
}
}

@media screen and (max-width: 767px) {
    .main-justify-content p.ord-img {
    width: auto;
    height: auto;
}
    .woocommerce-customer-details .woocommerce-column {
    margin-top: 25px;
}
h2.woocommerce-column__title {
    text-align: center;
}
.woocommerce-customer-details address {
    text-align: center;
}
.row.main-justify-content {
    display: inline-block;
    width: 100%!important;
}
.main-justify-content .col-md-1-main {
    width: 25%; 
    float: left;
}
.main-justify-content .col-md-7-main {
    display: inline-block;
    width:75%!important;
    padding: 0 0 0 10px;
}
.main-justify-content h2.pr-name {
    font-size: 16px; 
    margin-bottom:8px;
}
.main-justify-content h3.qunty-ord {
    line-height: 20px;
}
.table-row { 
    padding: 1.2em 0 15px!important; 
}
.amount { 
    font-size: 15px;
}
.order-via_col:last-child {
    margin: 0!important;
}
.woocommerce-order-details .order-via { 
    padding: 20px 0 0px!important;
}
.woocommerce-order-details .table-row { 
    padding: 1.3em 0!important; 
}
table.table-layout { 
    width: 100%; 
}

}


@media only screen and (max-width:1024px) and (min-width:768px) {
    .woocommerce-order-received .large-7.col {
    max-width:100%!important;
    flex-basis:100%!important;
}
 
}








</style>