<?php
/**
 * Flatsome functions and definitions
 *
 * @package flatsome
 */

// ============================================================================
// SECTION: Scripts & Styles Enqueuing
// ============================================================================

/**
 * Enqueue scripts and styles for child theme
 */
function p2c_enqueue_scripts_styles() {
    // Cache theme directory paths
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    // Common styles
    wp_enqueue_style('p2c-custom-css', $theme_uri . '/css/custom.css', array(), filemtime($theme_dir . '/css/custom.css'));
    wp_enqueue_style('p2c-cart-bar-css', $theme_uri . '/css/sidebar-cart.css', array(), filemtime($theme_dir . '/css/sidebar-cart.css'));
    wp_enqueue_style('p2c-fancy-css', $theme_uri . '/css/fancybox/jquery.fancybox.min.css', array(), filemtime($theme_dir . '/css/fancybox/jquery.fancybox.min.css'), 'all');

    // Common scripts
    wp_enqueue_script('p2c-fancy-js', $theme_uri . '/js/fancybox/jquery.fancybox.min.js', array('jquery'), filemtime($theme_dir . '/js/fancybox/jquery.fancybox.min.js'), true);
    wp_enqueue_script('p2c-custom-js', $theme_uri . '/js/custom.js', array('jquery'), filemtime($theme_dir . '/js/custom.js'), true);

    // Single product page
    if (is_product()) {
        wp_enqueue_style('p2c-single-css', $theme_uri . '/css/custom-single.css', array(), filemtime($theme_dir . '/css/custom-single.css'));
        wp_enqueue_script('p2c-single-product-js', $theme_uri . '/js/single-product.js', array('jquery'), filemtime($theme_dir . '/js/single-product.js'), true);
    }

    // Archive pages (category, shop, front page)
    if (is_product_category() || is_shop() || is_front_page()) {
        wp_enqueue_style('p2c-archive-css', $theme_uri . '/css/custom-archive.css', array(), filemtime($theme_dir . '/css/custom-archive.css'));
    }

    // Shop and category pages
    if (is_shop() || is_product_category()) {
        wp_enqueue_script('p2c-archive-js', $theme_uri . '/js/archive.js?v=1.0.12', array('jquery'), '1.0.12', true);
    }

    // Cart and checkout pages
    if (is_cart() || is_checkout()) {
        wp_enqueue_style('p2c-cart-checkout-css', $theme_uri . '/css/cart-checkout.css', array(), filemtime($theme_dir . '/css/cart-checkout.css'));
        wp_enqueue_script('p2c-cart-checkout-js', $theme_uri . '/js/cart-checkout.js', array('jquery'), filemtime($theme_dir . '/js/cart-checkout.js'), true);
    }
}

if (!is_admin()) {
    add_action('wp_enqueue_scripts', 'p2c_enqueue_scripts_styles', 11);
}

// ============================================================================
// SECTION: Instantio Plugin Customizations (Safe from plugin updates)
// ============================================================================

/**
 * Enqueue Instantio custom CSS and JS
 * These files override the plugin's suggested products functionality
 */
function p2c_enqueue_instantio_custom() {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();
    
    // Enqueue custom CSS for suggested products
    wp_enqueue_style(
        'p2c-instantio-custom-css',
        $theme_uri . '/instantio-custom.css',
        array(),
        filemtime($theme_dir . '/instantio-custom.css')
    );
    
    // Enqueue custom JS for suggested products
    wp_enqueue_script(
        'p2c-instantio-custom-js',
        $theme_uri . '/instantio-custom.js',
        array('jquery'),
        filemtime($theme_dir . '/instantio-custom.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'p2c_enqueue_instantio_custom', 999);

/**
 * Override Instantio template with theme version
 * This allows customization of suggested-products.php without modifying plugin files
 */
function p2c_instantio_template_override($template_path) {
    $theme_template = get_stylesheet_directory() . '/instantio/templates/suggested-products.php';
    
    // Check if we're loading the suggested products template
    if (strpos($template_path, 'suggested-products.php') !== false) {
        // If theme override exists, use it
        if (file_exists($theme_template)) {
            return $theme_template;
        }
    }
    
    return $template_path;
}
add_filter('ins_suggested_products_template', 'p2c_instantio_template_override', 10, 1);

// Also try the general template filter if the plugin uses it
add_filter('ins_template_path', 'p2c_instantio_template_override', 10, 1);

/**
 * Get suggested products based on cart items size or random
 * 
 * @return WP_Query|false
 */
function p2c_get_suggested_products() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return false;
    }

    $count = 4; // Number of products to show
    $exclude_ids = array();
    $target_attributes = array();

    // Get products in cart and their sizes
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $product = $cart_item['data'];
        $exclude_ids[] = $product->get_id();
        
        if ( $product->is_type( 'variation' ) ) {
            $exclude_ids[] = $product->get_parent_id();
            
            // Try to get size attribute
            $attributes = $product->get_attributes();
            
            // Check for pa_size or any attribute containing 'size'
            foreach ( $attributes as $attribute_name => $attribute_value ) {
                if ( strpos( $attribute_name, 'size' ) !== false ) {
                    if ( ! empty( $attribute_value ) ) {
                        if ( ! isset( $target_attributes[$attribute_name] ) ) {
                            $target_attributes[$attribute_name] = array();
                        }
                        $target_attributes[$attribute_name][] = $attribute_value;
                    }
                }
            }
        }
    }
    
    // Base arguments
    $args = array(
        'post_type' => array( 'product', 'product_variation' ),
        'post_status' => 'publish',
        'posts_per_page' => $count,
        'orderby' => 'rand',
        'post__not_in' => $exclude_ids,
        'meta_query' => array(
             array(
                'key' => '_stock_status',
                'value' => 'instock',
             )
        )
    );

    // If we have target sizes, try to find products with those sizes
    $products = false;
    
    if ( ! empty( $target_attributes ) ) {
        $size_args = $args;
        $tax_query = array('relation' => 'OR');
        
        foreach ( $target_attributes as $taxonomy => $terms ) {
             $tax_query[] = array(
                 'taxonomy' => $taxonomy,
                 'field' => 'slug',
                 'terms' => array_unique( $terms )
             );
        }
        
        if ( count($tax_query) > 1 ) {
            $size_args['tax_query'] = $tax_query;
            $products = new WP_Query( $size_args );
        }
    }

    // Fallback to random if no size-matched products found or no sizes in cart
    if ( ! $products || ! $products->have_posts() ) {
        $products = new WP_Query( $args );
    }

    return $products;
}


/**
 * Dequeue parent theme scripts and enqueue custom replacements
 */
function p2c_dequeue_parent_scripts() {
    if (wp_script_is('flatsome-theme-woocommerce-js', 'enqueued')) {
        wp_dequeue_script('flatsome-theme-woocommerce-js');
        wp_deregister_script('flatsome-theme-woocommerce-js');
    }

    if (wp_script_is('flatsome-infinite-scroll', 'enqueued')) {
        wp_dequeue_script('flatsome-infinite-scroll');
        wp_deregister_script('flatsome-infinite-scroll');
    }

    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    wp_enqueue_script('p2c-woocommerce-custom', $theme_uri . '/js/woocommerce.js', array('jquery'), filemtime($theme_dir . '/js/woocommerce.js'), true);
    wp_enqueue_script('p2c-woocommerce-infinite-scroll', $theme_uri . '/js/flatsome-infinite-scroll.js', array('jquery'), filemtime($theme_dir . '/js/flatsome-infinite-scroll.js'), true);

    add_action('wp_footer', 'p2c_add_infinite_scroll_params', 20);
}
add_action('wp_enqueue_scripts', 'p2c_dequeue_parent_scripts', 999);

/**
 * Add inline script for infinite scroll parameters
 */
function p2c_add_infinite_scroll_params() {
    ?>
    <script type="text/javascript" id="flatsome-infinite-scroll-js-extra">
        /* <![CDATA[ */
        var flatsome_infinite_scroll = {
            "scroll_threshold": "400",
            "fade_in_duration": "300",
            "type": "spinner",
            "list_style": "grid",
            "history": "push"
        };
        /* ]]> */
    </script>
    <?php
}

// ============================================================================
// SECTION: Template & Header Functions
// ============================================================================

/**
 * Get domain name with caching
 * Used in header template for conditional template loading
 *
 * @return string Domain name
 */
function p2c_get_domain_name() {
    static $domain = null;
    if (null === $domain) {
        $host = wp_parse_url(home_url(), PHP_URL_HOST);
        if ($host) {
            $parts = explode('.', $host);
            $domain = $parts[count($parts) - 2] ?? '';
        } else {
            $domain = '';
        }
    }
    return $domain;
}

/**
 * Add footer inline styles
 */
function p2c_add_footer_styles() {
    // Mobile banner style (dynamic)
    $mobile_banner = get_field('mobile_banner', 'option');
    if ($mobile_banner) {
        $mobile_banner_css = '
            @media (max-width: 767px) {
                body.home #content p a img.aligncenter {
                    content: url(' . esc_url($mobile_banner) . ') !important;
                }
            }
        ';
        wp_add_inline_style('p2c-custom-css', $mobile_banner_css);
    }

    // Static footer styles
    $footer_css = '
        .shop_table tr.order-total~tr {
            text-align: center !important;
        }
        square-placement::part(afterpay-paragraph) {
            text-align: center;
        }
    ';
    wp_add_inline_style('p2c-custom-css', $footer_css);

    // Product page specific styles
    if (is_product()) {
        $product_footer_css = '
            .yith-ywar-product-rating {
                display: none;
            }
        ';
        wp_add_inline_style('p2c-single-css', $product_footer_css);
    }
}
add_action('wp_enqueue_scripts', 'p2c_add_footer_styles', 20);

/**
 * Add footer inline scripts
 */
function p2c_add_footer_scripts() {
    if (!is_product()) {
        return;
    }

    $footer_script = '(function($) {
        var checkCountInterval = setInterval(function() {
            var $totalCountElement = $(".ctotal-yithr-count");
            
            if ($totalCountElement.length && $totalCountElement.text()) {
                var reviewCount = $totalCountElement.text();
                
                $(".yith-total-reviews").text(reviewCount + " reviews");
                $(".yith-ywar-product-rating").css("display", "block");
                
                clearInterval(checkCountInterval);
            }
        }, 2000);
        
        // Cleanup on page unload
        $(window).on("beforeunload", function() {
            if (checkCountInterval) {
                clearInterval(checkCountInterval);
            }
        });
    })(jQuery);';

    wp_add_inline_script('p2c-single-product-js', $footer_script);
}
add_action('wp_enqueue_scripts', 'p2c_add_footer_scripts', 21);

// ============================================================================
// SECTION: WordPress Core Functions
// ============================================================================

/**
 * Replace "Protected: " prefix with "VIP: " in post titles
 */
function p2c_replace_protected_title($title) {
    if (strpos($title, 'Protected: ') === 0) {
        $title = str_replace('Protected: ', 'VIP: ', $title);
    }
    return $title;
}
add_filter('protected_title_format', 'p2c_replace_protected_title');

// ============================================================================
// SECTION: WooCommerce Product Functions
// ============================================================================

/**
 * Allow backorders.
 */
function p2c_show_backorders($availability, $product)
{
    if (!$product->is_in_stock() && !$product->backorders_allowed()) {
        $availability['availability'] = ''; // Clear availability text
        $availability['class']        = 'out-of-stock'; // Optional: add a class for custom styling
    }
    return $availability;
}
add_filter('woocommerce_get_availability', 'p2c_show_backorders', 10, 2);



/**
 * Display size chart link on single product pages
 */
function p2c_add_size_guide_after_add_to_cart() {
    global $product;
    if ($product && $product->is_type('variable') && is_singular('product')) {
        $chart = get_field('product_chart', get_the_ID());
        if ($chart) {
            echo '<div class="size_guide" style="margin-top: 15px;">First Time? See our <a href="#inline2" class="various2" style="text-decoration: underline;">SIZE GUIDE</a></div>';
        }
    }
}



add_action('woocommerce_before_calculate_totals', 'p2c_add_free_shipping_products');

// ============================================================================
// SECTION: WooCommerce Cart Functions
// ============================================================================

/**
 * Add free shipping products to cart based on rules
 * Only works for AUD currency
 */
function p2c_add_free_shipping_products($cart_object) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    if (!function_exists('get_field') || get_woocommerce_currency() !== 'AUD') {
        return;
    }

    $shipping_rules = get_field('shipping_rules_re', 'option');
    if (empty($shipping_rules)) {
        return;
    }

    // Get free product IDs from rules
    $free_product_ids = array_column($shipping_rules, 'op_free_product');

    // Get bundled product IDs
    $bundled_product_ids = p2c_get_bundled_product_ids($cart_object);

    // Calculate cart subtotal excluding free products
    $cart_subtotal = p2c_calculate_cart_subtotal($cart_object, $free_product_ids);

    // Process each shipping rule
    foreach ($shipping_rules as $rule) {
        $min_value       = $rule['min_value'];
        $free_product_id = $rule['op_free_product'];

        // Skip if product is bundled or threshold not met
        if (in_array($free_product_id, $bundled_product_ids, true) || $cart_subtotal < $min_value) {
            continue;
        }

        // Add free product if not in cart
        if (!p2c_is_product_in_cart($free_product_id, $cart_object)) {
            $cart_object->add_to_cart($free_product_id);
        }

        // Remove free products that don't meet conditions
        p2c_remove_invalid_free_products($cart_object, $free_product_ids, $bundled_product_ids, $shipping_rules, $cart_subtotal);
    }
}

/**
 * Get all bundled product IDs from cart
 */
function p2c_get_bundled_product_ids($cart_object) {
    $bundled_product_ids = array();
    foreach ($cart_object->get_cart() as $cart_item) {
        $product = $cart_item['data'];
        if ($product && method_exists($product, 'get_bundled_items')) {
            $bundled_items = $product->get_bundled_items();
            foreach ($bundled_items as $bundled_item) {
                $bundled_product_ids[] = $bundled_item->get_product_id();
            }
        }
    }
    return $bundled_product_ids;
}

/**
 * Calculate cart subtotal excluding free products
 */
function p2c_calculate_cart_subtotal($cart_object, $free_product_ids) {
    $cart_subtotal = 0;
    foreach ($cart_object->get_cart() as $cart_item) {
        if (!in_array($cart_item['product_id'], $free_product_ids, true) && isset($cart_item['line_total'])) {
            $cart_subtotal += $cart_item['line_total'];
        }
    }
    return $cart_subtotal;
}

/**
 * Check if product is in cart
 */
function p2c_is_product_in_cart($product_id, $cart_object) {
    foreach ($cart_object->get_cart() as $cart_item) {
        if ($cart_item['product_id'] === $product_id) {
            return true;
        }
    }
    return false;
}

/**
 * Remove free products that don't meet conditions
 */
function p2c_remove_invalid_free_products($cart_object, $free_product_ids, $bundled_product_ids, $shipping_rules, $cart_subtotal) {
    $free_products_in_cart = array();
    foreach ($cart_object->get_cart() as $cart_item_key => $cart_item) {
        if (in_array($cart_item['product_id'], $free_product_ids, true)) {
            $free_products_in_cart[$cart_item['product_id']] = $cart_item_key;
        }
    }

    foreach ($free_products_in_cart as $free_product_id => $cart_item_key) {
        $is_part_of_bundle = in_array($free_product_id, $bundled_product_ids, true);
        $meets_price_condition = p2c_check_price_condition($free_product_id, $shipping_rules, $cart_subtotal);

        if (!$is_part_of_bundle && !$meets_price_condition) {
            $cart_object->remove_cart_item($cart_item_key);
        }
    }
}

/**
 * Check if free product meets price condition
 */
function p2c_check_price_condition($free_product_id, $shipping_rules, $cart_subtotal) {
    foreach ($shipping_rules as $rule) {
        if ($rule['op_free_product'] === $free_product_id && $cart_subtotal >= $rule['min_value']) {
            return true;
        }
    }
    return false;
}




/**
 * Customize checkout fields
 */
function p2c_customize_checkout_fields($fields) {
    // Remove company fields
    unset($fields['billing']['billing_company'], $fields['shipping']['shipping_company']);

    // Move and modify the email field
    if (isset($fields['billing']['billing_email'])) {
        $email_field = $fields['billing']['billing_email'];
        unset($fields['billing']['billing_email']);
        $fields['billing'] = array_merge(array('billing_email' => $email_field), $fields['billing']);
        $fields['billing']['billing_email']['autofocus'] = false;
        $fields['billing']['billing_email']['priority'] = 1;
        $fields['billing']['billing_email']['class'] = array('form-row-wide');
    }

    // Remove autofocus from first name and adjust classes
    if (isset($fields['billing']['billing_first_name']['autofocus'])) {
        unset($fields['billing']['billing_first_name']['autofocus']);
    }
    $fields['billing']['billing_postcode']['class'] = array('form-row-first');
    $fields['billing']['billing_phone']['class'] = array('form-row-last');

    // Set maxlength for specific fields
    $fields['order']['order_comments']['maxlength'] = 80;
    $fields['billing']['billing_address_1']['maxlength'] = 30;
    $fields['billing']['billing_address_2']['maxlength'] = 30;

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'p2c_customize_checkout_fields');



/**
 * Change ADD TO CART button text
 */
function p2c_custom_add_to_cart_text() {
    return __('Add to Cart', 'woocommerce');
}
add_filter('woocommerce_product_single_add_to_cart_text', 'p2c_custom_add_to_cart_text');

/**
 * Allow duplicate SKUs
 */
function p2c_allow_duplicate_sku() {
    remove_filter('wc_product_has_unique_sku', 'wc_product_has_unique_sku', 10);
    add_filter('wc_product_has_unique_sku', '__return_false', 10, 2);
}
add_action('wp_loaded', 'p2c_allow_duplicate_sku');

/**
 * Allowed Max Variations in backend
 */
define('WC_MAX_LINKED_VARIATIONS', 250);

/**
 * Set AJAX variation threshold for frontend
 * Helps JavaScript filter to display only in-stock items or available sizes/attributes
 */
function p2c_ajax_variation_threshold($default, $product) {
    return 250;
}
add_filter('woocommerce_ajax_variation_threshold', 'p2c_ajax_variation_threshold', 10, 2);

// ============================================================================
// SECTION: WooCommerce Order Functions
// ============================================================================

/**
 * Make processing orders editable in backend
 */
function p2c_make_processing_orders_editable($is_editable, $order) {
    if ($order->get_status() === 'processing') {
        $is_editable = true;
    }
    return $is_editable;
}
add_filter('wc_order_is_editable', 'p2c_make_processing_orders_editable', 10, 2);

// ============================================================================
// SECTION: WooCommerce Shipping Functions
// ============================================================================

/**
 * Hide other shipping methods when free shipping is available
 * Updated to support WooCommerce 2.6 Shipping Zones
 *
 * @param array $rates Array of rates found for the package
 * @return array
 */
function p2c_hide_shipping_when_free_available($rates) {
    $threshold_amount = get_field('threshold_amount', 'option');

    if (!$threshold_amount) {
        return $rates;
    }

    $cart_total = WC()->cart->get_cart_contents_total();

    if ($cart_total >= $threshold_amount) {
        $allowed_rates = array('free_shipping', 'flat_rate:10');
        $filtered_rates = array();

        foreach ($rates as $rate_key => $rate) {
            if (in_array($rate->method_id, array('free_shipping'), true) || in_array($rate->id, $allowed_rates, true)) {
                $filtered_rates[$rate_key] = $rate;
            }
        }

        return !empty($filtered_rates) ? $filtered_rates : $rates;
    }

    return $rates;
}
add_filter('woocommerce_package_rates', 'p2c_hide_shipping_when_free_available', 100);

/**
 * Custom PayPal icon on checkout page
 */
function p2c_paypal_checkout_icon() {
    return get_stylesheet_directory_uri() . '/images/partners-paypal4.png';
}
add_filter('woocommerce_paypal_icon', 'p2c_paypal_checkout_icon');

// ============================================================================
// SECTION: WordPress Comments & Authentication
// ============================================================================

/**
 * Remove URL field from comment form
 */
function p2c_remove_comment_url_field($fields) {
    if (isset($fields['url'])) {
        unset($fields['url']);
    }
    return $fields;
}
add_filter('comment_form_default_fields', 'p2c_remove_comment_url_field');

/**
 * Set custom auth cookie expiration time
 */
function p2c_auth_cookie_expiration($seconds, $user_id, $remember) {
    if ($remember) {
        $expiration = 14 * DAY_IN_SECONDS; // 2 weeks
    } else {
        $expiration = 2 * DAY_IN_SECONDS; // 2 days
    }

    // Handle the Year 2038 problem
    if (PHP_INT_MAX - time() < $expiration) {
        $expiration = PHP_INT_MAX - time() - 5;
    }

    return $expiration;
}
add_filter('auth_cookie_expiration', 'p2c_auth_cookie_expiration', 99, 3);

// ============================================================================
// SECTION: WooCommerce Loop & Archive Functions
// ============================================================================

/**
 * Customize add to cart button for loop
 */
function p2c_change_loop_add_to_cart() {
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    add_action('woocommerce_after_shop_loop_item', 'p2c_template_loop_add_to_cart', 10);
}
add_action('init', 'p2c_change_loop_add_to_cart', 10);

/**
 * Template loop add to cart for variable products
 */
function p2c_template_loop_add_to_cart() {
    if (!is_product_category() && !is_shop()) {
        return;
    }

    global $product;
    if (!$product->is_type('variable')) {
        return;
    }

    remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
    add_action('woocommerce_single_variation', 'p2c_loop_variation_add_to_cart_button', 20);
    woocommerce_template_single_add_to_cart();
}

/**
 * Customize variable add to cart button for loop
 * Remove qty selector and simplify
 */
function p2c_loop_variation_add_to_cart_button() {
    global $product;
    ?>
    <div class="woocommerce-variation-add-to-cart variations_button">
        <button type="submit" class="single_add_to_cart_button button">
            <?php echo esc_html($product->single_add_to_cart_text()); ?>
        </button>
        <input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>" />
        <input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />
        <input type="hidden" name="variation_id" class="variation_id" value="0" />
    </div>
    <?php
}

/**
 * Add to cart button for simple products in loop
 */
function p2c_add_loop_button() {
    global $product;
    
    if (!is_product_category() && !is_shop()) {
        return;
    }
    
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }
    
    if ($product->is_type('simple')) {
        woocommerce_template_loop_add_to_cart();
    }
}
add_action('woocommerce_after_shop_loop_item', 'p2c_add_loop_button', 15);

// ============================================================================
// SECTION: Admin Functions
// ============================================================================

/**
 * Hide admin navigation options for order_export_only user role
 */
function p2c_order_export_only_role_customization() {
    $user = wp_get_current_user();
    if (in_array('order_export_only', (array) $user->roles, true)) {
        echo '<style>
            .toplevel_page_woocommerce ul li:nth-of-type(1n+3) {
                display:none!important;
            }
        </style>';
    }
}
add_action('admin_head', 'p2c_order_export_only_role_customization');



/**
 * Disable Gutenberg editor
 */
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);


/**
 * Update shipping ribbon on cart page when quantity is updated
 */
function p2c_update_shipping_ribbon($fragments) {
    ob_start();
    get_template_part('template-parts/header/shipping-notice-module', 'page');
    $fragments['div.shipping-noticed'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'p2c_update_shipping_ribbon');





/**
 * Bypass logout confirmation
 */
function p2c_bypass_logout_confirmation() {
    global $wp;
    if (isset($wp->query_vars['customer-logout'])) {
        wp_safe_redirect(str_replace('&amp;', '&', wp_logout_url(wc_get_page_permalink('myaccount'))));
        exit;
    }
}
add_action('template_redirect', 'p2c_bypass_logout_confirmation');




/**
 * Disable Ajax cart fragments script
 */
function p2c_dequeue_cart_fragments() {
    wp_dequeue_script('wc-cart-fragments');
}
add_action('wp_enqueue_scripts', 'p2c_dequeue_cart_fragments', 11);



/**
 * Disable shipping calculator on cart page
 */
function p2c_disable_shipping_calc_on_cart($show_shipping) {
    if (is_cart()) {
        return false;
    }
    return $show_shipping;
}
add_filter('woocommerce_cart_ready_to_calc_shipping', 'p2c_disable_shipping_calc_on_cart', 99);


/**
 * Redirect users after add to cart to checkout page
 */
function p2c_add_to_cart_redirect($url) {
    if (isset($_REQUEST['checkout_url']) && !empty($_REQUEST['checkout_url'])) {
        return wc_get_checkout_url();
    }
    return $url;
}
add_filter('woocommerce_add_to_cart_redirect', 'p2c_add_to_cart_redirect');


/**
 * Get related products based on primary category
 *
 * @param int $product_id Product ID
 * @return array Array of product IDs
 */
function p2c_get_related_products($product_id) {
    $primary_cat_id = get_post_meta($product_id, '_yoast_wpseo_primary_product_cat', true);

    if (!$primary_cat_id) {
        $terms = get_the_terms($product_id, 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            $primary_cat_id = $terms[0]->term_id;
        }
    }

    if (!$primary_cat_id) {
        return array();
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'post__not_in'   => array($product_id),
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $primary_cat_id,
            ),
        ),
        'meta_query'     => array(
            array(
                'key'   => '_stock_status',
                'value' => 'instock',
            ),
        ),
        'post_status'    => 'publish',
        'fields'         => 'ids',
    );

    $query = new WP_Query($args);
    return $query->posts;
}




/**
 * Move related products above product description on single page
 */
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
add_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 5);




/**
 * Custom Related Products based on selection done in backend
 */
function p2c_display_linked_products_field() {
    global $product_object, $post;
    ?>
    <p class="form-field">
        <label for="subscription_toggle_products">
            <?php esc_html_e('Add Related Products', 'woocommerce'); ?>
        </label>
        <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="subscription_toggle_ids" name="_subscription_toggle_ids[]" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo absint($post->ID); ?>">
            <?php
            $product_ids = $product_object->get_meta('_subscription_toggle_ids');
            if (!empty($product_ids)) {
                foreach ($product_ids as $product_id) {
                    $product = wc_get_product($product_id);
                    if (is_object($product)) {
                        echo '<option value="' . esc_attr($product_id) . '" selected="selected">' . esc_html($product->get_formatted_name()) . '</option>';
                    }
                }
            }
            ?>
        </select>
    </p>
    <?php
}
add_action('woocommerce_product_options_related', 'p2c_display_linked_products_field');

/**
 * Save related products selection
 */
function p2c_save_linked_products_field($product) {
    $data = isset($_POST['_subscription_toggle_ids']) ? array_map('absint', (array) $_POST['_subscription_toggle_ids']) : array();
    $product->update_meta_data('_subscription_toggle_ids', $data);
}
add_action('woocommerce_admin_process_product_object', 'p2c_save_linked_products_field', 10, 1);

// ============================================================================
// SECTION: Shortcodes
// ============================================================================

/**
 * Social icons shortcode
 */
function p2c_social_icons_shortcode() {
    return '<div class="social-icon">
        <a href="https://www.instagram.com/plus2clothing/?hl=en" target="_blank" rel="noopener"><i class="fa fa-instagram" aria-hidden="true"></i></a>
        <a target="_blank" href="https://www.facebook.com/plus2clothing/" rel="noopener"><i class="fa fa-facebook" aria-hidden="true"></i></a>
    </div>';
}
add_shortcode('socialhome', 'p2c_social_icons_shortcode');

// ============================================================================
// SECTION: URL & Redirect Functions
// ============================================================================

/**
 * Redirect 404 pages to homepage
 */
function p2c_redirect_404_to_homepage() {
    if (is_404()) {
        wp_safe_redirect(home_url('/'));
        exit;
    }
}
add_action('template_redirect', 'p2c_redirect_404_to_homepage');


/**
 * Move coupon form to review order section
 */
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
add_action('woocommerce_review_order_after_order_total', 'p2c_checkout_coupon_form_custom');
function p2c_checkout_coupon_form_custom() {
    echo '<tr class="coupon-form"><td colspan="2">';
    wc_get_template(
        'checkout/form-coupon.php',
        array(
            'checkout' => WC()->checkout(),
        )
    );
    echo '</td></tr>';
}


/**
 * Custom video shortcode
 * Note: Scripts should be enqueued separately, not inline
 */
function p2c_video_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'vid'  => '',
            'text' => '',
            'id'   => wp_rand(),
        ),
        $atts,
        'custom-video'
    );

    $vid = esc_attr(sanitize_text_field($atts['vid']));
    $text = esc_html($atts['text']);
    $id = absint($atts['id']);

    if (empty($vid)) {
        return '';
    }

    // Enqueue fancybox script if not already enqueued
    wp_enqueue_script('p2c-fancy-js');

    $output = '<a class="custom_video" href="#wptuts-video-container-' . $id . '">' . $text . '</a>';
    $output .= '<div id="wptuts-video-container-' . $id . '" style="display:none">';
    $output .= '<iframe src="https://www.youtube.com/embed/' . esc_url($vid) . '" height="400" width="750" allowfullscreen frameborder="0"></iframe>';
    $output .= '</div>';

    // Add inline script for fancybox initialization
    wp_add_inline_script('p2c-fancy-js', '
        jQuery(document).ready(function($) {
            $(".custom_video").fancybox({
                overlayShow: true,
                width: 800,
                height: 600,
                autoScale: false,
                transitionIn: "none",
                transitionOut: "none"
            });
        });
    ');

    return $output;
}
add_shortcode('custom-video', 'p2c_video_shortcode');


/**
 * SVG Favicon
 */
function p2c_svg_favicon() {
    $theme_uri = get_stylesheet_directory_uri();
    echo '<link rel="icon" href="' . esc_url($theme_uri . '/images/favicons/darkn.svg') . '" type="image/svg+xml" media="(prefers-color-scheme: light)">';
    echo '<link rel="icon" href="' . esc_url($theme_uri . '/images/favicons/lightn.svg') . '" type="image/svg+xml" media="(prefers-color-scheme: dark)">';
}
add_action('wp_head', 'p2c_svg_favicon', 100);


/**
 * Manage checkout auto search field scripts
 * Dequeue on non-checkout pages, enqueue on checkout page
 */
function p2c_manage_checkout_autofill_scripts() {
    if (is_checkout()) {
        wp_enqueue_script('wc-af-main');
        wp_enqueue_script('wc-af-api');
    } else {
        wp_dequeue_script('wc-af-main');
        wp_dequeue_script('wc-af-api');
    }
}
add_action('wp_enqueue_scripts', 'p2c_manage_checkout_autofill_scripts', 99);




/**
 * Set gallery image size for bundle products
 */
function p2c_gallery_image_size($size) {
    global $product;
    if ($product && has_term('bundle', 'product_type', $product->get_id())) {
        return 'full';
    }
    return $size;
}
add_filter('woocommerce_gallery_image_size', 'p2c_gallery_image_size', 99);

/**
 * Include forest template in footer
 */
function p2c_include_forest_template() {
    include get_stylesheet_directory() . '/template-parts/header/forest.php';
}
add_action('wp_footer', 'p2c_include_forest_template');

/**
 * Carousel shortcode
 */
function p2c_carousel_shortcode() {
    ob_start();
    include get_stylesheet_directory() . '/template-parts/shop/carousel.php';
    return ob_get_clean();
}
add_shortcode('cacarousel', 'p2c_carousel_shortcode');

/**
 * Custom cart item name
 */
function p2c_custom_cart_item_name($title, $cart_item, $cart_item_key) {
    $product_id = isset($cart_item['product_id']) ? $cart_item['product_id'] : 0;
    if ($product_id) {
        return get_the_title($product_id);
    }
    return $title;
}
add_filter('woocommerce_cart_item_name', 'p2c_custom_cart_item_name', 10, 3);

// ============================================================================
// SECTION: Disabled Functions (Commented Out)
// ============================================================================
// These functions are defined but intentionally disabled.
// Uncomment the add_action/add_filter lines to enable them.

/**
 * Custom product order on category pages
 * Currently disabled - uncomment to enable
 */
function p2c_custom_product_order($query) {
    if (!is_admin() && $query->is_main_query() && is_product_category()) {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    }
}
// add_action('pre_get_posts', 'p2c_custom_product_order');

/**
 * Allow product category URLs without product-category prefix
 */
function p2c_product_category_url_rewrite($vars) {
    if (!class_exists('WooCommerce')) {
        return $vars;
    }

    global $wpdb;
    $slug = '';
    if (!empty($vars['pagename'])) {
        $slug = $vars['pagename'];
    } elseif (!empty($vars['name'])) {
        $slug = $vars['name'];
    } elseif (!empty($vars['category_name'])) {
        $slug = $vars['category_name'];
    } elseif (!empty($vars['attachment'])) {
        $slug = $vars['attachment'];
    }

    if ($slug) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT t.term_id FROM {$wpdb->terms} t 
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id 
            WHERE tt.taxonomy = 'product_cat' AND t.slug = %s",
            $slug
        ));

        if ($exists) {
            $new_vars = array('product_cat' => $slug);
            if (!empty($vars['paged'])) {
                $new_vars['paged'] = $vars['paged'];
            } elseif (!empty($vars['page'])) {
                $new_vars['paged'] = $vars['page'];
            }
            if (!empty($vars['orderby'])) {
                $new_vars['orderby'] = $vars['orderby'];
            }
            if (!empty($vars['order'])) {
                $new_vars['order'] = $vars['order'];
            }
            return $new_vars;
        }
    }

    return $vars;
}
add_filter('request', 'p2c_product_category_url_rewrite');




/**
 * Disable comments on pages
 * Currently disabled - uncomment to enable
 */
function p2c_disable_comments_on_pages() {
    if (is_page()) {
        remove_post_type_support('page', 'comments');
        remove_post_type_support('page', 'trackbacks');
    }
}
// add_action('init', 'p2c_disable_comments_on_pages');

function p2c_hide_existing_comments($comments, $post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'page') {
        return array();
    }
    return $comments;
}
// add_filter('comments_array', 'p2c_hide_existing_comments', 10, 2);

function p2c_remove_comments_admin_bar($wp_admin_bar) {
    if (is_page()) {
        $wp_admin_bar->remove_node('comments');
    }
}
// add_action('admin_bar_menu', 'p2c_remove_comments_admin_bar', 999);

// ============================================================================
// SECTION: AJAX Handlers
// ============================================================================

/**
 * Fixed cart progress bar feature
 */

/**
 * Count total products for a certain category in cart
 *
 * @param int $category_id Category ID
 * @return int Count of items
 */
function p2c_cat_cart_count($category_id) {
    $cat_count = 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (has_term($category_id, 'product_cat', $cart_item['product_id'])) {
            $cat_count += $cart_item['quantity'];
        }
    }
    return $cat_count;
}

/**
 * Get dynamic category deal settings
 *
 * @param int $category_id Category ID
 * @return array|null Deal settings or null
 */
function p2c_get_dynamic_category_deal($category_id) {
    $required_count = get_term_meta($category_id, 'required_count', true);
    $deal_text = get_term_meta($category_id, 'deal_text', true);

    if (!$required_count || !$deal_text) {
        return null;
    }

    return array(
        'required_count' => $required_count,
        'deal_text'      => $deal_text,
        'checkout_url'   => wc_get_checkout_url(),
    );
}

/**
 * AJAX handler for refreshing mini cart count
 */
function p2c_refresh_mini_cart_count() {
    $category_id = isset($_POST['category_id']) ? absint($_POST['category_id']) : 0;

    if (!function_exists('get_field')) {
        wp_send_json_error(array('message' => 'ACF plugin not available'));
    }

    $min_deal_count = get_field('min_deal_count', 'product_cat_' . $category_id);
    $pro_deal_msg = get_field('pro_deal_msg', 'product_cat_' . $category_id);
    $count = p2c_cat_cart_count($category_id);
    $goal_message = $count >= $min_deal_count ? $pro_deal_msg : '';
    $goal_reached = $count >= $min_deal_count ? 'yes' : 'no';

    $html = '<div class="fixedcart">
                <div id="progressbar">
                    <p>CHOOSE YOUR ' . esc_html($min_deal_count) . ' ITEMS - CART WILL AUTO UPDATE</p>
                    <div class="countwrap">
                        <span class="catSelectedPcount">' . esc_html($count) . '</span>/' . esc_html($min_deal_count) . ' items
                    </div>
                </div>
                <div id="mini-cart-count">' . wp_kses_post($goal_message) . '</div>
            </div>';

    wp_send_json_success(
        array(
            'html' => $html,
            'data' => array(
                'count'       => $count,
                'goalReached' => $goal_reached,
            ),
        )
    );
}
add_action('wp_ajax_wc_refresh_mini_cart_count', 'p2c_refresh_mini_cart_count');
add_action('wp_ajax_nopriv_wc_refresh_mini_cart_count', 'p2c_refresh_mini_cart_count');






/**
 * Filter product query for search - show only in-stock products
 * Currently disabled - uncomment to enable
 */
function p2c_filter_product_query_for_search($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('meta_key', '_stock_status');
        $query->set('meta_value', 'instock');
    }
    return $query;
}
// add_action('pre_get_posts', 'p2c_filter_product_query_for_search');





/**
 * Override parent theme quick view - disable for bundle products
 */
function p2c_override_parent_theme_features() {
    remove_action('flatsome_product_box_actions', 'flatsome_lightbox_button', 50);
    add_action('flatsome_product_box_actions', 'p2c_child_lightbox_button', 50);
}
add_action('after_setup_theme', 'p2c_override_parent_theme_features', 20);

/**
 * Custom lightbox button - only for simple and variable products
 */
function p2c_child_lightbox_button() {
    if (get_theme_mod('disable_quick_view', 0)) {
        return;
    }

    global $product;
    if (!isset($product)) {
        $product = wc_get_product(get_the_ID());
    }

    if ($product && ($product->is_type('simple') || $product->is_type('variable'))) {
        wp_enqueue_script('wc-add-to-cart-variation');
        echo '<a class="quick-view" data-prod="' . absint($product->get_id()) . '" href="#quick-view">' . esc_html__('Quick View', 'flatsome') . '</a>';
    }
}




/**
 * Custom breadcrumb function using Yoast's primary category
 */
function p2c_custom_yoast_breadcrumb() {
    if (!is_singular('product')) {
        return;
    }

    $product_id = get_the_ID();
    $primary_cat_id = get_post_meta($product_id, '_yoast_wpseo_primary_product_cat', true);
    $primary_category = $primary_cat_id ? get_term($primary_cat_id, 'product_cat') : null;

    if ($primary_category && !is_wp_error($primary_category)) {
        $primary_category_link = get_term_link($primary_category->term_id, 'product_cat');
        $primary_category_link = str_replace('/product-category/', '/', $primary_category_link);
        $primary_category_name = $primary_category->name;
    } else {
        $primary_category_link = '#';
        $primary_category_name = '';
    }

    $home_url = esc_url(home_url('/'));
    $buy_url = esc_url(home_url('/buy/'));
    $category_url = esc_url($primary_category_link);
    $category_name = esc_html($primary_category_name);

    echo '<p id="breadcrumbs">';
    echo '<a href="' . $home_url . '">Home</a> » ';
    echo '<a href="' . $buy_url . '">Buy</a> » ';
    echo '<a href="' . $category_url . '">' . $category_name . '</a>';
    echo '</p>';
}






/**
 * Decrypt base64 encoded IDs
 *
 * @param string $data Base64 encoded data
 * @return string|false Decoded data or false on failure
 */
function p2c_decrypt_base64($data) {
    return base64_decode($data, true);
}



/**
 * Hide checkout address fields for virtual products only
 * Currently disabled - uncomment to enable
 */
function p2c_hide_address_fields_for_virtual_products($fields) {
    $has_physical_product = false;

    foreach (WC()->cart->get_cart() as $cart_item) {
        if (!$cart_item['data']->is_virtual()) {
            $has_physical_product = true;
            break;
        }
    }

    if (!$has_physical_product) {
        $address_fields = array(
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_postcode',
            'billing_country',
            'billing_state',
            'billing_address_rpgaac',
        );

        foreach ($address_fields as $field) {
            unset($fields['billing'][$field]);
        }
    }

    return $fields;
}
// add_filter('woocommerce_checkout_fields', 'p2c_hide_address_fields_for_virtual_products');







/**
 * Restrict feeder category products - redirect to homepage
 */
function p2c_restrict_feeder_category() {
    if (is_product()) {
        global $post;
        $restricted_category_slug = 'feeder';
        if (has_term($restricted_category_slug, 'product_cat', $post)) {
            wp_safe_redirect(home_url('/'));
            exit;
        }
    }
}
add_action('template_redirect', 'p2c_restrict_feeder_category');

/**
 * Custom message for specific products
 * Currently disabled - uncomment to enable
 */
function p2c_custom_message_for_specific_products() {
    global $product;
    $categories = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'ids'));
    $tags = wp_get_post_terms($product->get_id(), 'product_tag', array('fields' => 'ids'));
    if (in_array(200, $categories, true) || in_array(200, $tags, true)) {
        echo '<div class="single-deal-msg" style="color: green; font-weight: bold;">[ Buy in bulk & save. Grab 5 for $99 (Cart will auto update) ]</div>';
    }
}
// add_action('woocommerce_after_add_to_cart_button', 'p2c_custom_message_for_specific_products');

// Remove default tabs (bottom of the page)
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

// Add tabs inside summary AFTER Add to Cart
add_action( 'woocommerce_single_product_summary', 'my_custom_product_tabs_inside_summary', 35 );
function my_custom_product_tabs_inside_summary() {
    echo '<div class="custom-summary-tabs">';
    woocommerce_output_product_data_tabs();
    echo '</div>';
}
