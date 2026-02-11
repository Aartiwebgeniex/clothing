<?php
/**
 * Side Cart Footer
 *
 * This template can be overridden by copying it to yourtheme/templates/side-cart-woocommerce/xoo-wsc-footer.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 * @see     https://docs.xootix.com/side-cart-woocommerce/
 * @version 4.0
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

extract(Xoo_Wsc_Template_Args::cart_footer());

?>



<?php /* CUSTOM START */ ?>

<div class="xoo-wsc-tools sidebar_cart_user">



    <?php

    $ca_product_op = get_field('product', 'option');
    if ($ca_product_op) {

        $thumbnail_url = get_field('cart_addon_thumbnail', 'option');
        if (!$thumbnail_url) {
            $thumbnail_id  = get_post_thumbnail_id($ca_product_op); // Assuming $ca_product_op holds the post ID
            $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'thumbnail')[0];
        }
        global $woocommerce;

        $cart_total     = $woocommerce->cart->get_cart_total();
        $removeHTML     = strip_tags($cart_total);
        $removeDollar   = preg_replace('/&.*?;/', '', $removeHTML);
        $ca_product     = wc_get_product($ca_product_op);
        $cartProduct_id = $ca_product_op;
        if ($ca_product->is_type('variable')) {
            $variations = $ca_product->get_available_variations();
        }
        $all_variations = array();


        $in_cart = false; // Default to false, indicating the product is not in the cart
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_in_cart   = $cart_item['product_id'];
            $variation_in_cart = isset($cart_item['variation_id']) ? $cart_item['variation_id'] : null;

            if ($product_in_cart === $cartProduct_id) {
                $in_cart = true; // The parent product is in the cart
                break; // No need to check further if we found the product
            }

            if (null !== $variation_in_cart && $variation_in_cart === $cartProduct_id) {
                $in_cart = true; // A specific variation of the product is in the cart
                break; // No need to check further if we found the variation
            }
        }

        ?>



        <a id="addon_modal" href="#myModal1">
            <?php if (!$thumbnail_url) {
                $thumbnail_url = get_site_url() . '/wp-content/uploads/2016/04/mysterybag-plus2-255x300-150x150.jpg';
            }
                ?>
            <img width="150" height="150" src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php esc_attr_e('Cart addon product', 'woocommerce'); ?>">
        </a>
        <!-- The Modal -->
        <div id="myModal1" class="modal" style="display:none;">

            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>

                <table class="variations" cellspacing="0">
                    <tbody>
                        <tr>

                            <td class="value">
                                <?php if ($ca_product->is_type('variable')): ?>
                                    <label for="pa_size" style="font-size:16px;letter-spacing: 1px;">SELECT YOUR SIZE : </label>
                                <?php else: ?>
                                    <label for="pa_size">Click on following button to <br /> add <?php echo get_the_title($ca_product_op); ?> in cart :
                                    </label>
                                <?php endif; ?>
                                <div id="pa_size" class="sidebar_addon_cart_variable" name="attribute_pa_size" data-attribute_name="attribute_pa_size" data-show_option_none="yes" style="display: none;">

                                    <?php if ($ca_product->is_type('variable')): ?>
                                        <?php foreach ($variations as $variation): ?>
                                            <?php foreach ($variation['attributes'] as $variation_attribute => $term_slug): ?>
                                                <?php
                                                $taxonomy   = str_replace('attribute_', '', $variation_attribute);
                                                $label_name = wc_attribute_label($taxonomy);
                                                $term_name  = get_term_by('slug', $term_slug, $taxonomy)->name;
                                                ?>

                                                <?php if (isset($variation['variation_id']) && $variation['is_in_stock'] == 1): ?>
                                                    <span class="variation-option attached enabled" data-id="<?php echo $variation['variation_id']; ?>" data-attribute-name="<?php echo esc_attr($variation_attribute); ?>" data-attribute-value="<?php echo esc_attr($term_slug); ?>">
                                                        <?php echo $term_name; ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>



                                    <?php else: ?>
                                        <!-- Simple Product Add to Cart Button -->
                                        <?php if (!$in_cart): ?>
                                            <a href="#" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-quantity="1" data-product_id="<?php echo esc_attr($cartProduct_id); ?>" data-product_sku="<?php echo esc_attr($ca_product->get_sku()); ?>">Add to Cart</a>
                                        <?php else: ?>
                                            <div style="color:red;">Product is already in cart. Max quantity allowed is 1 per order.
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>


                                </div>
                                <a class="reset_variations" href="#" style="visibility: hidden;">Clear</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($ca_product->is_type('variable')):
                                    //if (!$in_cart):
                                    ?>
                                    <a href="#" class="button add_to_cart_button ajax_add_to_cart variable_add_to_cart_button" data-quantity="1" data-product_id="<?php echo esc_attr($ca_product->get_id()); ?>" data-product_sku="<?php echo esc_attr($ca_product->get_sku()); ?>" style="display:none;">Add to Cart</a>
                                    <?php
                                    // else:      ?>
                                    <div style="color:red;display:none;">Product is already in cart. Max quantity allowed is 1
                                        per order.</div>
                                    <?php

                                    //endif;   
                                endif; ?>
                            </td>





                        </tr>
                    </tbody>
                </table>



            </div>

        </div>


    <?php } ?>

    <?php
    $products_array = array();  // Initialize as an empty array
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = $cart_item['data'];
        //  $type     = $_product->product_type;
        $type = $_product->get_type();

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


    }
    $total_quantity       = 0;
    $price_of_all_items   = 0.0; // initialize to a float value
    $discounts            = 0.0; // initialize to a float value
    $price_after_discount = 0.0; // initialize to a float value
    if (!empty($products_array)) { // Check if array is not empty
        foreach ($products_array as $arr) {
            $total_quantity += $arr['quantity'];
            $price_of_all_items += (float) $arr['price'];
            $discounts += (float) $arr['discount'];
            $price_after_discount += (float) $arr['price_after_discount'];
        }
        if ($discounts > 0) {
            ?>

            <div class="xoo-wsc-subtotal xoo-wsc-tool">



                <?php
                // Ensure WooCommerce is active
                if (class_exists('WooCommerce')) {

                    global $woocommerce;
                    $items = 0;

                    // Get the total count of items in the cart
                    $items = $woocommerce->cart->get_cart_contents_count();


                }
                ?>



                <div class="onlytomove" style="display:none;">
                    <?php esc_html_e($items . ' ITEM' . (($items > 1) ? 'S' : ''), 'woocommerce'); ?>

                </div>




                <?php esc_html_e($total_quantity . ' ITEM' . ((count($products_array) > 1) ? 'S' : ''), 'woocommerce'); ?>
                <span class="before_discount_color">BEFORE DISCOUNT</span>
                <span class="amount xoo-wsc-tools-value">
                    <?php echo number_format((float) $price_of_all_items, 2, '.', ''); ?>
                </span>
            </div>
            <div class="xoo-wsc-subtotal xoo-wsc-tool cart_discounts" onclick="show_discounted_items()">
                <div class="dis_outline">
                    <?php esc_html_e('DISCOUNTS', 'woocommerce'); ?>
                </div> <i class="fa fa-angle-down"></i>
                <div class="minus_part xoo-wsc-tools-value"> - <span class="amount">
                        <?php echo number_format($discounts, 2, '.', ''); ?>
                    </span></div>
            </div>
            <?php
            foreach ($products_array as $arr) {
                if ($arr['discount'] > 0) {
                    ?>
                    <div class="xoo-wsc-subtotal xoo-wsc-tool discounted_items">
                        <?php esc_html_e($arr['name'], 'woocommerce'); ?>
                        <?php echo 'SAVE <span class="amount xoo-wsc-tools-value">' . number_format($arr['discount'], 2, '.', '') . '</span>'; ?>
                    </div>
                    <?php
                }
            }
        }
    }
    $labelc = '';
    if ($discounts > 0) {
        $labelc = 'SUBTOTAL AFTER DISCOUNT';

    } else {
        $labelc = 'SUBTOTAL';
    }
    ?>

    <div class="xoo-wsc-subtotal xoo-wsc-tool">
        <span class="xoo-wsc-tools-label">
            <?php esc_html_e($labelc, 'woocommerce'); ?>
        </span>
        <span class="xoo-wsc-tools-value">
            <?php echo WC()->cart->get_cart_subtotal(); ?>
        </span>
    </div>


    <?php 

      $has_physical_product = false;

    foreach (WC()->cart->get_cart() as $cart_item) {
        if (!$cart_item['data']->is_virtual()) {
            $has_physical_product = true;
            break;
        }
    }
    ?>


    <?php
    $currencies = array('USD', 'CAD', 'AUD', 'EUR', 'GBP');
    $cur        = '';
    if (!empty($_COOKIE['woocommerce_current_currency'])) {
        $cur = $_COOKIE['woocommerce_current_currency'];
    }
    // Finally, if no currency has been set then just use the default shop currency
    if (!$cur) {
        $cur = get_woocommerce_currency();
    }
    ?>
<?php if ($cur == 'AUD' && $has_physical_product): ?>
        <div class="xoo-wsc-shipping xoo-wsc-tool">
            <span class="xoo-wsc-tools-label">
                <?php _e('STANDARD SHIPPING', 'side-cart-woocommerce'); ?> <img src="<?php echo site_url(); ?>/wp-content/themes/flatsome-child/images/flags/australia.png" style="width: 15px;  margin-top: -4px;">
            </span>
            <?php if (WC()->cart->subtotal > 120) { ?>
                <span class="xoo-wsc-tools-value">FREE!</span>
            <?php } else { ?>
                <span class="xoo-wsc-tools-value">$9.95</span>
            <?php } ?>
            <!--                    <span class="xoo-wsc-tools-value"><?php echo WC()->cart->get_cart_shipping_total(); ?></span>-->
        </div>
    <?php endif; ?>






</div>

<?php /* CUSTOM END */ ?>







<?php xoo_wsc_helper()->get_template('global/footer/extras.php') ?>

<?php if ($has_physical_product) {
    xoo_wsc_helper()->get_template('global/footer/totals.php');
} ?>

<div class="top_checkout"><a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="button checkout wc-forward">CHECKOUT</a></div>


<script>
    
    jQuery(document).ready(function ($) {
        $('#myModal1 .variation-option').on('click', function () {
            var variationId = $(this).data('id');
            $(' .variable_add_to_cart_button').show().data('variation_id', variationId);
            // Update the hidden variation input field if you have one
            //  $('#myModal1 .variation_id').val(variationId);
            $('#myModal1 .variable_add_to_cart_button').attr('data-product_id', variationId);
        });

      
    });


    function show_discounted_items() {

        jQuery('.discounted_items').toggleClass('show_discounted_items');
    }
 
    jQuery(document).ready(function () {
        jQuery("a#addon_modal").fancybox({
            'titleShow': false,
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'easingIn': 'easeOutBack',
            'easingOut': 'easeInBack',
            'width': 420,
            'height': 350
        });

    })

    jQuery(document.body).on('added_to_cart', function () {
        var moveItems = jQuery('.onlytomove').html();
        jQuery('.top_items').html(moveItems);
    });
</script>
              