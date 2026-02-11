jQuery(document).ready(function ($) {
    'use strict';

    if (!jQuery('body').hasClass('woocommerce-cart') && !jQuery('body').hasClass('woocommerce-checkout')) {
        return;
    }

    // ============================================
    // Cart and Checkout Page Setup
    // ============================================
    var siteUrl = window.location.origin;
    var cURL, word;

    if (jQuery('body').hasClass('woocommerce-checkout')) {
        cURL = siteUrl + '/cart/';
        word = 'Back to Cart';
    } else if (jQuery('body').hasClass('woocommerce-cart')) {
        cURL = siteUrl + '/buy/';
        word = 'Continue Shopping';
    }

    if (cURL && word) {
        // Create header link using jQuery (replaces string concatenation)
        var $linkHtml = jQuery('<div>', {
            'class': 'c-cart-logo'
        }).append(
            jQuery('<span>', {
                'class': 'c-goback'
            }).append(
                jQuery('<h2>').append(
                    jQuery('<a>', {
                        href: cURL,
                        text: '<< ' + word
                    })
                )
            ),
            jQuery('<span>', {
                'class': 'logos'
            }).append(
                jQuery('<a>', {
                    href: siteUrl
                }).append(
                    jQuery('<img>', {
                        src: siteUrl + '/wp-content/themes/flatsome-child/images/plus-2-clothing-logoWEB.jpg',
                        alt: 'Plus 2 Clothing'
                    })
                )
            ),
            jQuery('<span>', {
                'class': 'c-currency'
            })
        );

        $linkHtml.insertBefore("#wrapper");

        // Create footer copyright
        var currentYear = new Date().getFullYear();
        jQuery('<div>', {
            'class': 'c-cart-footer',
            text: 'Copyright © ' + currentYear + ' Plus 2 Clothing.'
        }).insertBefore("footer");
    }

    // ============================================
    // Checkout Updated Handler
    // ============================================
    jQuery(document.body).on('updated_checkout', function () {
        jQuery('ul#shipping_method li input[checked="checked"]').trigger('click');
    });

    // ============================================
    // Social Login Setup
    // ============================================
    var socialLoginTimeout = setTimeout(function () {
        jQuery('.js-show-social-login').trigger('click');
        jQuery('.wc-social-login').find("p:first").hide();
        jQuery('.js-show-social-login').hide();
    }, 5);

    // Cleanup timeout
    jQuery(window).on('beforeunload', function() {
        if (socialLoginTimeout) {
            clearTimeout(socialLoginTimeout);
        }
    });

    // ============================================
    // Facebook Login Styling
    // ============================================
    jQuery("#facebook-login .banner-bg").removeAttr("style").attr("style", "background-color: #DDD;");

    // ============================================
    // Toggle Discount Items
    // ============================================
    function toggle_discount_items() {
        jQuery('.cart-subtotal-items').toggleClass('discounted_items');
    }

    // ============================================
    // Cart Page Specific Functionality
    // ============================================
    if (jQuery("body").hasClass("woocommerce-cart")) {
        // Quick view handlers
        jQuery('.sfn-cart-addons .product-small .box-image .image-zoom .quick-view-popup').on('click', function () {
            jQuery(this).closest('.image-zoom').closest('.box-image').children('.image-tools.hover-slide-in').children('a.quick-view-added').trigger('click');
        });

        jQuery('.sfn-cart-addons .product-small .title-wrapper p a').on('click', function (event) {
            event.preventDefault();
            jQuery(this).closest('.box-text-products').closest('.product-small').children('.box-image').children('.image-tools.hover-slide-in').children('a.quick-view-added').trigger('click');
        });
    }

    // ============================================
    // Checkout Page Specific Functionality
    // ============================================
    if (jQuery("body").hasClass("woocommerce-checkout")) {
        // Order comments character limit
        jQuery("#order_comments").after('<sub style="text-align:right"><b>Character Limit</b> <span id="order_comments_limit">80</span></sub>');
        jQuery("#order_comments").on('keyup', function (e) {
            try {
                var tval = jQuery.trim(jQuery('#order_comments').val());
                var tlength = tval.length;
                var set = 80;
                var remain = parseInt(set - tlength);
                jQuery('body #order_comments_limit').html(remain);
                if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                    jQuery('#order_comments').val(tval.substring(0, tlength - 1));
                }
            } catch (err) {
                console.log(err.message);
            }
        });

        // Billing address 1 character limit
        jQuery("#billing_address_1").after('<sub style="text-align:right"> Character Limit : <span id="address_limit">30</span></sub>');
        jQuery("#billing_address_1").on('keyup', function (e) {
            var tval = jQuery('#billing_address_1').val();
            var tlength = tval.length;
            var set = 30;
            var remain = parseInt(set - tlength);

            jQuery('body #address_limit').html(remain);
            if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                jQuery('#billing_address_1').val(tval.substring(0, tlength - 1));
            } else if (remain <= 0) {
                var lastword = tval.substring(0, this.selectionStart || tval.length).split(" ").pop();
                var lastIndex = tval.lastIndexOf(" ");
                var newAddress = tval.substring(0, lastIndex);
                jQuery.trim(jQuery('#billing_address_1').val(newAddress));
                jQuery('#billing_address_2').val(lastword);
                jQuery('#billing_address_2').focus();
            }
        });

        // Billing address 2 character limit
        jQuery("#billing_address_2").after('<sub style="text-align:right">Character Limit : <span id="address_limit2">30</span></sub>');
        jQuery("#billing_address_2").on('keyup', function (e) {
            var tval = jQuery('#billing_address_2').val();
            var tlength = tval.length;
            var set = 30;
            var remain = parseInt(set - tlength);
            jQuery('body #address_limit2').html(remain);
        });

        // Move autofill field
        jQuery('#autofill_checkout_field_field').insertAfter('#billing_last_name_field');

        // Move login forms
        var loginFormTimeout = setTimeout(function () {
            var $loginForms = jQuery('.woocommerce-form-login, .woocommerce-form-login-toggle');
            if ($loginForms.length) {
                jQuery('form.woocommerce-checkout .large-7').prepend($loginForms);
                $loginForms.wrapAll('<div class="border-wrap"></div>');
            }
        }, 500);

        // Cleanup timeout
        jQuery(window).on('beforeunload', function() {
            if (loginFormTimeout) {
                clearTimeout(loginFormTimeout);
            }
        });
    }
});
