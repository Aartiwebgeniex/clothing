<div id="fixedcart-wrapper">
    <div class="fixedcart">
        <div id="progressbar">
            <p>Cart Deal</p>
            <div class="countwrap">
                <span class="catSelectedPcount"></span>
            </div>
        </div>
        <div id="mini-cart-count"></div>
    </div>
</div>
<style>
    body #progressbar p {
        display: block;
    }
</style>

<?php
$category_id           = get_queried_object_id();
$activate_progress_bar = get_field('activate_pro_bar', 'product_cat_' . $category_id);
$min_deal_count        = get_field('min_deal_count', 'product_cat_' . $category_id);
$pro_deal_msg          = get_field('pro_deal_msg', 'product_cat_' . $category_id);
?>

<div id="progressBarData" data-activate-progress-bar="<?php echo esc_attr($activate_progress_bar); ?>" data-min-deal-count="<?php echo esc_attr($min_deal_count); ?>" data-pro-deal-msg="<?php echo esc_html($pro_deal_msg); ?>">
</div>

<script>

    jQuery(document).ready(function ($) {
        updateFixedCart();
    })
    function updateFixedCart() {
        jQuery.ajax({
            url: woocommerce_params.ajax_url,
            method: "POST",
            data: {
                action: 'wc_refresh_mini_cart_count',
                category_id: <?php echo absint($category_id); ?>,
            },
            success: function (response) {
                if (response.success) {
                    // The HTML content is in response.data.html
                    jQuery('#fixedcart-wrapper').html(response.data.html);
                    //  alert(JSON.stringify(response.data.data.));
                    if (response.data.data.goalReached == 'yes') {
                        // Since you are updating styles based on data, you might want to ensure these changes are only applied if goal is reached.
                        jQuery('body #progressbar p').css('display', 'block');
                        updateProgressBar();
                    } else {
                        updateProgressBar();
                    }
                } else {
                    console.error("Error: ", response.data); // Log any error message that might have been sent
                }
            },

            error: function (error) {
                console.error("There was an error", error);
            }
        });
    }
    jQuery(document.body).on('added_to_cart removed_from_cart updated_cart_totals', updateFixedCart);


console.log('add or remove or update event triggered');



    function updateProgressBar() {
        var progressBarData = jQuery('#progressBarData');
        var activateProgressBar = progressBarData.data('activate-progress-bar');
        var minDealCount = parseInt(progressBarData.data('min-deal-count'), 10);
        var proDealMsg = progressBarData.data('pro-deal-msg');
        var catSelectedPcount = parseInt(jQuery('.catSelectedPcount').html(), 10);

        if (activateProgressBar && catSelectedPcount === 0) {
            jQuery('.countwrap').hide();
            jQuery('#progressbar p').show();
        } else {
            jQuery('.countwrap').show();
            jQuery('#progressbar p').hide();
            var width = Math.min((catSelectedPcount / minDealCount) * 100, 100) + '%';
            jQuery('#progressbar>div').css('width', width);
            if (catSelectedPcount >= minDealCount) {
                jQuery('#mini-cart-count').html(proDealMsg + ' Proceed to <a class="cart-contents" href="<?php echo esc_url(wc_get_checkout_url()); ?>" title="View your shopping cart">Checkout</a>');
            }
        }
    }


    // Call the function with backend values
    updateProgressBar();





</script>