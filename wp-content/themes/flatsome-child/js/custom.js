

jQuery(document).ready(function ($) {
    'use strict';

    // ============================================
    // Single click add to cart for size buttons
    // ============================================
    if (jQuery('body').hasClass('term-5-for-99')) {
        jQuery(document).on('click', '#picker_pa_size a', function (e) {
            // custom code 25 nov
            e.preventDefault(); // Prevent the default action of the link

            var $this = jQuery(this);
            var $form = $this.closest('.variations_form');
            var $addToCartBtn = $form.find('.single_add_to_cart_button');

            // Get the value of the clicked size
            var selectedSize = $this.parent().data('value');

            // Remove 'selected' class from all options
            $this.closest('#picker_pa_size').find('.swatch-wrapper').removeClass('selected');

            // Add 'selected' class to the clicked option
            $this.closest('.swatch-wrapper').addClass('selected');

            // If we already have a variation found listener, off it to prevent duplicates/stacking
            $form.off('found_variation.auto_add');

            // Listen for the variation to be found
            $form.on('found_variation.auto_add', function (event, variation) {
                if (variation && variation.is_purchasable) {

                    // custom code 25 nov - Polling to ensure button is ready and variation ID is set
                    var attempts = 0;
                    var maxAttempts = 20; // 2 seconds max
                    var checkInterval = setInterval(function () {
                        attempts++;
                        var $btn = $form.find('.single_add_to_cart_button');
                        var varId = $form.find('input[name="variation_id"]').val();

                        // Check if button is enabled and variation ID is populated
                        if (!$btn.hasClass('disabled') && !$btn.hasClass('wc-variation-selection-needed') && varId && varId != 0) {
                            clearInterval(checkInterval);
                            $btn.click();
                        }

                        if (attempts >= maxAttempts) {
                            clearInterval(checkInterval);
                        }
                    }, 100);
                }
                // Clean up listener
                $form.off('found_variation.auto_add');
            });

            // Trigger the change on the select to start the variation lookup
            // We need to find the specific select for this product context if there are multiple
            var $select = $form.find('#pa_size');
            $select.val(selectedSize).trigger('change');
        });
    }

    // ============================================
    // Cart page - reload on cart totals update
    // ============================================
    if (jQuery('body').hasClass('woocommerce-cart')) {
        jQuery(document.body).on('updated_cart_totals', function () {
            location.reload();
        });
    }

    // ============================================
    // Product image click handlers
    // ============================================
    jQuery('.shop-container .product-type-variable .box-image img, .shop-container .product-type-simple .box-image img').click(function (event) {
        if (jQuery('#progressBarData').length) {
            event.preventDefault();
            jQuery(this).closest('.box-image').children('.image-tools.hover-slide-in').children('a.quick-view-added').trigger('click');
        }
    });

    jQuery(document.body).on('click', '.title-wrapper p a', function (event) {
        if (jQuery('#progressBarData').length) {
            if (jQuery(this).closest('.box-text-products').closest('.product-small').children('.box-image').children('.image-tools.hover-slide-in').children('a.quick-view-added').length > 0) {
                event.preventDefault();
                jQuery(this).closest('.box-text-products').closest('.product-small').children('.box-image').children('.image-tools.hover-slide-in').children('a.quick-view-added').trigger('click');
            }
        }
    });

    // ============================================
    // Size guide helper function
    // ============================================
    function addSizeGuide($target) {
        if ($target.length > 0 && $target.find('.size_guide_bundle').length === 0) {
            $target.after('<span class="size_guide_bundle" title="Size Guide">First Time? See our <a class="various2" title="" href="#inline2"> SIZE GUIDE</a></span>');
        }
    }

    // Add size guide to bundled products
    if (jQuery(".bundled_product").length > 0) {
        jQuery('.bundled_product.bundled_product_summary.product').each(function () {
            var $this = jQuery(this);
            var $sizeLabel = $this.find('table tr[data-attribute_label]').filter(function () {
                return jQuery(this).data('attribute_label').toLowerCase() === 'size';
            }).find('td.value');
            addSizeGuide($sizeLabel);
        });
    }

    // ============================================
    // Single product page functionality
    // ============================================
    if (jQuery("body.single-product").length > 0) {
        // Bundle product image update on attribute change
        jQuery('.variations [id^=pa_colour_], .variations [id^=pa_style_], .variations [id^=pa_button-ups_]').on('change', function () {
            var attributePrefix = jQuery(this).attr('id').replace(/^pa_(.+?)(?:_\d+)?$/, 'attribute_pa_$1');
            var colorVal = jQuery(this).val();
            if (colorVal) {
                var allVariations = jQuery(this).parents('.variations_form').attr('data-product_variations');
                if (!allVariations) return;

                var variations = JSON.parse(allVariations);
                var imgSrc = '', srcSet = '';

                jQuery.each(variations, function (_, variation) {
                    var attributes = variation.attributes;
                    if (attributes && attributes[attributePrefix] === colorVal) {
                        if (variation.image) {
                            imgSrc = variation.image.full_src || '';
                            srcSet = variation.image.full_src || '';
                            return false;
                        }
                    }
                });

                if (imgSrc) {
                    var $productImage = jQuery(this).parents('.bundled_product').find('.bundled_product_image .wp-post-image');
                    var timeoutId = setTimeout(function () {
                        $productImage.attr('src', imgSrc);
                        $productImage.attr('srcset', srcSet);
                        $productImage.attr('data-large_image', srcSet);
                    }, 500);

                    // Store timeout ID for potential cleanup
                    jQuery(this).data('image-timeout', timeoutId);
                    jQuery(this).parents('.bundled_product').find('.image').attr('href', imgSrc);
                }
            }
        });

        // Add size guide to single product variations
        if (!jQuery(".bundled_product").length) {
            var $sizeLabel = jQuery('body.single-product table.variations');
            addSizeGuide($sizeLabel);
        }
    }

    // ============================================
    // Size chart / Fancybox initialization
    // ============================================
    jQuery(document).on('click', '.various2', function () {
        jQuery('.tabcontent').first().show();
    });

    var fancyboxTimeout = setTimeout(function () {
        jQuery('a.various2').fancybox({
            overlayShow: true,
            width: '300',
            height: '200',
            autoScale: false,
            transitionIn: 'none',
            transitionOut: 'none'
        });
    }, 2000);

    // Cleanup timeout on page unload
    jQuery(window).on('beforeunload', function () {
        if (fancyboxTimeout) {
            clearTimeout(fancyboxTimeout);
        }
    });



    jQuery('.tablink').click(function () {
        var tabClass = jQuery(this).attr('class').split(' ')[0];
        jQuery('.tabcontent').hide();
        jQuery('#' + tabClass).show();
    });
    // ============================================
    // Social icons positioning
    // ============================================
    if (jQuery('.footer-social-icons2').length && jQuery(".payment-icons.inline-block").length) {
        jQuery('.footer-social-icons2').prependTo(jQuery(".payment-icons.inline-block"));
    }

    // ============================================
    // Lightbox reset variation handler
    // ============================================
    jQuery('body').on('click', '.product-lightbox .reset_variations', function () {
        var productIdElement = jQuery(".product-lightbox .product").attr("id");
        if (productIdElement) {
            var splitId = productIdElement.split('-');
            if (splitId.length > 1) {
                var productId = splitId[1];
                jQuery(".product-lightbox input:radio, .archive .post-" + productId + " input:radio").removeAttr("checked");
            }
        }

        var featureIdElement = jQuery(".product-lightbox .product-quick-view-container .row").attr("id");
        if (featureIdElement) {
            jQuery('#pa_size option:selected').removeAttr("selected");
        }

        jQuery('.product-lightbox input[name="r"]').prop('checked', false);
    });

    // ============================================
    // Cart sidebar addon
    // ============================================
    jQuery("body").on('click', '.sidebar_addon_cart_variable span', function () {
        var $this = jQuery(this);
        $this.addClass('active').siblings().removeClass('active');
        var variationID = $this.attr('data-id');
        var cartURL = '?add-to-cart=' + variationID;
        var $addonModal = jQuery('a#addon_modal');
        if ($addonModal.length) {
            $addonModal.attr({
                'href': cartURL,
                'data-product_id': variationID
            });
        }
    });

    // ============================================
    // Cart quantity update
    // ============================================
    if (jQuery(".actions .hidemem").css('display') === 'none') {
        jQuery(document).on("click", ".minus, .plus", function () {
            jQuery("[name='update_cart']").trigger('click');
        });
    }


    // ============================================
    // Color change for sale links
    // ============================================
    var $saleLinks = jQuery('#shop-sidebar-cat li a, ul.header-nav-main li#menu-item-104207 ul.sub-menu li a');
    if ($saleLinks.length) {
        $saleLinks.each(function () {
            var linkText = jQuery(this).text().trim();
            if (linkText === "ON SALE" || linkText === "On Sale") {
                jQuery(this).css({
                    'color': '#d61a1a',
                    'font-weight': 'bold'
                });
            }
        });
    }

    // ============================================
    // YouTube player initialization
    // ============================================
    var $youtubePlayers = jQuery(".youtube-player");
    if ($youtubePlayers.length) {
        function labnolThumb(id) {
            var overlaySrc = $youtubePlayers.first().attr("overlay-src");
            return '<img src="' + overlaySrc + '"><div class="play"></div>';
        }

        function labnolIframe() {
            var iframe = jQuery("<iframe>", {
                src: "https://www.youtube.com/embed/" + jQuery(this).data('id') + "?autoplay=1&rel=0",
                frameborder: "0",
                allowfullscreen: "1",
                width: $youtubePlayers.first().attr("width"),
                height: $youtubePlayers.first().attr("height")
            });
            jQuery(this).parent().replaceWith(iframe);
        }

        $youtubePlayers.each(function () {
            var playerDiv = jQuery("<div/>", {
                "data-id": jQuery(this).data('id'),
                html: labnolThumb(jQuery(this).data('id'))
            }).click(labnolIframe);
            jQuery(this).append(playerDiv);
        });
    }



    // ============================================
    // Search field autocomplete
    // ============================================
    var $searchFields = jQuery('input[type="search"][class="search-field mb-0"]');
    if ($searchFields.length > 0) {
        $searchFields.attr('autocomplete', 'off');
    }

    // ============================================
    // MFP wrap tabindex removal
    // ============================================
    var mfpTimeout = setTimeout(function () {
        jQuery('.mfp-wrap').removeAttr("tabindex");
    }, 2000);

    // Cleanup timeout
    jQuery(window).on('beforeunload', function () {
        if (mfpTimeout) {
            clearTimeout(mfpTimeout);
        }
    });

    // ============================================
    // Dropdowns to bubbles conversion
    // ============================================
    window.dropdowns_to_bubbles_oncat = function (from) {
        var bodyClasses = jQuery("body").attr('class') || '';
        var isArchive = bodyClasses.indexOf('archive') !== -1;
        var isHome = bodyClasses.indexOf('home') !== -1;
        var isCheckout = bodyClasses.indexOf('page-template-page-checkout') !== -1;

        if (!isArchive && !isHome && !isCheckout) {
            return;
        }

        if (from === 'lightbox') {
            handleLightboxBubbles();
        } else {
            handleArchiveBubbles();
        }
    };

    function handleLightboxBubbles() {
        var globalctr = 999;
        var $container = jQuery(".product-quick-view-container .variations .value");
        $container.attr('id', "d" + globalctr);
        jQuery("<div></div>")
            .attr('id', "r" + globalctr)
            .attr('class', "r")
            .appendTo($container);

        var id = jQuery('.product-quick-view-container').children().attr('id');
        if (!id) return;

        var arr = id.split("-");
        var productId = arr[1];
        if (!productId) return;

        var $productContainer = jQuery(".product-quick-view-container #product-" + productId + " #d" + globalctr + " #pa_size");
        var productVariations = $productContainer.parents('.variations_form').attr('data-product_variations');
        if (!productVariations) return;

        var allVariations = JSON.parse(productVariations);
        $productContainer.find('option').each(function () {
            var size = jQuery(this).val();
            var addOption = checkVariationSize(allVariations, size);

            if (addOption) {
                createSizeRadio(jQuery(this), globalctr, productId, true);
            }
        });

        wrapRadioInputs("#r" + globalctr);
        setupTermSizeChange('#product-' + productId, true);
    }

    function handleArchiveBubbles() {
        var globalctr = 1;
        jQuery('.value').each(function () {
            if (jQuery(this).closest("body.single-product .product-info").length) {
                return;
            }

            jQuery(this).attr('id', "d" + globalctr);
            jQuery("<div></div>")
                .attr('id', "r" + globalctr)
                .attr('class', "r")
                .appendTo(jQuery(this).parent());

            jQuery("#d" + globalctr + " #pa_size option").each(function () {
                createSizeRadio(jQuery(this), globalctr, null, false);
            });

            wrapRadioInputs("#r" + globalctr);
            globalctr++;
        });

        jQuery(".r label:nth-child(1)").remove();
        setupTermSizeChange(null, false);
    }

    function checkVariationSize(allVariations, size) {
        var found = false;
        jQuery.each(allVariations, function (key, variation) {
            if (variation.attributes && variation.attributes.attribute_pa_size === size) {
                found = true;
                return false;
            }
        });
        return found;
    }

    function createSizeRadio($option, ctr, productId, isLightbox) {
        var $radio = jQuery("<input type='radio' name='r' /><span></span>")
            .attr("value", $option.val())
            .attr("data-id", ctr)
            .html('<p>' + $option.html() + '</p>')
            .click(function () {
                var val = jQuery(this).val();
                var selector = "#d" + ctr + ">#pa_size";

                if (isLightbox && productId) {
                    jQuery(".product-quick-view-container " + selector).val(val).trigger("change");
                    jQuery(".post-" + productId + " .quick-view").attr('data-size', val);
                    jQuery(".post-" + productId + " #pa_size").val(val).trigger("change");
                    jQuery('.post-' + productId + ' .r input[value="' + val + '"]').prop("checked", true);
                } else {
                    if (val !== '') {
                        var $colInner = jQuery(this).parents('.col-inner');
                        var link = $colInner.find('.attachment-woocommerce_thumbnail').parent().attr('href');
                        $colInner.find('.attachment-woocommerce_thumbnail').parent().attr('href', link + '?attribute_pa_size=' + val);
                        $colInner.find('.quick-view').attr('data-size', val);
                    }
                    jQuery(selector).val(val).trigger("change");
                    jQuery('.r label input').prop('checked', false);
                    jQuery(this).prop('checked', true);
                }
            });

        var targetSelector = isLightbox ? ".product-quick-view-container #r" + ctr : "#r" + ctr;
        $radio.appendTo(jQuery(targetSelector)).parent();
    }

    function wrapRadioInputs(selector) {
        jQuery(selector + " input").each(function () {
            jQuery(this).next('span').addBack().wrapAll('<label style="display:inline-block!important;"></label>');
        });
    }

    function setupTermSizeChange(productSelector, isLightbox) {
        var selector = isLightbox ? ".product-quick-view-container #term_pa_size" : "#term_pa_size";
        jQuery(selector).off('change').on('change', function () {
            var e = document.getElementById("term_pa_size");
            if (e && e.options[e.selectedIndex]) {
                var strUser = e.options[e.selectedIndex].value;
                var inputSelector = isLightbox ? '.product-quick-view-container input[value="' + strUser + '"]' : 'input[value="' + strUser + '"]';
                jQuery(inputSelector).prop("checked", true);
            }
        });
    }





    // ============================================
    // Close modals on add to cart
    // ============================================
    jQuery(document.body).on('added_to_cart', function () {
        jQuery('.mfp-close').click();
        jQuery.fancybox.close();

    });


    // ============================================
    // Max quantity check function
    // ============================================
    function check_max_qty(productId) {
        var isSingleProduct = jQuery('body').hasClass('single-product');

        if (isSingleProduct) {
            var attrData = jQuery('.variations_form').attr('data-product_variations');
            if (!attrData) return;

            var formArray = JSON.parse(attrData);
            jQuery.each(formArray, function (key, value) {
                var max_quan = value['max_qty'];
                if (max_quan === 1) {
                    checkProductInCart(productId, true);
                }
            });
        } else {
            var maxVal = jQuery('.product-quick-view-container').find('.mxq').val();
            if (maxVal === '1') {
                checkProductInCart(productId, false);
            }
        }
    }

    function checkProductInCart(productId, isSingle) {
        var found = false;
        jQuery(".xoo-wsc-content .xoo-wsc-product").each(function () {
            var aclass = jQuery(this).attr('class');
            var clsArray = aclass.split(" ");
            var pClass = clsArray[0];
            var pArray = pClass.split('-product-');
            var pId = pArray[1];

            if (pId === productId) {
                found = true;
                if (isSingle) {
                    jQuery('.max_error').remove();
                    jQuery('.woocommerce-variation-add-to-cart').append('<p class="max_error" style="color:red;">Max 1 item allowed of this product</p>');
                    jQuery('.variations_form .woocommerce-variation-add-to-cart .button').removeClass('loading');
                } else {
                    jQuery('.perror').html('Max 1 item allowed of this product');
                    jQuery('.product-quick-view-container .single_add_to_cart_button').removeClass('loading');
                }
                return false;
            }
        });
        return found;
    }

    jQuery('#woocommerce-product-search-field-1').attr('required', 'required');
    jQuery('#woocommerce-product-search-field-0').attr('required', 'required');


})



document.querySelectorAll(".bundle-option").forEach(option => {
    option.addEventListener("click", function () {
        document.querySelectorAll(".bundle-option").forEach(opt => {
            opt.classList.remove("active");
            opt.querySelector(".bundle-radio").classList.remove("active");
        });

        this.classList.add("active");
        this.querySelector(".bundle-radio").classList.add("active");

        // Update WooCommerce quantity input based on selected option's Qty
        const text = this.querySelector('.bundle-text').textContent;
        const qtyMatch = text.match(/Qty:\s*(\d+)/);
        const qty = qtyMatch ? parseInt(qtyMatch[1]) : 1;
        const qtyInput = document.querySelector('input.qty, input[name="quantity"]');
        if (qtyInput) {
            qtyInput.value = qty;
            qtyInput.dispatchEvent(new Event('change', { bubbles: true })); // Trigger WC update
        }
    });
});

jQuery(document).ready(function ($) {
    // Quantity controls - sync with main product quantity
    jQuery('.qty-minus').on('click', function () {
        var $input = jQuery(this).siblings('.qty-input');
        var currentVal = parseInt($input.val());
        if (currentVal > 1) {
            $input.val(currentVal - 1);
            // Sync with main product quantity input
            jQuery('input.qty, input[name="quantity"]').val(currentVal - 1).trigger('change');
        }
    });

    jQuery('.qty-plus').on('click', function () {
        var $input = jQuery(this).siblings('.qty-input');
        var currentVal = parseInt($input.val());
        var maxVal = parseInt($input.attr('max')) || 999;
        if (currentVal < maxVal) {
            $input.val(currentVal + 1);
            // Sync with main product quantity input
            jQuery('input.qty, input[name="quantity"]').val(currentVal + 1).trigger('change');
        }
    });

    // Also sync when floating quantity input changes directly
    jQuery('.qty-input').on('change', function () {
        var newVal = jQuery(this).val();
        jQuery('input.qty, input[name="quantity"]').val(newVal).trigger('change');
    });

    // Add to cart functionality
    jQuery('.floating-add-to-cart[data-product-id]').on('click', function (e) {
        var $button = jQuery(this);
        var productId = $button.data('product-id');
        var quantity = $('.qty-input').val() || 1;

        // Check if this is a variable product and no variation is selected
        if ($('.single_variation_wrap').length > 0 && $('.single_variation_wrap').is(':visible')) {
            var variationId = $('input[name="variation_id"]').val();
            if (!variationId || variationId === '0') {
                // Show error message
                showSizeSelectionError();

                // Scroll to variation selection area
                jQuery('html, body').animate({
                    scrollTop: jQuery('.variations').offset().top - 100
                }, 500);

                // Add a temporary highlight effect
                jQuery('.variations').addClass('highlight-variations');
                setTimeout(function () {
                    jQuery('.variations').removeClass('highlight-variations');
                }, 2000);

                return false; // Prevent form submission
            }
        }

        $button.addClass('loading').text('Adding...');

        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: 'POST',
            data: {
                action: 'woocommerce_add_to_cart',
                product_id: productId,
                quantity: quantity
            },
            success: function (response) {
                if (response.error) {
                    alert(response.error);
                    $button.removeClass('loading').text('Add to Cart');
                } else {
                    // Update cart fragments
                    jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                    $button.removeClass('loading').text('Added to Cart');

                    // Keep floating cart visible for continued shopping
                }
            },
            error: function () {
                alert('Error adding to cart. Please try again.');
                $button.removeClass('loading').text('Add to Cart');
            }
        });
    });

    // Keep floating cart always visible (no scroll hide behavior)

    // Hide floating cart for bundle products
    if (jQuery('.bundled_product').length > 0) {
        jQuery('#p2c-floating-cart').hide();
    }
});

// Function to show size selection error
// Function to show size selection error
function showSizeSelectionError() {
    // Scroll to variations
    var target = document.querySelector('#picker_pa_size') || document.querySelector('table.variations');
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        // Add shake animation effect
        var $target = jQuery(target).closest('table.variations');
        if ($target.length) {
            $target.css({ position: 'relative' });
            for (var i = 0; i < 3; i++) {
                $target.animate({ left: -10 }, 50)
                    .animate({ left: 10 }, 50)
                    .animate({ left: 0 }, 50);
            }

            // Also highlight briefly
            $target.css('transition', 'box-shadow 0.3s ease')
                .css('box-shadow', '0 0 10px rgba(255,0,0,0.5)');

            setTimeout(function () {
                $target.css('box-shadow', 'none');
            }, 1000);
        }
    }
}

// ============================================
// Floating cart behaviors for variable and bundle products
// ============================================

// Handle swatch clicks for variable products (general, not just for specific categories)
jQuery(document).on('click', '.swatch-wrapper', function (e) {
    e.preventDefault();
    var $swatch = jQuery(this);
    var value = $swatch.data('value');
    var attribute = $swatch.closest('.select').find('select').attr('name');

    if (value && attribute) {
        // Update the select element
        var $select = $swatch.closest('.select').find('select');
        $select.val(value).trigger('change');

        // Update visual selection
        $swatch.closest('.select').find('.swatch-wrapper').removeClass('selected');
        $swatch.addClass('selected');
    }
});

// Handle "Select Options" click for variable products
jQuery(document).on('click.test', '.floating-select-options', function (e) {
    e.preventDefault();
    console.log('✅ Floating button clicked (test)');

    jQuery('#p2c-floating-cart .size-error-message').remove();

    jQuery('#p2c-floating-cart .floating-cart-content').prepend(
        '<div class="size-error-message" style="color:red;font-size:14px;text-align:center;margin-bottom:10px;">Please select a size</div>'
    );

    var target = document.querySelector('#picker_pa_size') || document.querySelector('table.variations');

    target?.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
});

// Handle "Build Your Bundle" click for bundle products
jQuery('.floating-build-bundle').on('click', function (e) {
    e.preventDefault();
    // Scroll to bundle summary - look for bundle summary section
    var $bundleSummary = jQuery('.bundle-summary, .bundled_product_summary, .bundle_container');
    if ($bundleSummary.length) {
        jQuery('html, body').animate({
            scrollTop: $bundleSummary.offset().top - 100
        }, 500);
    } else {
        // Fallback: scroll to bundled products
        var $bundledProducts = jQuery('.bundled_product');
        if ($bundledProducts.length) {
            jQuery('html, body').animate({
                scrollTop: $bundledProducts.first().offset().top - 100
            }, 500);
        }
    }
});

// Listen for variation changes on variable products
jQuery(document).on('found_variation', function (event, variation) {
    console.log('✅ Variation found - removing error message:', variation);
    if (variation && variation.is_purchasable) {
        // Remove error message when variation is selected
        var $errorMessages = jQuery('#p2c-floating-cart .size-error-message');
        console.log('Found error messages to remove:', $errorMessages.length);
        $errorMessages.remove();
        // Adjust layout to prevent wrapping after error removal
        jQuery('#p2c-floating-cart .floating-cart-content').css('flex-wrap', 'nowrap');
        console.log('✅ Error message removed from floating cart');

        // Get selected size
        var selectedSize = '';
        jQuery('.variations select').each(function () {
            var attrName = jQuery(this).data('attribute_name') || jQuery(this).attr('name');
            console.log('Checking attribute:', attrName);
            if (attrName && attrName.toLowerCase().includes('size')) {
                selectedSize = jQuery(this).find('option:selected').text();
                console.log('Selected size:', selectedSize);
            }
        });

        // Update floating cart
        var $floatingCart = jQuery('#p2c-floating-cart');
        var $priceElement = $floatingCart.find('.floating-price');
        var $selectedSize = $priceElement.find('.selected-size');

        // Show selected size
        if (selectedSize && selectedSize !== 'Choose an option') {
            $selectedSize.text(' (' + selectedSize + ')').show();
            console.log('✅ Selected size displayed:', selectedSize);
        } else {
            $selectedSize.hide();
        }

        // Change button to "Add to Cart"
        var $buttonContainer = $floatingCart.find('.floating-cart-content');
        if ($buttonContainer.find('.floating-select-options').length) {
            var productId = jQuery('input[name="add-to-cart"]').val();
            console.log('✅ Changing button to Add to Cart for product:', productId);
            $buttonContainer.find('.floating-select-options').replaceWith('<button type="button" class="floating-add-to-cart-variable" data-product-id="' + productId + '">Add to Cart</button>');
        }
    }
});

// Handle reset variations
jQuery(document).on('reset_data', function () {
    var $floatingCart = jQuery('#p2c-floating-cart');
    var $priceElement = $floatingCart.find('.floating-price');
    var $selectedSize = $priceElement.find('.selected-size');

    // Hide selected size
    $selectedSize.hide();

    // Change back to "Select Options"
    var $buttonContainer = $floatingCart.find('.floating-cart-content');
    if ($buttonContainer.find('.floating-add-to-cart-variable').length) {
        $buttonContainer.find('.floating-add-to-cart-variable').replaceWith('<a href="#" class="floating-select-options">Select Options</a>');
    }
});

// Handle add to cart for variable products from floating button
jQuery(document).on('click', '.floating-add-to-cart-variable', function (e) {
    e.preventDefault();

    var $button = jQuery(this);

    // Double-check variation is selected
    var variationId = jQuery('input[name="variation_id"]').val();
    if (!variationId || variationId === '0') {
        showSizeSelectionError();
        return false;
    }

    // Add loading effect
    $button.addClass('loading').text('Adding...');

    // Sync quantity
    var floatingQty = jQuery('.qty-input').val();
    if (floatingQty) {
        jQuery('input.qty, input[name="quantity"]').val(floatingQty).trigger('change');
    }

    // Trigger the main button
    jQuery('.single_add_to_cart_button').trigger('click');

    // Safety timeout to reset button if nothing happens (e.g. validation error)
    setTimeout(function () {
        if ($button.hasClass('loading')) {
            $button.removeClass('loading').text('Add to Cart');
        }
    }, 5000);
});

// Reset floating button on cart update
jQuery(document.body).on('added_to_cart', function () {
    var $button = jQuery('.floating-add-to-cart-variable');
    if ($button.length) {
        $button.removeClass('loading').text('Added');
        setTimeout(function () {
            $button.text('Add to Cart');
        }, 2000);
    }
});

// // Listen for bundle completion (when bundle options are selected)
// // This assumes bundle products update a quantity or have some completion indicator
// $(document).on('change', '.bundle-option input, .bundled_product input.qty', function() {
//     setTimeout(function() {
//         var $floatingCart = $('#p2c-floating-cart');
//         var $buildBundleBtn = $floatingCart.find('.floating-build-bundle');

//         if ($buildBundleBtn.length) {
//             // Check if bundle is complete (all required items selected)
//             var bundleComplete = true;
//             $('.bundled_product').each(function() {
//                 var $product = $(this);
//                 var $qtyInput = $product.find('input.qty');
//                 if ($qtyInput.length && parseInt($qtyInput.val()) === 0) {
//                     bundleComplete = false;
//                     return false;
//                 }
//             });

//             if (bundleComplete) {
//                 // Change to "Add to Cart"
//                 $buildBundleBtn.replaceWith('<button type="button" class="floating-add-to-cart-bundle" data-product-id="' + $('.bundle_form input[name="add-to-cart"]').val() + '">Add to Cart</button>');
//             } else if ($floatingCart.find('.floating-add-to-cart-bundle').length) {
//                 // Change back if not complete
//                 $floatingCart.find('.floating-add-to-cart-bundle').replaceWith('<a href="#bundle-summary" class="floating-build-bundle">Build Your Bundle</a>');
//             }
//         }
//     }, 100);


// ============================================
// Floating cart size error message removal
// ============================================
(function () {
    const msg = document.querySelector('#p2c-floating-cart .size-error-message');
    if (!msg) return;

    // Remove message when animation finishes
    msg.addEventListener('animationend', () => {
        msg.style.display = 'none';
    });

    // Safety fallback (prevents stuck message)
    setTimeout(() => {
        msg.style.display = 'none';
    }, 2600);
})();


jQuery(function ($) {

    $(document).on('click', '.single_add_to_cart_button', function (e) {

        const $form = $(this).closest('form.variations_form');
        if (!$form.length) return;

        let valid = true;

        $form.find('.variations select').each(function () {
            if (!$(this).val()) {
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const $variations = $form.find('.variations');

            // Create message once
            if (!$form.find('.variation-error-message').length) {
                $('<div class="variation-error-message">Please select a size to continue</div>')
                    .insertBefore($variations)
                    .slideDown(200);
            }

            // Scroll to variations
            $('html, body').animate({
                scrollTop: $variations.offset().top - 120
            }, 500);

            // Highlight variations
            $variations.addClass('variation-attention');
            setTimeout(() => {
                $variations.removeClass('variation-attention');
            }, 800);

            return false;
        }
    });

    // Remove error once variation selected
    $(document).on('change', '.variations select', function () {
        $(this).closest('form.variations_form')
            .find('.variation-error-message')
            .slideUp(200, function () {
                $(this).remove();
            });
    });

});

// ============================================
// 5-for-99 Category Cart Opening Logic
// ============================================

(function($) {
    'use strict';

    // Function to check if product is from 5-for-99 category
    function isProductFromFiveForNinetyNine(productId) {
        var isFiveForNinetyNine = false;

        // Check if we're on the 5-for-99 category page
        if ($('body').hasClass('term-5-for-99')) {
            isFiveForNinetyNine = true;
            console.log('5-for-99 check: On category page, product is 5-for-99');
        } else {
            // AJAX call to check product category
            console.log('5-for-99 check: Not on category page, checking via AJAX for product:', productId);
            $.ajax({
                url: ins_params.ajax_url,
                type: 'POST',
                async: false, // Synchronous for immediate check
                data: {
                    action: 'p2c_check_product_category',
                    product_id: productId,
                    category_slug: '5-for-99'
                },
                success: function(response) {
                    console.log('5-for-99 check AJAX response:', response);
                    if (response.success && response.data.is_in_category) {
                        isFiveForNinetyNine = true;
                        console.log('5-for-99 check: Product is in category');
                    } else {
                        console.log('5-for-99 check: Product is NOT in category');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('5-for-99 check AJAX error:', error);
                }
            });
        }

        console.log('5-for-99 check result for product', productId, ':', isFiveForNinetyNine);
        return isFiveForNinetyNine;
    }

    // Function to count products from 5-for-99 category in cart
    function countFiveForNinetyNineItemsInCart() {
        var count = 0;

        $.ajax({
            url: ins_params.ajax_url,
            type: 'POST',
            async: false, // Synchronous for immediate check
            data: {
                action: 'p2c_count_category_items_in_cart',
                category_slug: '5-for-99'
            },
            success: function(response) {
                if (response.success) {
                    count = response.data.count;
                }
            }
        });

        return count;
    }

    // Override the auto_open_toggle variable dynamically
    var originalAutoOpenToggle = auto_open_toggle;

    // Function to check if we should open cart for current product
    function shouldOpenCartForCurrentProduct() {
        // Get the last added product from WooCommerce fragments or button context
        var lastAddedProductId = 0;
        var lastAddedQuantity = 1;

        // Try to get from WooCommerce last added to cart data
        if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.last_added_to_cart) {
            // This might contain the last added product info
            console.log('Last added to cart:', wc_add_to_cart_params.last_added_to_cart);
        }

        // Check if we're on a product page and get the product ID
        if ($('body').hasClass('single-product')) {
            lastAddedProductId = $('input[name="product_id"]').val() || $('input[name="add-to-cart"]').val() || $('.single_add_to_cart_button').val();
            lastAddedQuantity = $('input[name="quantity"]').val() || 1;
        }

        console.log('Checking cart open for product:', lastAddedProductId, 'quantity:', lastAddedQuantity);

        if (lastAddedProductId && isProductFromFiveForNinetyNine(lastAddedProductId)) {
            var currentCount = countFiveForNinetyNineItemsInCart();
            var newCount = currentCount + parseInt(lastAddedQuantity);
            var shouldOpen = (newCount >= 5);
            console.log('5-for-99 logic: current=' + currentCount + ', adding=' + lastAddedQuantity + ', new=' + newCount + ', shouldOpen=' + shouldOpen);
            return shouldOpen;
        }

        // For non-5-for-99 products, use default behavior
        return originalAutoOpenToggle;
    }

    // Override the success callback of Instantio's AJAX cart reload
    var originalAjax = $.ajax;
    $.ajax = function(settings) {
        if (settings.data && typeof settings.data === 'string' && settings.data.indexOf('ins_ajax_cart_reload') !== -1) {
            var originalSuccess = settings.success;
            settings.success = function(response) {
                // Modify auto_open_toggle before calling original success
                var shouldOpen = shouldOpenCartForCurrentProduct();
                auto_open_toggle = shouldOpen && originalAutoOpenToggle;
                console.log('Setting auto_open_toggle to:', auto_open_toggle);

                // Call original success
                if (originalSuccess) {
                    originalSuccess.call(this, response);
                }

                // Reset after a short delay
                setTimeout(function() {
                    auto_open_toggle = originalAutoOpenToggle;
                }, 100);
            };
        }
        return originalAjax.call(this, settings);
    };

    // Override single page add to cart
    $(document).on("click", ".single_add_to_cart_button", function(e) {
        if (disable_ajax_add_cart == true) {
            return;
        }

        e.preventDefault();
        var thisbutton = $(this),
            cart_form = thisbutton.closest("form.cart"),
            id = thisbutton.val(),
            product_id = cart_form.find("input[name=product_id]").val() || id,
            product_qty = cart_form.find("input[name=quantity]").val() || 1,
            variation_id = cart_form.find("input[name=variation_id]").val() || 0;

        if (cart_form.find("input[name=variation_id]").length > 0) {
            if (variation_id == '' || variation_id == 0) {
                return;
            }
        }

        // Check if product is from 5-for-99 category
        var isFiveForNinetyNineProduct = isProductFromFiveForNinetyNine(product_id);
        var shouldOpenCart = true;

        if (isFiveForNinetyNineProduct) {
            // Count current items (before adding)
            var currentCount = countFiveForNinetyNineItemsInCart();
            // Add 1 for the item being added
            var newCount = currentCount + parseInt(product_qty);
            // Only open if new count >= 5
            shouldOpenCart = (newCount >= 5);
        }

        $.ajax({
            url: ins_params.ajax_url,
            type: "POST",
            data: {
                action: "ins_ajax_cart_single",
                product_id: product_id,
                quantity: product_qty,
                variation_id: variation_id,
            },
            beforeSend: function (response) {
                thisbutton.removeClass("added").addClass("loading");
            },
            complete: function (response) {
                ins_cart_icon_animation();
                thisbutton.addClass("added").removeClass("loading");
            },
            success: function (response) {
                $(".ins-quick-view").hide();
                $("#ins_cart_totals").html(response.data.ins_cart_count);
                $("#ins_cart_mobile_totals").html(response.data.ins_cart_count);
                $(".ins-checkout-layout .ins-content").removeClass("hide");
                $(".ins-single-layout-wrap .ins_single_layout_checkout_area").removeClass("hide");
                $(".ins-checkout-layout .ins-content").addClass("ins-show");
                $(".ins-checkout-layout .ins-cart-empty").addClass("hide");
                $(".ins-checkout-layout .ins-cart-inner.step-1").html("");
                $(".ins-checkout-layout .ins-cart-inner.step-1").append(response.data.data);

                ins_owl_carousel();

                if (shouldOpenCart && auto_open_toggle == true) {
                    $(".ins-checkout-layout-3").addClass("active");
                    $(".ins-checkout-overlay").addClass("active");
                    $(".ins-checkout-popup").addClass("active");
                    $(".ins-checkout-popup").addClass("fadeIn");
                }

                $(".ins-quick-view").hide();
                $(".loader-container").addClass("active");

                setTimeout(function () {
                    $(".loader-container").removeClass("active");
                    $('.ins-single-step').removeClass('done');
                    $('.ins-single-step').removeClass('active');
                    $('.ins-single-step.step-1').addClass('done');
                    $('.ins-single-step.step-1').addClass('active');
                    $('.ins-content').find('.ins-cart-inner').hide();
                    $('.ins-content').find('.ins-cart-inner').removeClass('active');
                    $('.ins-content').find('.step-1').show();
                    $('.ins-content').find('.step-1').addClass('active');
                }, 1000);

                single_step_order_review_callback();
                $('.ins-checkout-layout button[name="update_cart"]').trigger("click");
            },
        });
    });

})(jQuery);
