/**
 * Instantio Custom Scripts - Suggested Products Override
 * This file contains custom JavaScript for the suggested products feature
 * Safe from plugin updates
 */

(function ($) {
    'use strict';

    // Track if user has manually toggled to prevent auto-open interference
    var manuallyToggled = false;
    var autoOpenTimeout = null;

    $(document).ready(function () {

        // Suggested Products Toggle (external button on cart)
        $(document).on("click", ".ins-suggested-products-toggle-btn", function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $suggestedProducts = $('.ins-suggested-products');
            var $toggleBtn = $(this);

            // Toggle the collapsed state
            $suggestedProducts.toggleClass('collapsed');
            $toggleBtn.toggleClass('collapsed');

            // Mark as manually toggled to prevent auto-open
            manuallyToggled = true;

            // Clear any pending auto-open
            if (autoOpenTimeout) {
                clearTimeout(autoOpenTimeout);
                autoOpenTimeout = null;
            }
        });

        // Also make it available as a global function for inline onclick
        window.toggleSuggestedProducts = function () {
            var $suggestedProducts = $('.ins-suggested-products');
            var $toggleBtn = $('.ins-suggested-products-toggle-btn');

            // Toggle the collapsed state
            $suggestedProducts.toggleClass('collapsed');
            $toggleBtn.toggleClass('collapsed');

            // Mark as manually toggled to prevent auto-open
            manuallyToggled = true;

            // Clear any pending auto-open
            if (autoOpenTimeout) {
                clearTimeout(autoOpenTimeout);
                autoOpenTimeout = null;
            }
        };

        // Auto-open/close for slide cart
        $(document).on("click", ".ins-click-to-show.sidecart", function (e) {
            var isOpening = !$(".ins-checkout-layout-3.slide").hasClass("active");

            if (isOpening) {
                // Reset manual toggle flag when cart opens
                manuallyToggled = false;

                // Auto-open suggested products after 1.5 seconds if cart is opening
                autoOpenTimeout = setTimeout(function () {
                    if ($(".ins-checkout-layout-3.slide").hasClass("active") && !manuallyToggled) {
                        if ($('.ins-suggested-products').hasClass('collapsed')) {
                            $('.ins-suggested-products').removeClass('collapsed');
                            $('.ins-suggested-products-toggle-btn').removeClass('collapsed');
                        }
                    }
                }, 1500);
            } else {
                // Cart is closing
                if (autoOpenTimeout) {
                    clearTimeout(autoOpenTimeout);
                    autoOpenTimeout = null;
                }
            }
        });

        // Auto-open/close for popup cart
        $(document).on("click", ".ins-click-to-show.popupcart", function (e) {
            var isOpening = !$(".ins-checkout-popup").hasClass("active");

            if (isOpening) {
                // Reset manual toggle flag when cart opens
                manuallyToggled = false;

                // Auto-open suggested products after 1.5 seconds if cart is opening
                autoOpenTimeout = setTimeout(function () {
                    if ($(".ins-checkout-popup").hasClass("active") && !manuallyToggled) {
                        if ($('.ins-suggested-products').hasClass('collapsed')) {
                            $('.ins-suggested-products').removeClass('collapsed');
                            $('.ins-suggested-products-toggle-btn').removeClass('collapsed');
                        }
                    }
                }, 1500);
            } else {
                // Cart is closing
                if (autoOpenTimeout) {
                    clearTimeout(autoOpenTimeout);
                    autoOpenTimeout = null;
                }
            }
        });

        // Close suggested products when cart closes (close button)
        $(document).on("click", ".ins-checkout-close", function (e) {
            $('.ins-suggested-products').addClass('collapsed');
            $('.ins-suggested-products-toggle-btn').addClass('collapsed');
            manuallyToggled = false;

            if (autoOpenTimeout) {
                clearTimeout(autoOpenTimeout);
                autoOpenTimeout = null;
            }
        });

        // Close suggested products when cart closes (overlay)
        $(document).on("click", ".ins-checkout-overlay", function (e) {
            $('.ins-suggested-products').addClass('collapsed');
            $('.ins-suggested-products-toggle-btn').addClass('collapsed');
            manuallyToggled = false;

            if (autoOpenTimeout) {
                clearTimeout(autoOpenTimeout);
                autoOpenTimeout = null;
            }
        });

    });

    // Addon Modal Logic
    $(document).ready(function () {
        // Variation selection inside modal
        $('#myModal1 .variation-option').on('click', function () {
            var variationId = $(this).data('id');
            $('.variable_add_to_cart_button').show().data('variation_id', variationId);
            $('#myModal1 .variable_add_to_cart_button').attr('data-product_id', variationId);

            // Visual feedback
            $('#myModal1 .variation-option').removeClass('selected');
            $(this).addClass('selected');
        });

        // Initialize Fancybox for the modal
        if ($.fn.fancybox) {
            $("a#addon_modal").fancybox({
                'titleShow': false,
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'easingIn': 'easeOutBack',
                'easingOut': 'easeInBack',
                'width': 420,
                'height': 350,
                'autoSize': false
            });
        }
    });

})(jQuery);
