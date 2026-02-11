<?php
/**
 * Addon Modal Template
 * Extracted from xoo-wsc-footer.php
 */

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

    <div class="ins-addon-modal-wrapper" style="margin-bottom: 15px; text-align: center;">
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
    </div>

<?php } ?>
