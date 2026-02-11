<?php
$cart_total      = WC()->cart->subtotal;
$is_in_stock     = false;
$in_cart         = '';
$free_product_id = get_option('free_product_id');
$status          = get_post_status($free_product_id);
if ($free_product_id && $status != false) {
    $product = wc_get_product($free_product_id);
    if ($product->is_in_stock()) {
        $is_in_stock = true;
    }
}

$shipping_offer_ribbon = '';
$highest_min_value     = null;

// Check if the function exists (using new name first, fallback to old for compatibility)
if (function_exists('p2c_get_current_currency')) {
    $cur = p2c_get_current_currency();
} elseif (function_exists('CurrencyConverterCustom')) {
    $cur = CurrencyConverterCustom();
} else {
    $cur = get_woocommerce_currency(); // Fallback to default WooCommerce currency
}

if ($cur == 'USD') {
    $repeater = 'shipping_rules_usa';
} else {
    $repeater = 'shipping_rules_re';
}

//$free_product_name = "Your Free Product Name";


// Retrieve the ACF repeater field
if (have_rows($repeater, 'option')):
    while (have_rows($repeater, 'option')):
        the_row();
        $min_value = get_sub_field('min_value');
        $max_value = get_sub_field('max_value');
        $re_value  = get_sub_field('re_value');

        if (is_numeric($min_value) && is_numeric($max_value)) {
            if ($cart_total >= $min_value && $cart_total < $max_value) {
                if ($min_value > $highest_min_value) {
                    $highest_min_value     = $min_value;
                    $shipping_offer_ribbon = $re_value;
                    $shipping_offer_ribbon = str_replace('{{x}}', number_format((int) $max_value - $cart_total, 2), $shipping_offer_ribbon);
                    //$shipping_offer_ribbon = str_replace('{{p}}', $free_product_name, $shipping_offer_ribbon);
                }
            }
        } elseif (is_numeric($min_value)) {
            if ($cart_total >= $min_value && $min_value > $highest_min_value) {
                $highest_min_value     = $min_value;
                $shipping_offer_ribbon = $re_value;
            }
        } elseif (is_numeric($max_value)) {
            if ($cart_total < $max_value) {
                // Note: This won't overwrite a min_value if already set.
                if ($highest_min_value === null) {
                    $shipping_offer_ribbon = $re_value;
                    $shipping_offer_ribbon = str_replace('{{x}}', number_format((int) $max_value - $cart_total, 2), $shipping_offer_ribbon);
                }
            }
        }
    endwhile;
endif;


function convert_text_emojis($text)
{
    $emoji_map = array(
        ':gift:'       => '🎁',
        ':sparkles:'   => '✨',
        ':sunglasses:' => '😎',
        ':wink:'       => '😉',
		':socks:' => '🧦',
		':white_check_mark:' => '✅'
        // Add more mappings here as needed
    );

    return str_replace(array_keys($emoji_map), array_values($emoji_map), $text);
}

// Usage
$shipping_offer_ribbon = convert_text_emojis($shipping_offer_ribbon);
?>
<div class="shipping-noticed">
    <div class="ship_notice mee">
        <?php echo wp_kses_post($shipping_offer_ribbon); ?>
    </div>
</div>


<?php
global $post, $wpdb, $wp_query;
if (is_product_category()) {
    ?>
    <div class="title-barc">
        <h1>
            <?php
            $cat_obj = $wp_query->get_queried_object();
            if ($cat_obj) {
                if (is_product_category('Tall Hoodies')) {
                    echo esc_html("Australian Tall Hoodies Range");
                } else {
                    echo esc_html($cat_obj->name);
                    $category_desc = $cat_obj->description;
                    $category_ID   = $cat_obj->term_id;
                }
            }
            ?>
        </h1>
    </div>

<?php } else if (is_shop()) { ?>
        <div class="title-barc">
            <h1>Shop</h1>
        </div>
<?php } ?>