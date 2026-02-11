<?php
// convert jquery
if (is_product()) {
    $pa_size = '';
    if (isset($_GET['pa_size'])) {
        $pa_size = sanitize_text_field($_GET['pa_size']);
    }
    ?>
    <script type="text/javascript">
        if (jQuery('#pa_size').length > 0) {
            jQuery('#pa_size').prop('value', '<?php echo esc_js($pa_size); ?>');
            jQuery('#pa_size').trigger('change');
        }
    </script>
    <script>
        jQuery(document).ready(function () {
            if (jQuery('input[name="quantity"]').length > 0) {
                jQuery('input[name="quantity"]').val(1);
            }
        })
    </script>
    <?php
    // previously this code was in single-product.php
    global $product;
    $ItemId         = $product->get_id();
    $ImageUrl       = wp_get_attachment_image_src(get_post_thumbnail_id($ItemId), 'single-post-thumbnail')[0];
    $Title          = $product->get_title();
    $ProductUrl     = get_permalink($ItemId);
    $Price          = $product->get_price();
    $RegularPrice   = $product->get_regular_price();
    $DiscountAmount = intval($RegularPrice) - intval($Price);
    $terms          = get_terms('product_tag');
    ?>
    <script>
        var Title = <?php echo wp_json_encode($Title); ?>;
        var ItemId = <?php echo absint($ItemId); ?>;
        var ImageUrl = <?php echo wp_json_encode($ImageUrl); ?>;
        var ProductUrl = <?php echo wp_json_encode($ProductUrl); ?>;
        var Price = <?php echo wp_json_encode($Price); ?>;
        var DiscountAmount = <?php echo absint($DiscountAmount); ?>;
        var RegularPrice = <?php echo wp_json_encode($RegularPrice); ?>;
        var _learnq = _learnq || [];
        _learnq.push(['track', 'Viewed Product', {
            Title: Title,
            ItemId: ItemId,
            ImageUrl: ImageUrl,
            Url: ProductUrl,
            Metadata: {
                Price: Price,
                DiscountAmount: DiscountAmount,
                RegularPrice: RegularPrice
            }
        }]);
    </script>
<?php }


if (is_product_category() || is_product()) {
    get_template_part('template-parts/footer/category-chart-module', 'page');
}

if (is_product_category()) {
    $category = get_queried_object();
    if ($category) {
        $category_id = $category->term_id;
        $activate_progress_bar = get_field('activate_pro_bar', 'product_cat_' . $category_id);
        $min_deal_count        = get_field('min_deal_count', 'product_cat_' . $category_id);
        if ($activate_progress_bar && $min_deal_count && $min_deal_count > 0) {
            get_template_part('template-parts/footer/category-fixed-cart-module', 'page');
        }
    }
}

?>