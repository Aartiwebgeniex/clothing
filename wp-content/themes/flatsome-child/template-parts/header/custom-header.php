<meta property="og:image" content="<?php echo get_site_url(); ?>/wp-content/uploads/2019/09/image1_location1_ver2.jpg" />
<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicons/favicon.ico" sizes="16x16">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicons/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicons/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicons/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicons/apple-touch-icon-precomposed.png">


<?php
global $product, $woocommerce;

// Exclude facebook login reffer and replace with sitename to correct GA stats.
if (is_page('my-account') || is_page('checkout')) {
    // Check if 'HTTP_REFERER' is set in the server array to avoid undefined index warning
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referral_url = $_SERVER['HTTP_REFERER'];

        // Check if the referral URL contains 'facebook.com'
        if (strpos($referral_url, 'facebook.com') !== false) { ?>
                        <script>
                            ga('set', 'referrer', <?php echo wp_json_encode(home_url('/')); ?>);
                        </script>
                    <?php
        }
    }
}


if (is_product()) {
    $id = wc_get_product()->get_id();
    if ($id == 80412) { ?>
        <script>
            window.location.replace(<?php echo wp_json_encode(home_url('/')); ?>);
        </script>
    <?php }
}
?>


<script type='text/javascript'>
    /* <![CDATA[ */
    var wc_cart_fragments_params = {
        "ajax_url": "\/wp-admin\/admin-ajax.php",
        "wc_ajax_url": "\/?wc-ajax=%%endpoint%%",
        "i18n_view_cart": "View cart",
        "cart_url": <?php echo wp_json_encode(wc_get_cart_url()); ?>,
        "is_cart": "",
        "cart_redirect_after_add": "no"
    };
    /* ]]> */
</script>

<?php // shop page filter to pass info in URL to auto select size in single page     ?>
<script>
    <?php
    if (isset($_GET['pa_size'])) {
        $pa_size = sanitize_text_field($_GET['pa_size']);
        ?>
        var pa_size_value = <?php echo wp_json_encode($pa_size); ?>;
    <?php } ?>
</script>