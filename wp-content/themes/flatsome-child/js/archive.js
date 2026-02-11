jQuery(document).ready(function ($) {
    'use strict';

    if (!jQuery("body").hasClass("archive")) {
        return;
    }

    // ============================================
    // Size and Style Filter Toggle
    // ============================================
    jQuery('#toggleHelp').on('click', function (event) {
        event.preventDefault();
        var $sizeDiv = jQuery('#visual_term_pa_size');
        $sizeDiv.css('display', $sizeDiv.css('display') === 'none' || $sizeDiv.css('display') === '' ? 'flex' : 'none');
    });

    // ============================================
    // Size Selection Handler
    // ============================================
    jQuery('#visual_term_pa_size #termmm_size').on('click', '.size-option', function () {
        var selectedSize = jQuery(this).attr('data-value');

        jQuery('#visual_term_pa_size #termmm_size .size-option').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('#term_pa_size').val(selectedSize).trigger('change');

        // Re-apply style filter after size filter if style is selected
        var $selectedStyle = jQuery('#termmm_style .size-option.selected');
        if ($selectedStyle.length && $selectedStyle.attr('data-cat') !== '0') {
            window.filterProductsByStyle($selectedStyle.attr('data-cat'));
        }
    });

    // ============================================
    // Style Selection Handler
    // ============================================
    jQuery('#visual_term_pa_size #termmm_style').on('click', '.size-option', function () {
        var selectedStyle = jQuery(this).attr('data-cat');
        var $this = jQuery(this);

        jQuery('#termmm_style .size-option').removeClass('selected');
        $this.addClass('selected');

        if (selectedStyle === '0') {
            resetOptions();
        } else {
            window.filterProductsByStyle(selectedStyle);
        }

        // Re-apply size filter after style if size is selected
        var selectedSize = jQuery('#term_pa_size').val();
        if (selectedSize && selectedSize !== 'all' && selectedSize !== '') {
            jQuery('#term_pa_size').trigger('change');
        }
    });

    // ============================================
    // Shop Page Top Filter - Size Change Handler
    // ============================================
    jQuery(document.body).on('change', '#term_pa_size', function () {
        var term_pa_size_slug = jQuery(this).val();

        // Show all if "all" selected
        if (term_pa_size_slug === 'all' || term_pa_size_slug === '') {
            jQuery('.products .product').show();
            return;
        }

        // Loop through each product and check variation data for size
        jQuery('.products .product').each(function () {
            var $product = jQuery(this);
            var $select = $product.find('select[name="attribute_pa_size"]');
            var variations = $product.find('.variations_form').data('product_variations');
            var hasSize = false;

            // Check variation data for available in-stock sizes
            if (variations && Array.isArray(variations)) {
                jQuery.each(variations, function (index, variation) {
                    // Skip out of stock variations
                    if (variation.manage_stock && parseInt(variation.stock_quantity) <= 0) {
                        return true; // continue
                    }
                    var sizeValue = variation.attributes.attribute_pa_size;
                    if (sizeValue && sizeValue.toString() === term_pa_size_slug) {
                        hasSize = true;
                        return false; // break
                    }
                });
            }

            if (hasSize) {
                $product.show();
                if ($select.length) {
                    $select.val(term_pa_size_slug).trigger('change');
                }
                $product.find('.quick-view-added').attr('data-size', term_pa_size_slug);
            } else {
                $product.hide();
            }
        });

        // Auto-fetch more if no visible products
        setTimeout(function() {
            if (typeof window.autoFetchIfNoResults === 'function') {
                window.autoFetchIfNoResults();
            }
        }, 100);
    });

    // ============================================
    // Filter Products by Style (Global for pagination)
    // ============================================
    window.filterProductsByStyle = function(selectedStyle) {
        jQuery('.product').each(function () {
            var $product = jQuery(this);
            var categoryIds = $product.attr('data-category-ids');
            var productCategoryIds = categoryIds ? categoryIds.split(',') : [];
            var styleMatches = (selectedStyle === 'all' || productCategoryIds.indexOf(selectedStyle) !== -1);
            
            $product.toggle(styleMatches);
        });
    };

    // ============================================
    // Auto-fetch Products When No Results
    // ============================================
    window.allPagesLoaded = false; // Track if all pages exhausted

    window.autoFetchIfNoResults = function() {
        var visibleProducts = jQuery('.products .product:visible').length;
        var $infiniteScroll = jQuery('.shop-container .products');
        var infScroll = $infiniteScroll.data('infiniteScroll');
        
        // Check if filter is active
        var selectedSize = jQuery('#term_pa_size').val();
        var sizeFilterActive = selectedSize && selectedSize !== 'all' && selectedSize !== '';

        if (!sizeFilterActive) {
            jQuery('#filter-loading-msg, #no-products-msg').remove();
            return;
        }

        // Check if more pages available
        var hasMorePages = false;
        if (infScroll && !window.allPagesLoaded) {
            var lastPageReached = jQuery('.page-load-status .infinite-scroll-last').is(':visible') || 
                                  jQuery('.page-load-status').hasClass('infinite-scroll-last');
            hasMorePages = !lastPageReached;
        }
        
        // If no visible products
        if (visibleProducts === 0) {
            if (hasMorePages) {
                // Show loading and fetch more
                if (!jQuery('#filter-loading-msg').length) {
                    jQuery('.products').before('<div id="filter-loading-msg" style="text-align:center;padding:20px;font-weight:bold;">Fetching products with selected size...</div>');
                }
                $infiniteScroll.infiniteScroll('loadNextPage');
            } else {
                // All pages loaded, no products found - show message immediately
                jQuery('#filter-loading-msg').remove();
                if (!jQuery('#no-products-msg').length) {
                    jQuery('.products').before('<div id="no-products-msg" style="text-align:center;padding:30px 20px;"><p style="font-weight:bold;font-size:16px;margin-bottom:10px;">Sorry, no products available in this size right now.</p><p style="color:#666;">Try selecting a different size or check back later!</p></div>');
                }
            }
        } else {
            // Products found - remove all messages
            jQuery('#filter-loading-msg, #no-products-msg').remove();
        }
    };

    // Listen for last page event from infinite scroll
    jQuery('.shop-container .products').on('last.infiniteScroll', function() {
        window.allPagesLoaded = true;
    });

    // Remove messages when filter changes
    jQuery(document.body).on('change', '#term_pa_size', function() {
        jQuery('#no-products-msg').remove();
        jQuery('#filter-loading-msg').remove();
    });

    // Reset allPagesLoaded when "All" is selected
    jQuery('#visual_term_pa_size #termmm_size').on('click', '.size-option[data-value="all"]', function() {
        window.allPagesLoaded = false;
    });

    // ============================================
    // Reset Filter Options
    // ============================================
    function resetOptions() {
        jQuery('.product').show();
        jQuery('#termmm_style .size-option').removeClass('selected');
        jQuery('#termmm_style .size-option[data-cat="0"]').addClass('selected');
        // Re-apply size filter if still selected
        var selectedSize = jQuery('#term_pa_size').val();
        if (selectedSize && selectedSize !== 'all' && selectedSize !== '') {
            jQuery('#term_pa_size').trigger('change');
        }
    }

    // ============================================
    // Size Chart / Fancybox Initialization
    // ============================================
    var fancyboxTimeout = setTimeout(function () {
        jQuery('.tabcontent').first().show();
        jQuery('a#various2').fancybox({
            overlayShow: true,
            width: '300',
            height: '200',
            autoScale: false,
            transitionIn: 'none',
            transitionOut: 'none'
        });

        jQuery('.tablink').on('click', function () {
            var tabClass = jQuery(this).attr('class').split(' ')[0];
            jQuery('.tabcontent').hide();
            jQuery('#' + tabClass).show();
        });
    }, 500);

    // Cleanup timeout
    jQuery(window).on('beforeunload', function() {
        if (fancyboxTimeout) {
            clearTimeout(fancyboxTimeout);
        }
    });

    // ============================================
    // Variation Size Change Handler
    // ============================================
    jQuery('.variations #pa_size').on('change', function () {
        var sizeSelected = jQuery(this).val();
        var productId = jQuery(this).parents('.product-small').attr('product-id');
        var $quickView = jQuery('.quick-view-added[data-prod="' + productId + '"]');
        
        if ($quickView.length) {
            jQuery('.post-' + productId + ' .quick-view-added').attr('data-size', sizeSelected);
        }
    });

    // ============================================
    // Initialize Variation Forms on Archive Pages
    // ============================================
    function initVariationForms() {
        if (typeof jQuery.fn.wc_variation_form === 'undefined') return;
        
        jQuery('.variations_form').each(function() {
            var $form = jQuery(this);
            if (!$form.data('wc_variation_form') && $form.data('product_variations')) {
                $form.wc_variation_form();
            }
        });
    }

    // Wait for WooCommerce variation script
    var wcCheck = setInterval(function() {
        if (typeof jQuery.fn.wc_variation_form !== 'undefined') {
            clearInterval(wcCheck);
            setTimeout(initVariationForms, 100);
        }
    }, 100);
    setTimeout(function() { clearInterval(wcCheck); }, 5000);

    // ============================================
    // Product Size Swatch Selection Handler
    // ============================================
    jQuery(document).off('click', '.select-option').on('click', '.select-option', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var $swatch = jQuery(this);
        
        // Prevent duplicate execution
        if ($swatch.data('processing')) return;
        $swatch.data('processing', true);
        setTimeout(function() { $swatch.removeData('processing'); }, 500);
        
        var $product = $swatch.closest('.product');
        var $form = $product.find('.variations_form');
        var sizeValue = $swatch.data('value');
        
        if (!$form.length || !sizeValue) return;
        if (typeof jQuery.fn.wc_variation_form === 'undefined') return;
        
        // Check if swatch is disabled
        if ($swatch.hasClass('disabled') || $swatch.css('display') === 'none') return;
        
        // Initialize form if needed
        var needsInit = !$form.data('wc_variation_form') && $form.data('product_variations');
        if (needsInit) {
            $form.wc_variation_form();
        }
        
        // Find select dropdown
        var $select = $product.find('select[name="attribute_pa_size"]');
        if (!$select.length) return;
        
        sizeValue = sizeValue.toString();
        
        // Verify option exists in select dropdown
        var $option = $select.find('option[value="' + sizeValue + '"]');
        if (!$option.length || $option.hasClass('disabled')) return;
        
        // Verify size exists in variations
        var variations = $form.data('product_variations');
        if (variations && Array.isArray(variations)) {
            var sizeExists = false;
            jQuery.each(variations, function(i, variation) {
                if (variation.attributes && 
                    variation.attributes.attribute_pa_size && 
                    variation.attributes.attribute_pa_size.toString() === sizeValue) {
                    sizeExists = true;
                    return false;
                }
            });
            if (!sizeExists) return;
        }
        
        // Update UI
        $product.find('.select-option').removeClass('selected');
        $swatch.addClass('selected');
        
        // Update dropdown and trigger WooCommerce validation
        // Ensure variation form is ready before triggering
        var triggerChange = function() {
            $select.val(sizeValue);
            
            // Wait a moment for WooCommerce to process, then trigger change
            setTimeout(function() {
                $select.trigger('change');
            }, 50);
        };
        
        if (needsInit) {
            setTimeout(triggerChange, 200);
        } else {
            triggerChange();
        }
    });

    // ============================================
    // Hide Unavailable Sizes Function
    // ============================================
    window.hideUnavailableSizes = function() {
        jQuery('.products .product').each(function () {
            var $productContainer = jQuery(this);
            var $select = $productContainer.find('select[name="attribute_pa_size"]');
            var variations = $productContainer.find('.variations_form').data('product_variations');

            if ($select.length && variations) {
                var availableSizes = [];

                // Gather available sizes from variations
                jQuery.each(variations, function (index, variation) {
                    if (variation.manage_stock && parseInt(variation.stock_quantity) <= 0) {
                        return; // Skip if variation is out of stock
                    }
                    var sizeValue = variation.attributes.attribute_pa_size.toString();
                    if (sizeValue && availableSizes.indexOf(sizeValue) === -1) {
                        availableSizes.push(sizeValue);
                    }
                });

                // Hide unavailable sizes
                $productContainer.find('.select-option').each(function () {
                    var sizeValue = jQuery(this).data('value').toString();
                    if (availableSizes.indexOf(sizeValue) === -1) {
                        jQuery(this).hide();
                    } else {
                        jQuery(this).show();
                    }
                });
            }
        });
    };

    // Call the function initially if on archive page
    if (typeof window.hideUnavailableSizes === 'function') {
        window.hideUnavailableSizes();
    }

    // ============================================
    // UI Text Updates
    // ============================================
    jQuery(".single_add_to_cart_button").text("Add to cart");

    jQuery('.title-wrapper .product-title a').text(function (_, text) {
        return text.replace(/\(.*?\)/g, '');
    });

    // ============================================
    // Lightbox Add to Cart - Max Quantity Check
    // ============================================
    jQuery('body').on('click', '.product-lightbox-inner .single_add_to_cart_button', function (e) {
        var $lightbox = jQuery(this).parents('.product-lightbox-inner');
        var $mxqInput = $lightbox.find('.clightbox.mxq');
        
        if ($mxqInput.length > 0) {
            var mxq = $mxqInput.val();
            if (mxq === '1') {
                var productId = $lightbox.find("input[name*='product_id']").val();
                var found = false;
                
                jQuery('.woocommerce-mini-cart li').each(function () {
                    var mincID = jQuery(this).find("a").attr('data-product_id');
                    if (mincID === productId) {
                        jQuery('.perror').html('Max 1 item allowed of this product');
                        e.preventDefault();
                        e.stopPropagation();
                        found = true;
                        return false;
                    }
                });
                
                if (!found) {
                    $lightbox.find('.perror').text('');
                }
            } else {
                $lightbox.find('.perror').text('');
            }
        }
    });

    // ============================================
    // Category Specific Functionality (term-200)
    // ============================================
    if (jQuery("body").hasClass("term-200")) {
        // Product image click handler
        jQuery(document).on('click', '.shop-container .box-image img', function (event) {
            event.preventDefault();
            var $quickViewLink = jQuery(this).closest('.box-image').find('.image-tools a.quick-view-added');

            if ($quickViewLink.length) {
                $quickViewLink.trigger('click');
            }
        });

        // Title link click handler
        jQuery(document).on('click', '.title-wrapper p a', function (event) {
            var $quickViewLink = jQuery(this)
                .closest('.box-text-products')
                .closest('.product-small')
                .find('.box-image .image-tools.hover-slide-in a.quick-view-added');

            if ($quickViewLink.length > 0) {
                event.preventDefault();
                $quickViewLink.trigger('click');
            }
        });

        // Move term description
        var $catdes = jQuery(".term-description").detach();
        $catdes.insertAfter(".tax-product_cat ul.products");
        jQuery('.term-description').css('display', 'block');
    }
});
