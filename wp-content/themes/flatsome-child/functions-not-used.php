
<?php


// ADD THIS WITH ACF wherever being used....
/*
add_action('product_cat_add_form_fields', 'Custom_taxonomy_image_field');

function Custom_taxonomy_image_field($taxonomy)
{
    ?>
    <div class="form-field">
        <label for="rudr_text">Quality Indicator image URL</label>
        <input type="text" name="rudr_text" id="rudr_text" />
        <p>Add Image URL</p>
    </div>
    <?php
}

add_action('product_cat_edit_form_fields', 'custom_edit_image_fields', 10, 2);
function custom_edit_image_fields($term, $taxonomy)
{

    // get meta data value
    $text_field = get_term_meta($term->term_id, 'rudr_text', true);

    ?>
    <tr class="form-field">
        <th><label for="rudr_text">Quality Indicator image URL</label></th>
        <td>
            <input name="rudr_text" id="rudr_text" type="text" value="<?php echo esc_attr($text_field) ?>" />
            <p class="description">Add Image URL</p>
        </td>
    </tr>
    <?php
}

add_action('edited_product_cat', 'custom_save_image_fields');
add_action('create_product_cat', 'custom_save_image_fields');
function custom_save_image_fields($term_id)
{

    update_term_meta(
        $term_id,
        'rudr_text',
        sanitize_text_field($_POST['rudr_text'])
    );
}


add_action('product_cat_add_form_fields', 'Custom_taxonomy_image_link');

function Custom_taxonomy_image_link($taxonomy)
{
    ?>
    <div class="form-field">
        <label for="rudr_link">Quality Indicator image Link</label>
        <input type="text" name="rudr_link" id="rudr_link" />
        <p>Add Image Link</p>
    </div>
    <?php
}

add_action('product_cat_edit_form_fields', 'custom_edit_image_link', 10, 2);
function custom_edit_image_link($term, $taxonomy)
{

    // get meta data value
    $image_link = get_term_meta($term->term_id, 'rudr_link', true);

    ?>
    <tr class="form-field">
        <th><label for="rudr_link">Quality Indicator image Link</label></th>
        <td>
            <input name="rudr_link" id="rudr_link" type="text" value="<?php echo esc_attr($image_link) ?>" />
            <p class="description">Add Image Link</p>
        </td>
    </tr>
    <?php
}

add_action('edited_product_cat', 'custom_save_image_link');
add_action('create_product_cat', 'custom_save_image_link');
function custom_save_image_link($term_id)
{

    update_term_meta(
        $term_id,
        'rudr_link',
        sanitize_text_field($_POST['rudr_link'])
    );
}

*/

// under admin area, check if size is not selected while adding variation.
//add_action( 'admin_head', 'admin_size_field_check_sku' );
function admin_size_field_check_sku()
{
    $screen    = get_current_screen();
    $screen_id = $screen ? $screen->id : '';
    if ($screen_id == 'product') {
        global $product;

        ?>
        <script>
            jQuery(document).ready(function ($) {
                var sizeSkuMapping = {
                    '2XL': ['XXL', '2XL'],
                    '3XL': ['XXXL', '3XL'],
                    '4XL': ['XXXXL', '4XL']
                };

                function validateSkuSizeMatch() {
                    var isValid = true;
                    $("select[name^='attribute_pa_size'] option:selected").each(function () {
                        var sizetext = $(this).text();
                        var sku = $(this).closest('.woocommerce_variation').find("input[id^='variable_sku']").val();
                        if (sizeSkuMapping[sizetext]) {
                            var matches = sizeSkuMapping[sizetext].some(skuPart => sku.indexOf(skuPart) !== -1);
                            if (!matches) {
                                alert("Size " + sizetext + " not found in SKU");
                                isValid = false;
                                return false; // Break the loop
                            }
                        } else if (sku.indexOf(sizetext) === -1) {
                            alert("Size " + sizetext + " not found in SKU");
                            isValid = false;
                            return false; // Break the loop
                        }
                    });

                    return isValid;
                }

                $('#publish, .save-variation-changes').on('click', function (e) {
                    if (!validateSkuSizeMatch()) {
                        $('.save-variation-changes').prop('disabled', true);
                        e.preventDefault();
                    }
                });
            });
        </script>


        <?php


    }
}





/*
add_action('wp_ajax_fetch_product_variations', 'handle_fetch_product_variations');
add_action('wp_ajax_nopriv_fetch_product_variations', 'handle_fetch_product_variations'); // If needed for non-logged users

function handle_fetch_product_variations()
{
    if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
        wp_send_json_error(['error' => 'Invalid product IDs']);
        return;
    }

    $response = [];
    foreach ($_POST['ids'] as $productId) {
        $product = wc_get_product($productId);
        if (!$product)
            continue;

        if ($product->is_type('variable')) {
            $variations = [];
            $available  = false;

            // Only attempt to get variations if it's a variable product
            foreach ($product->get_available_variations() as $variation) {
                if ($variation['is_in_stock']) {
                    $available    = true;
                    $variations[] = $variation['attributes']['attribute_pa_size']; // Adjust attribute name as needed
                }
            }

            $response[$productId] = [
                'available' => $available,
                'sizes'     => $variations
            ];
        } else {
            // For non-variable products, you might want to handle them differently
            $response[$productId] = [
                'available' => $product->is_in_stock(),
                'sizes'     => [] // No sizes for simple products
            ];
        }
    }

    wp_send_json_success(['data' => $response]);
}

*/


add_action('rest_api_init', function () {
    register_rest_route('mytheme/v1', '/product-variations/', [
        'methods'             => 'POST',
        'callback'            => 'fetch_product_variations_api',
        'permission_callback' => '__return_true',  // Ensure to implement proper permission checks as needed
    ]);
});

function fetch_product_variations_api($request)
{
    $product_ids = $request->get_param('ids');
    if (empty($product_ids) || !is_array($product_ids)) {
        return new WP_Error('invalid_request', 'Invalid product IDs', ['status' => 400]);
    }

    $response = [];
    foreach ($product_ids as $productId) {
        $product = wc_get_product($productId);
        if (!$product)
            continue;

        if ($product->is_type('variable')) {
            $variations = [];
            $available  = false;

            foreach ($product->get_available_variations() as $variation) {
                if ($variation['is_in_stock']) {
                    $available    = true;
                    $variations[] = $variation['attributes']['attribute_pa_size'];
                }
            }

            $response[$productId] = [
                'available' => $available,
                'sizes'     => $variations
            ];
        } else {
            $response[$productId] = [
                'available' => $product->is_in_stock(),
                'sizes'     => []
            ];
        }
    }

    return new WP_REST_Response($response, 200);
}


function allow_duplicate_sku_filter($sku_found, $product_id)
{
    // Always return false to allow duplicate SKUs
    return false;
}

// Allow backorders.
function kfg_show_backorders($is_visible, $id)
{
    $product = wc_get_product($id);
    if (!$product->is_in_stock() && !$product->backorders_allowed()) {
        $is_visible = false;
    }
    return $is_visible;
}
add_filter('woocommerce_get_availability', 'kfg_show_backorders', 1, 2);


/*
function dequeue_flatsome_woocommerce_scripts()
{

     /*if (wp_style_is('style-handle', 'enqueued')) {
        wp_dequeue_style('style-handle');
        wp_deregister_style('style-handle');
    }*/

    // Dequeue flatsome woocommerce stylesheet
    wp_dequeue_style('flatsome-theme-woocommerce');
    wp_deregister_style('flatsome-theme-woocommerce');

    // Dequeue flatsome woocommerce javascript
    wp_dequeue_script('flatsome-theme-woocommerce-js');
    wp_deregister_script('flatsome-theme-woocommerce-js');
}
add_action('wp_enqueue_scripts', 'dequeue_flatsome_woocommerce_scripts', 999);
*/

		// Custom Code
	/*	$sql   = "SELECT * from wp_options where option_name = 'category_sales' ";
		$data  = $wpdb->get_results($sql);
		$sales = json_decode($data[0]->option_value, TRUE);
		if ($sales['date'] < date('Y-m-d')) {
			$products_IDs         = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'fields'         => 'ids',
					'posts_per_page' => '100',
				));
			$all_products         = implode(',', array_values($products_IDs->posts));
			$sql                  = "
            SELECT COUNT(*) AS sale_count, posts.ID
            FROM wp_woocommerce_order_items AS order_items
            INNER JOIN wp_woocommerce_order_itemmeta AS order_meta ON order_items.order_item_id = order_meta.order_item_id
            INNER JOIN wp_posts AS posts ON order_meta.meta_value = posts.ID
            WHERE order_items.order_item_type = 'line_item'
            AND order_meta.meta_key = '_product_id'
            AND order_meta.meta_value IN (" . $all_products . ")
            AND order_items.order_id IN (
                SELECT posts.ID AS post_id
                FROM wp_posts AS posts
                WHERE posts.post_type = 'shop_order'
                    AND posts.post_status IN ('wc-completed','wc-processing')
                    AND DATE(posts.post_date) BETWEEN '" . date('Y-m-d', strtotime('-14 days')) . "' AND '" . date('Y-m-d') . "'
            )
            GROUP BY order_meta.meta_value";
			$data                 = $wpdb->get_results($sql);
			$product_sale['date'] = date('Y-m-d');
			foreach ($data as $row) {
				$product_sale[$row->ID] = $row->sale_count;
			}
			$wpdb->get_results("UPDATE wp_options set option_value = '" . json_encode($product_sale) . "' where option_name = 'category_sales' ");
		}*/
		// CUSTOM END
		?>

<?php










<!--
<?php
//if (is_shop() || is_archive()) {      ?>
    <script>
        jQuery(document).on('click', '.r [name=r]', function () {
            jQuery(this).closest('.variations_form').find('.single_add_to_cart_button').click();
        })		
    </script>
<?php
//}
?>
    -->
	    /*
        jQuery(document).ready(function () {
    
            jQuery('.header-cart-link').removeAttr("href");
            jQuery('.header-cart-link').on('click', function () {
                jQuery(".xoo-wsc-basket").click();
            })
            jQuery(".xoo-wsc-close").on('click', function () {
                jQuery(".xoo-wsc-basket").click();
            });
        });
    */


//add_filter( 'woocommerce_package_rates', 'set_free_shipping_by_default', 10, 2 );

function set_free_shipping_by_default($rates, $package)
{
    $cart_total              = WC()->cart->subtotal;
    $free_shipping_threshold = 120;
    $base_currency           = get_woocommerce_currency();
    if ($base_currency == 'USD') {
        $free_shipping_threshold = 150;
    }


    if ($cart_total >= $free_shipping_threshold) {
        foreach ($rates as $rate_id => $rate) {
            if ('free_shipping' !== $rate->method_id) {
                unset($rates[$rate_id]);
            }
        }
    }


    return $rates;
}



//global $product;
//if ( $product->is_type( 'variable' ) && ! is_product_category() && ! is_shop() ) {
//echo '<div id="selectmsg" style="margin-top: 12px;font-weight:600;color:#b20000;font-size:12px;">Please select Size</div>';
//}




// Delete product from cart, this function is being used further in other function add_product_to_cart.
function delete_product_from_cart($product_id) {
    // Ensure WC global is available
    if (!is_object(WC()->cart)) {
        wc_load_cart();
    }

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            WC()->cart->remove_cart_item($cart_item_key);
            // Optionally, break after finding and removing the item to improve efficiency
            break;
        }
    }
}




/* * ******* Remove default UNIQUE SKU condition */
/* add_filter('filter_wc_product_has_unique_sku', 'wc_product_has_unique_sku');

 function filter_wc_product_has_unique_sku($product_id, $sku)
 {
     $data_store = WC_Data_Store::load('product');
     $sku_found = $data_store->is_existing_sku($product_id, $sku);
     return true;
 }
 */



// not sure about its use check again
add_filter('woof_use_chosen', function ($is) {
    if (wp_is_mobile()) {
        return false;
    }
    return $is;
});



/*
add_action('woocommerce_before_calculate_totals', 'add_product_to_cart');

// add free item to cart after certain cart total.
function add_product_to_cart($cart)
{
    if (is_admin() && !defined('DOING_AJAX'))
        return;

    global $woocommerce;

    $optionFreeProductID = get_option('free_product_id');
    if (empty($optionFreeProductID)) {
        $optionFreeProductID = '';
    }

    $free_product_id = $optionFreeProductID;

    $cur = get_woocommerce_currency();
    $min_subtotal = 200;

    if ($cur == 'USD') {
        $min_subtotal = 150;
    }

    $cart_subtotal = 0;
    $has_bundle_with_free_product = false;

    // Check if any bundle product contains the free product
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];

        // Check if the product is a bundle product
        if (class_exists('WC_Product_Bundle') && WC_Product_Bundle::is_bundled_product($product_id)) {
            $bundled_items = WC_PB_Helpers::get_bundled_items($product_id);

            // Check if any bundled item contains the free product
            foreach ($bundled_items as $bundled_item) {
                if ($bundled_item['product_id'] == $free_product_id) {
                    $has_bundle_with_free_product = true;
                    break 2; // Break out of both loops
                }
            }
        }

        // Calculate cart subtotal
        $cart_subtotal += $cart_item['line_total'] + $cart_item['line_tax'];
    }

    // Add Free product if subtotal is above the threshold and there is no bundle with the free product
    if ($cart_subtotal >= $min_subtotal && !$has_bundle_with_free_product && $cur == 'AUD') {
        $cart->add_to_cart($free_product_id, 1);
    }
    // Remove free product if subtotal falls below the threshold or if it's already included in a bundle
    elseif (($cart_subtotal < $min_subtotal || $has_bundle_with_free_product) && $cur == 'AUD') {
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $free_product_id) {
                $cart->remove_cart_item($cart_item_key);
                break; // Stop after removing one instance of the free product
            }
        }
    }
}

*/


/*add_action('woocommerce_before_calculate_totals', 'add_free_product_to_cart2');
add_action( 'woocommerce_before_calculate_totals', 'add_free_product_to_cart2', 10, 1 );

function add_free_product_to_cart2($cart) {
    
    // Get the free product ID.
    $free_product_id = 114909;
    
    // Get the cart subtotal.
    $cart_subtotal = $cart->subtotal;
    
    // Check if the free product is already in the cart.
    $free_product_cart_key = $cart->find_product_in_cart($free_product_id);
    
    // If the cart subtotal is above 90 and the free product is not in the cart, add it.
    if ($cart_subtotal > 90 && !$free_product_cart_key) {
        $cart->add_to_cart($free_product_id, 1);
    }
    // If the cart subtotal is below 90 and the free product is in the cart, remove it.
    elseif ($cart_subtotal < 90 && $free_product_cart_key) {
        $cart->remove_cart_item($free_product_cart_key);
    }
}
*/



// create shipping notice backend area to update shipping notice text and other changes.
add_action('admin_menu', 'custom_page_create');

function custom_page_create()
{
    //create new top-level menu
    add_menu_page('Custom Settings', 'Custom Settings', 'edit_posts', __FILE__, 'custom_settings_page', 'dashicons-welcome-widgets-menus');
    //call register settings function
    add_action('admin_init', 'register_custom_field_settings');
}

function register_custom_field_settings()
{
    //register our settings
    register_setting('custom-field-settings-group', 'tall_tee_size_image');
    register_setting('custom-field-settings-group', 'tall_hoodie_size_image');
    register_setting('custom-field-settings-group', 'button_ups');
    register_setting('custom-field-settings-group', 'trackpants');
    register_setting('custom-field-settings-group', 'crewneck_jumpers');
    register_setting('custom-field-settings-group', 'semi_tall');
    register_setting('custom-field-settings-group', 'work_shirt');
    register_setting('custom-field-settings-group', 'basketball_shorts');
    register_setting('custom-field-settings-group', 'polo_shirt');
    register_setting('custom-field-settings-group', 'jogger_pants');
    register_setting('custom-field-settings-group', 'jacket');
    register_setting('custom-field-settings-group', 'singlet');


    register_setting('custom-field-settings-group', 'if_carttotal_0');
    register_setting('custom-field-settings-group', 'if_carttotal_0_90');
    register_setting('custom-field-settings-group', 'free_product_id');
    register_setting('custom-field-settings-group', 'if_carttotal_90_120');
    register_setting('custom-field-settings-group', 'if_carttotal_120_150');
    register_setting('custom-field-settings-group', 'if_carttotal_90_120_out_stock');
    register_setting('custom-field-settings-group', 'if_carttotal_120_150_out_stock');
    register_setting('custom-field-settings-group', 'if_carttotal_150');
    register_setting('custom-field-settings-group', 'if_carttotal_us_spendover_100');
    register_setting('custom-field-settings-group', 'if_carttotal_us_spendmove');
    register_setting('custom-field-settings-group', 'if_carttotal_us_congrts');
    register_setting('custom-field-settings-group', 'if_carttotal_canada_spendover_100');
    register_setting('custom-field-settings-group', 'if_carttotal_canada_congrts');
    register_setting('custom-field-settings-group', 'if_carttotal_canada_spendmove');
    register_setting('custom-field-settings-group', 'if_carttotal_uk_spendover_100');
    register_setting('custom-field-settings-group', 'if_carttotal_uk_congrts');
    register_setting('custom-field-settings-group', 'if_carttotal_uk_spendmove');
    register_setting('custom-field-settings-group', 'if_carttotal_eu_spendover_100');
    register_setting('custom-field-settings-group', 'if_carttotal_eu_congrts');
    register_setting('custom-field-settings-group', 'if_carttotal_eu_spendmove');
    register_setting('custom-field-settings-group', 'mobile_banner');
}

function custom_settings_page()
{
    ?>
        <div class="wrap">

            <form method="post" action="options.php">
                <?php
                settings_fields('custom-field-settings-group');
                do_settings_sections('custom-field-settings-group');
                $args = array('post_type' => 'product', 'posts_per_page' => -1);
                $loop = new WP_Query($args);
                ?>
                <style>
                    .custom-settings textarea {
                        width: 400px;
                        height: 75px;
                    }

                    .custom-settings select {
                        width: 400px;
                    }
                </style>
                <table class="form-table custom-settings">

                    <tr valign="top">
                        <th scope="row">Tall Tee Size image</th>
                        <td><input name="tall_tee_size_image" value="<?php echo esc_attr(get_option('tall_tee_size_image')); ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Tall Hoodie Size image</th>
                        <td><input name="tall_hoodie_size_image" value="<?php echo esc_attr(get_option('tall_hoodie_size_image')); ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Button ups Size image</th>
                        <td><input name="button_ups" value="<?php echo esc_attr(get_option('button_ups')); ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Trackpants Size image</th>
                        <td><input name="trackpants" value="<?php echo esc_attr(get_option('trackpants')); ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Jumpers Size image</th>
                        <td><input name="crewneck_jumpers" value="<?php echo esc_attr(get_option('crewneck_jumpers')); ?>" /></td>
                    </tr>



                    <tr valign="top">
                        <th scope="row">Semi Tall Size image</th>
                        <td><input name="semi_tall" value="<?php echo esc_attr(get_option('semi_tall')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Work Shirt Size image</th>
                        <td><input name="work_shirt" value="<?php echo esc_attr(get_option('work_shirt')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Basketball Shorts Size image</th>
                        <td><input name="basketball_shorts" value="<?php echo esc_attr(get_option('basketball_shorts')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Polo Shirt Size image</th>
                        <td><input name="polo_shirt" value="<?php echo esc_attr(get_option('polo_shirt')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Jogger Pants Size image</th>
                        <td><input name="jogger_pants" value="<?php echo esc_attr(get_option('jogger_pants')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Jacket Size image</th>
                        <td><input name="jacket" value="<?php echo esc_attr(get_option('jacket')); ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Singlet</th>
                        <td><input name="singlet" value="<?php echo esc_attr(get_option('singlet')); ?>" /></td>
                    </tr>




                    <tr valign="top">
                        <th scope="row">String to append in Customer Name</th>
                        <td><input name="customer_append_string" value="<?php echo esc_attr(get_option('customer_append_string')); ?>" /></td>
                    </tr>



                    <tr valign="top">
                        <th scope="row">IF Cart Total is ZERO</th>
                        <td><textarea name="if_carttotal_0"><?php echo esc_attr(get_option('if_carttotal_0')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">IF Cart Total is above 0 and Less than 90</th>
                        <td><textarea name="if_carttotal_0_90"><?php echo esc_attr(get_option('if_carttotal_0_90')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Free Product</th>
                        <td>
                            <select name="free_product_id">
                                <?php
                                echo get_option('free_product_id');
                                while ($loop->have_posts()):
                                    $loop->the_post();
                                    global $product;
                                    $price = get_post_meta(get_the_ID(), '_price', true);
                                    if ($price == 0 && $price != '') {
                                        ?>
                                                <option <?php echo (get_option('free_product_id') == get_the_ID()) ? 'selected = "selected"' : ''; ?> value="<?php echo get_the_ID(); ?>">
                                                    <?php echo get_the_title(); ?>
                                                </option>
                                                <?php
                                    }
                                endwhile;
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">IF Cart Total is Above & Equal to 90 AND Less than 130</th>
                        <td><textarea name="if_carttotal_90_120"><?php echo esc_attr(get_option('if_carttotal_90_120')); ?></textarea>
                        </td>
                    </tr>


                    <tr valign="top">
                        <th scope="row">IF Cart Total is Above & Equal to 130 AND Less than 150</th>
                        <td><textarea name="if_carttotal_120_150"><?php echo esc_attr(get_option('if_carttotal_120_150')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">IF Cart Total is Above & Equal to 90 AND Less than 130 and Free product is out
                            of
                            Stock
                        </th>
                        <td><textarea name="if_carttotal_90_120_out_stock"><?php echo esc_attr(get_option('if_carttotal_90_120_out_stock')); ?></textarea>
                        </td>
                    </tr>


                    <tr valign="top">
                        <th scope="row">IF Cart Total is Above & Equal to 130 AND Less than 150 and Free product is out
                            of
                            Stock
                        </th>
                        <td><textarea name="if_carttotal_120_150_out_stock"><?php echo esc_attr(get_option('if_carttotal_120_150_out_stock')); ?></textarea>
                        </td>
                    </tr>


                    <tr valign="top">
                        <th scope="row">IF Cart Total is Above & Equal to 150</th>
                        <td><textarea name="if_carttotal_150"><?php echo esc_attr(get_option('if_carttotal_150')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">USA : IF Cart Total is Equal to ZERO</th>
                        <td><textarea name="if_carttotal_us_spendover_100"><?php echo esc_attr(get_option('if_carttotal_us_spendover_100')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">USA : IF Cart Total is less than 104</th>
                        <td><textarea name="if_carttotal_us_spendmove"><?php echo esc_attr(get_option('if_carttotal_us_spendmove')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">USA : IF Cart Total is Above & Equal to 104</th>
                        <td><textarea name="if_carttotal_us_congrts"><?php echo esc_attr(get_option('if_carttotal_us_congrts')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">CANADA : IF Cart Total is Equal to ZERO</th>
                        <td><textarea name="if_carttotal_canada_spendover_100"><?php echo esc_attr(get_option('if_carttotal_canada_spendover_100')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">CANADA : IF Cart Total is less than 140</th>
                        <td><textarea name="if_carttotal_canada_spendmove"><?php echo esc_attr(get_option('if_carttotal_canada_spendmove')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">CANADA : IF Cart Total is Above & Equal to 140</th>
                        <td><textarea name="if_carttotal_canada_congrts"><?php echo esc_attr(get_option('if_carttotal_canada_congrts')); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">UK : IF Cart Total is Equal to ZERO</th>
                        <td><textarea name="if_carttotal_uk_spendover_100"><?php echo esc_attr(get_option('if_carttotal_uk_spendover_100')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">UK : IF Cart Total is less than 140</th>
                        <td><textarea name="if_carttotal_uk_spendmove"><?php echo esc_attr(get_option('if_carttotal_uk_spendmove')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">UK : IF Cart Total is Above & Equal to 140</th>
                        <td><textarea name="if_carttotal_uk_congrts"><?php echo esc_attr(get_option('if_carttotal_uk_congrts')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">European Union : IF Cart Total is Equal to ZERO</th>
                        <td><textarea name="if_carttotal_eu_spendover_100"><?php echo esc_attr(get_option('if_carttotal_eu_spendover_100')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">European Union : IF Cart Total is less than 140</th>
                        <td><textarea name="if_carttotal_eu_spendmove"><?php echo esc_attr(get_option('if_carttotal_eu_spendmove')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">European Union : IF Cart Total is Above & Equal to 140</th>
                        <td><textarea name="if_carttotal_eu_congrts"><?php echo esc_attr(get_option('if_carttotal_eu_congrts')); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Mobile Banner Image</th>
                        <td><input type="text" name="mobile_banner" value="<?php echo esc_attr(get_option('mobile_banner')); ?>" />
                        </td>
                    </tr>


                </table>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php
}



/****************cart addon to remove if already added in cart ************/
// wp-admin/admin.php?page=sfn-cart-addons but not using this plugin as cart sidebar is enough for cart addons..
add_action('woocommerce_before_calculate_totals', 'remove_addon', 50, 1);
function remove_addon($cart)
{
    $settings     = get_option('sfn_cart_addons');
    $multiple_ids = $settings['default_addons'];
    foreach ($multiple_ids as $addon_id) {
        $newpro = wc_get_product($addon_id);
        if ($newpro->is_type('variable')) {
            foreach ($newpro->get_available_variations() as $variation) {
                $variations[] = $variation['variation_id'];
            }
        }
        $has_others = false;
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = version_compare(WC_VERSION, '3.0', '<') ? $cart_item['data']->id : $cart_item['data']->get_id();
            if ($product_id == $addon_id || in_array($cart_item['variation_id'], $variations)) {
                $addon_key = $cart_item_key;
            } else {
                $has_others = true;
            }

        }
        if (!$has_others && isset ($addon_key)) {
            $cart->remove_cart_item($addon_key);
        }
    }
}





/**********************Size Chart Individual Product************/

add_action('woocommerce_product_options_general_product_data', 'product_checkbox_fields_sizechart');
function product_checkbox_fields_sizechart()
{
    global $post;

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'tall_tees',
            'desc'     => __('Tall Tees size cart button', 'woocommerce'),
            'label'    => __('Tall Tees', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';
    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'tall_hoodies',
            'desc'     => __('Tall Hoodies size cart button', 'woocommerce'),
            'label'    => __('Tall Hoodies', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';
    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'button_ups',
            'desc'     => __('Button UPS size cart button', 'woocommerce'),
            'label'    => __('Button UPS', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'tackpants',
            'desc'     => __('Trackpants size cart button', 'woocommerce'),
            'label'    => __('Trackpants', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'crewneck_jumpers',
            'desc'     => __('Crewneck Jumpers size cart button', 'woocommerce'),
            'label'    => __('Jumpers', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'semi_tall',
            'desc'     => __('Semi Tall size cart button', 'woocommerce'),
            'label'    => __('Semi Tall', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'work_shirt',
            'desc'     => __('Work Shirt size cart button', 'woocommerce'),
            'label'    => __('Work Shirt', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'basketball_shorts',
            'desc'     => __('Basketball Shorts size cart button', 'woocommerce'),
            'label'    => __('Basketball Shorts', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'polo_shirt',
            'desc'     => __('Polo Shirt size cart button', 'woocommerce'),
            'label'    => __('Polo Shirt', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'jogger_pants',
            'desc'     => __('Jogger Pants size cart button', 'woocommerce'),
            'label'    => __('Jogger Pants', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'jacket',
            'desc'     => __('Jacket size cart button', 'woocommerce'),
            'label'    => __('Jacket', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';


    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox(
        array(
            'id'       => 'singlet',
            'desc'     => __('Singlet size cart button', 'woocommerce'),
            'label'    => __('Singlet', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';


}

// Save Fields
add_action('woocommerce_process_product_meta', 'product_checkbox_fields_sizechart_save');
function product_checkbox_fields_sizechart_save($post_id)
{
    $tall_tees_product = isset ($_POST['tall_tees']) ? 'yes' : 'no';
    update_post_meta($post_id, 'tall_tees', esc_attr($tall_tees_product));

    $tall_hoodies_product = isset ($_POST['tall_hoodies']) ? 'yes' : 'no';
    update_post_meta($post_id, 'tall_hoodies', esc_attr($tall_hoodies_product));

    $button_ups_product = isset ($_POST['button_ups']) ? 'yes' : 'no';
    update_post_meta($post_id, 'button_ups', esc_attr($button_ups_product));

    $trackpants_product = isset ($_POST['tackpants']) ? 'yes' : 'no';
    update_post_meta($post_id, 'tackpants', esc_attr($trackpants_product));

    $crewneck_jumpers = isset ($_POST['crewneck_jumpers']) ? 'yes' : 'no';
    update_post_meta($post_id, 'crewneck_jumpers', esc_attr($crewneck_jumpers));

    $semi_tall = isset ($_POST['semi_tall']) ? 'yes' : 'no';
    update_post_meta($post_id, 'semi_tall', esc_attr($semi_tall));

    $work_shirt = isset ($_POST['work_shirt']) ? 'yes' : 'no';
    update_post_meta($post_id, 'work_shirt', esc_attr($work_shirt));

    $basketball_shorts = isset ($_POST['basketball_shorts']) ? 'yes' : 'no';
    update_post_meta($post_id, 'basketball_shorts', esc_attr($basketball_shorts));

    $polo_shirt = isset ($_POST['polo_shirt']) ? 'yes' : 'no';
    update_post_meta($post_id, 'polo_shirt', esc_attr($polo_shirt));

    $jogger_pants = isset ($_POST['jogger_pants']) ? 'yes' : 'no';
    update_post_meta($post_id, 'jogger_pants', esc_attr($jogger_pants));

    $jacket = isset ($_POST['jacket']) ? 'yes' : 'no';
    update_post_meta($post_id, 'jacket', esc_attr($jacket));

    $singlet = isset ($_POST['singlet']) ? 'yes' : 'no';
    update_post_meta($post_id, 'singlet', esc_attr($singlet));

}
/**********************Size Chart Individual Product************/


/**********************Size Chart Category************/
add_action('product_cat_add_form_fields', 'wh_taxonomy_add_new_meta_field', 10, 1);
add_action('product_cat_edit_form_fields', 'wh_taxonomy_edit_meta_field', 10, 1);
//Product Cat Create page
function wh_taxonomy_add_new_meta_field()
{
    ?>
        <div class="form-field">
            <label for="wh_meta_tees">
                <?php _e('Tall Tees', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_tees" id="wh_meta_tees" />
        </div>
        <div class="form-field">
            <label for="wh_meta_hoodies">
                <?php _e('Tall Hoodies', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_hoodies" id="wh_meta_hoodies" />
        </div>
        <div class="form-field">
            <label for="wh_meta_buttons">
                <?php _e('Buttons UPS', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_buttons" id="wh_meta_buttons" />
        </div>
        <div class="form-field">
            <label for="wh_meta_trackpants">
                <?php _e('Track Pants', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_trackpants" id="wh_meta_trackpants" />
        </div>
        <div class="form-field">
            <label for="wh_meta_crewneck_jumpers">
                <?php _e('Jumpers', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_crewneck_jumpers" id="wh_meta_crewneck_jumpers" />
        </div>

        <div class="form-field">
            <label for="wh_meta_semi_tall">
                <?php _e('Semi Tall', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_semi_tall" id="wh_meta_semi_tall" />
        </div>

        <div class="form-field">
            <label for="wh_meta_work_shirt">
                <?php _e('Work Shirt', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_work_shirt" id="wh_meta_work_shirt" />
        </div>

        <div class="form-field">
            <label for="wh_meta_basketball_shorts">
                <?php _e('Basketball Shorts', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_basketball_shorts" id="wh_meta_basketball_shorts" />
        </div>

        <div class="form-field">
            <label for="wh_meta_polo_shirt">
                <?php _e('Polo Shirt', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_polo_shirt" id="wh_meta_polo_shirt" />
        </div>
        <div class="form-field">
            <label for="wh_meta_jogger_pants">
                <?php _e('Jogger Pants', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_jogger_pants" id="wh_meta_jogger_pants" />
        </div>
        <div class="form-field">
            <label for="wh_meta_jacket">
                <?php _e('Jacket', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_jacket" id="wh_meta_jacket" />
        </div>

        <div class="form-field">
            <label for="wh_meta_singlet">
                <?php _e('Singlet', 'wh'); ?>
            </label>
            <input type="checkbox" name="wh_meta_singlet" id="wh_meta_singlet" />
        </div>

        <?php
}
//Product Cat Edit page
function wh_taxonomy_edit_meta_field($term)
{
    //getting term ID
    $term_id = $term->term_id;
    // retrieve the existing value(s) for this meta field.
    $wh_meta_tees             = get_term_meta($term_id, 'wh_meta_tees', true);
    $wh_meta_hoodies          = get_term_meta($term_id, 'wh_meta_hoodies', true);
    $wh_meta_buttons          = get_term_meta($term_id, 'wh_meta_buttons', true);
    $wh_meta_trackpants       = get_term_meta($term_id, 'wh_meta_trackpants', true);
    $wh_meta_crewneck_jumpers = get_term_meta($term_id, 'wh_meta_crewneck_jumpers', true);

    $wh_meta_semi_tall         = get_term_meta($term_id, 'wh_meta_semi_tall', true);
    $wh_meta_work_shirt        = get_term_meta($term_id, 'wh_meta_work_shirt', true);
    $wh_meta_basketball_shorts = get_term_meta($term_id, 'wh_meta_basketball_shorts', true);
    $wh_meta_polo_shirt        = get_term_meta($term_id, 'wh_meta_polo_shirt', true);
    $wh_meta_jogger_pants      = get_term_meta($term_id, 'wh_meta_jogger_pants', true);
    $wh_meta_jacket            = get_term_meta($term_id, 'wh_meta_jacket', true);
    $wh_meta_singlet           = get_term_meta($term_id, 'wh_meta_singlet', true);
    ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_tees">
                    <?php _e('Tall Tees', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_tees" id="wh_meta_tees" value="yes" <?php echo ($wh_meta_tees) ? checked($wh_meta_tees, 'yes') : ''; ?> />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_hoodies">
                    <?php _e('Tall Hoodies', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_hoodies" id="wh_meta_hoodies" value="yes" <?php echo ($wh_meta_hoodies) ? checked($wh_meta_hoodies, 'yes') : ''; ?> />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_buttons">
                    <?php _e('Button UPS', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_buttons" id="wh_meta_buttons" value="yes" <?php echo ($wh_meta_buttons) ? checked($wh_meta_buttons, 'yes') : ''; ?> />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_trackpants">
                    <?php _e('Trackpants', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_trackpants" id="wh_meta_trackpants" value="yes" <?php echo ($wh_meta_trackpants) ? checked($wh_meta_trackpants, 'yes') : ''; ?> />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_crewneck_jumpers">
                    <?php _e('Jumpers', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_crewneck_jumpers" id="wh_meta_crewneck_jumpers" value="yes" <?php echo ($wh_meta_crewneck_jumpers) ? checked($wh_meta_crewneck_jumpers, 'yes') : ''; ?> />
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_semi_tall">
                    <?php _e('Semi Tall', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_semi_tall" id="wh_meta_semi_tall" value="yes" <?php echo ($wh_meta_semi_tall) ? checked($wh_meta_semi_tall, 'yes') : ''; ?> />
            </td>
        </tr>


        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_work_shirt">
                    <?php _e('Work Shirt', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_work_shirt" id="wh_meta_work_shirt" value="yes" <?php echo ($wh_meta_work_shirt) ? checked($wh_meta_work_shirt, 'yes') : ''; ?> />
            </td>
        </tr>


        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_basketball_shorts">
                    <?php _e('Basketball Shorts', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_basketball_shorts" id="wh_meta_basketball_shorts" value="yes" <?php echo ($wh_meta_basketball_shorts) ? checked($wh_meta_basketball_shorts, 'yes') : ''; ?> />
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_polo_shirt">
                    <?php _e('Polo Shirt', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_polo_shirt" id="wh_meta_polo_shirt" value="yes" <?php echo ($wh_meta_polo_shirt) ? checked($wh_meta_polo_shirt, 'yes') : ''; ?> />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_jogger_pants">
                    <?php _e('Jogger Pants', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_jogger_pants" id="wh_meta_jogger_pants" value="yes" <?php echo ($wh_meta_jogger_pants) ? checked($wh_meta_jogger_pants, 'yes') : ''; ?> />
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_jacket">
                    <?php _e('Jacket', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_jacket" id="wh_meta_jacket" value="yes" <?php echo ($wh_meta_jacket) ? checked($wh_meta_jacket, 'yes') : ''; ?> />
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_singlet">
                    <?php _e('Singlet', 'wh'); ?>
                </label></th>
            <td>
                <input type="checkbox" name="wh_meta_singlet" id="wh_meta_singlet" value="yes" <?php echo ($wh_meta_singlet) ? checked($wh_meta_singlet, 'yes') : ''; ?> />
            </td>
        </tr>

        <?php
}


add_action('edited_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
add_action('create_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
// Save extra taxonomy fields callback function.
function wh_save_taxonomy_custom_meta($term_id)
{
    if (isset ($_POST['wh_meta_hoodies'])) {
        update_term_meta($term_id, 'wh_meta_hoodies', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_hoodies', '');
    }

    if (isset ($_POST['wh_meta_buttons'])) {
        update_term_meta($term_id, 'wh_meta_buttons', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_buttons', '');
    }

    if (isset ($_POST['wh_meta_tees'])) {
        update_term_meta($term_id, 'wh_meta_tees', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_tees', '');
    }

    if (isset ($_POST['wh_meta_trackpants'])) {
        update_term_meta($term_id, 'wh_meta_trackpants', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_trackpants', '');
    }

    if (isset ($_POST['wh_meta_crewneck_jumpers'])) {
        update_term_meta($term_id, 'wh_meta_crewneck_jumpers', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_crewneck_jumpers', '');
    }



    if (isset ($_POST['wh_meta_semi_tall'])) {
        update_term_meta($term_id, 'wh_meta_semi_tall', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_semi_tall', '');
    }

    if (isset ($_POST['wh_meta_work_shirt'])) {
        update_term_meta($term_id, 'wh_meta_work_shirt', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_work_shirt', '');
    }


    if (isset ($_POST['wh_meta_basketball_shorts'])) {
        update_term_meta($term_id, 'wh_meta_basketball_shorts', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_basketball_shorts', '');
    }


    if (isset ($_POST['wh_meta_polo_shirt'])) {
        update_term_meta($term_id, 'wh_meta_polo_shirt', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_polo_shirt', '');
    }

    if (isset ($_POST['wh_meta_jogger_pants'])) {
        update_term_meta($term_id, 'wh_meta_jogger_pants', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_jogger_pants', '');
    }

    if (isset ($_POST['wh_meta_jacket'])) {
        update_term_meta($term_id, 'wh_meta_jacket', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_jacket', '');
    }

    if (isset ($_POST['wh_meta_singlet'])) {
        update_term_meta($term_id, 'wh_meta_singlet', 'yes');
    } else {
        update_term_meta($term_id, 'wh_meta_singlet', '');
    }








}




/*add_filter( 'woocommerce_single_product_image_thumbnail_html', 'my_custom_thumbnail_size', 10, 2 );
function my_custom_thumbnail_size( $html, $attachment_id ) {
    global $product;
    // Check if the product is a bundle
    if ( $product && has_term( 'bundle', 'product_type', $product->get_id() ) ) {
        $image_size = 'full'; // set the desired image size here
        $image_src = wp_get_attachment_image_src( $attachment_id, $image_size );
        $html = '<img src="' . $image_src[0] . '" alt="" />';
    }
    return $html;
}
*/

function change_bundled_item_thumbnail_size($size, $bundle)
{
    // Modify the size to 'full' or any other size you desire
    return 'full';
}
//add_filter('bundled_product_large_thumbnail_size', 'change_bundled_item_thumbnail_size', 10, 2);




/**********************Size Chart Category************/
// ASK CLIENT SAVE FIRST IN ACF then DELETE THIS 

add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_video');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_video_save');
function woocommerce_product_custom_video()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id'          => '_custom_product_video',
            'placeholder' => 'Video ID here...',
            'label'       => __('Youtube Video ID', 'woocommerce'),
            'desc_tip'    => 'true'
        )
    );

    echo '</div>';
}

function woocommerce_product_custom_video_save($post_id)
{
    // Custom Product Text Field
    $woocommerce_custom_product_text_field = $_POST['_custom_product_video'];
    //if (!empty($woocommerce_custom_product_text_field))
    update_post_meta($post_id, '_custom_product_video', esc_attr($woocommerce_custom_product_text_field));

}

// it was in header file before.
   $wholesaleHidePriceClass = '';
    if (get_post_meta(get_the_ID(), 'wholesale_hide_price', true) == 'Yes') {
        $wholesaleHidePriceClass = 'wholesalehideyes';
    }