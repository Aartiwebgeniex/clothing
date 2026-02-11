<?php
/**
 * Shop breadcrumb
 *
 * @see              woocommerce_breadcrumb()
 *
 * @author           WooThemes
 * @package          WooCommerce/Templates
 * @version          2.3.0
 * @flatsome-version 3.19.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Custom breadcrumb implementation
echo "<div style='margin-bottom:10px;'>";

if (is_singular('product')) {
	if (function_exists('p2c_custom_yoast_breadcrumb')) {
		p2c_custom_yoast_breadcrumb();
	} else {
		echo do_shortcode('[wpseo_breadcrumb]');
	}
} else {
	echo do_shortcode('[wpseo_breadcrumb]');
}

echo "</div>";
