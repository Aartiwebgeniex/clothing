<?php
/**
 * Product loop sale flash
 *
 * @author           WooThemes
 * @package          WooCommerce/Templates
 * @version          1.6.4
 * @flatsome-version 3.16.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post, $product, $wc_cpdf;

$rimg     = '';
$fontSize = '10';
$product_id = $product->get_id();

// Fetch ACF fields
$product_text     = get_field('product_text', $product_id); 
$badge            = get_field('product_badge', $product_id);
$font_size_in_px  = get_field('font_size_in_px', $product_id);

$newness_days = 30;
$created      = strtotime($product->get_date_created());
$is_new       = (time() - (60 * 60 * 24 * $newness_days)) < $created;

$product_categories  = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
$desired_category_id = 200;
$anyfive             = in_array($desired_category_id, $product_categories);
$is_any5_page        = is_product_category('5-for-99');
$is_sale             = $product->is_on_sale();
$product_type        = $product->get_type();

/**
 * ✅ Priority logic
 */

// 1. Custom product text (from ACF)
if (!empty($product_text)) {
    $rimg = esc_html($product_text);
    $fontSize = $font_size_in_px ?: '10';

// 2. On Sale - show discount %
} elseif ($is_sale) {

    if ($product_type === 'simple') {
        $regular_price = floatval($product->get_regular_price());
        $sale_price    = floatval($product->get_price());

    } elseif ($product_type === 'variable') {
        // ✅ Variable product percentage (based on parent min/max price)
        $regular_price = floatval($product->get_variation_regular_price('min')); 
        $sale_price    = floatval($product->get_variation_sale_price('min'));

        // fallback if empty
        if (empty($regular_price)) {
            $regular_price = floatval($product->get_regular_price());
        }
        if (empty($sale_price)) {
            $sale_price = floatval($product->get_price());
        }
    } else {
        $regular_price = 0;
        $sale_price    = 0;
    }

    if ($regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
        $percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
        $rimg = $percentage . '% OFF';
        $rimg = 'SALE';
    } else {
        $rimg = 'SALE';
    }

    $fontSize = '10';

// 3. New Product
} elseif ($is_new) {
    $rimg = 'NEW';

// 4. Any 5 for 99 Category
} elseif ($anyfive && !$is_any5_page) {
    $rimg     = 'ANY 5 FOR 99';
    $fontSize = '10';

// 5. Custom Badge (from ACF)
} elseif (!empty($badge)) {
    $rimg = esc_html($badge);
    $fontSize = $font_size_in_px ?: '10';
}

// ✅ Output badge
if ($rimg) {
    $important = is_product() ? '' : '';
    ?>
    <div class="ribbon ribbon-hot">
        <span style="font-size:<?php echo esc_attr($fontSize); ?>px<?php echo $important; ?>">
            <?php echo esc_html($rimg); ?>
        </span>
    </div>
    <?php
}
?>
