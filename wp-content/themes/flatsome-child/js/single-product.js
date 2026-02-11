jQuery(document).ready(function ($) {
    'use strict';

    // ============================================
    // Scroll to tabs if URL has 't' parameter
    // ============================================
    if (new URLSearchParams(window.location.search).has('t')) {
        var scrollTimeout = setTimeout(function () {
            jQuery('html, body').animate({
                scrollTop: jQuery(".woocommerce-tabs").offset().top + jQuery(".woocommerce-tabs").outerHeight(true)
            }, 1000);
        }, 2500);

        // Cleanup timeout
        jQuery(window).on('beforeunload', function() {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
        });
    }

    // ============================================
    // Move size guide after variation
    // ============================================
    jQuery(window).on('load', function () {
        if (jQuery(".product-type-bundle").length === 0) {
            var $sizeGuide = jQuery('#size_guide');
            if ($sizeGuide.length > 0) {
                var sizeGuide = $sizeGuide.detach();
                jQuery('.woocommerce-variation.single_variation').after(sizeGuide);
            }
        }
    });

    // ============================================
    // Variation show/hide handlers
    // ============================================
    jQuery('body').addClass('csingle');
    
    jQuery(".single_variation_wrap").on("show_variation", function (event, variation) {
        jQuery('#selectmsg').css('display', 'none');
    });

    jQuery('.reset_variations').on('click', function () {
        jQuery('#selectmsg').css('display', 'block');
    });

    // Event-driven approach for variation messages (replaces setInterval)
    jQuery(document).on('show_variation', '.single_variation_wrap', function (event, variation) {
        var message = jQuery('body.single-product .ajaxerrors p').text().trim();
        if (message === 'Please select size') {
            jQuery('.ajaxerrors').addClass('hidemsg');
            jQuery('.ajaxerrors p').css('display', 'none');
        }
    });

    jQuery(document).on('hide_variation', '.single_variation_wrap', function (event, variation) {
        var message = jQuery('body.single-product .ajaxerrors p').text().trim();
        if (message === 'Please select size') {
            jQuery('.ajaxerrors').removeClass('hidemsg');
            jQuery('.ajaxerrors p').css('display', 'block');
        }
    });

    // ============================================
    // Related products carousel
    // ============================================
    jQuery('.customrelated .flickity-slider').flickity({
        cellAlign: 'left',
        pageDots: false,
        contain: true
    });

    // ============================================
    // Swatch color display
    // ============================================
    jQuery('.swatch-wrapper a').on('click', function () {
        var attribute = jQuery(this).parent().attr('data-attribute');
        var colorAttributes = ['pa_colour', 'pa_style', 'pa_button-ups', 'pa_jacket', 'pa_tall_tank', 'pa_beanie'];
        
        if (colorAttributes.indexOf(attribute) !== -1) {
            var color = jQuery(this).attr('title');
            var $parentRow = jQuery(this).parents('tr.attribute_options');
            $parentRow.find('.ccolor').remove();
            $parentRow.find('td>label>abbr').after("<div class='ccolor'>" + color + "</div>");
        }
    });

    // ============================================
    // Size change event handler (using data attribute instead of global)
    // ============================================
    var pa_size_get = new URLSearchParams(window.location.search).get('pa_size');

    jQuery(document.body).on('mousedown', '#pa_size', function () {
        if (!jQuery(this).data('size-change-initiated')) {
            jQuery(this).data('size-change-initiated', true);
        }
    });

    jQuery(document.body).on('change', '#pa_size', function () {
        var $select = jQuery(this);
        if ($select.data('size-change-initiated') && !$select.data('size-change-processed')) {
            $select.data('size-change-processed', true);
            var size_value = $select.val();
            
            jQuery('select#pa_size').each(function () {
                var $thisSelect = jQuery(this);
                var hasOption = false;
                
                $thisSelect.find('option').each(function () {
                    if (this.value === size_value) {
                        hasOption = true;
                        return false;
                    }
                });
                
                if (hasOption) {
                    $thisSelect.val(size_value).trigger('change');
                }
            });
        }
    });

    if (pa_size_get) {
        jQuery('#pa_size').trigger('mousedown');
        jQuery('#pa_size').val(pa_size_get).trigger('change');
    } else {
        jQuery('select#pa_size').each(function () {
            jQuery(this).prop('selectedIndex', 0).trigger('change');
        });
        jQuery('select#pa_colour').each(function () {
            jQuery(this).prop('selectedIndex', 0).trigger('change');
        });
    }

    // ============================================
    // YouTube player initialization
    // ============================================
    function initYouTubePlayers() {
        var $youtubePlayers = jQuery(".youtube-player");
        if (!$youtubePlayers.length) {
            return;
        }

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
            }).on('click', labnolIframe);
            jQuery(this).append(playerDiv);
        });
    }

    // Initialize YouTube players when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initYouTubePlayers);
    } else {
        initYouTubePlayers();
    }

    // ============================================
    // Move related products and reviews
    // ============================================
    var moveContentTimeout = setTimeout(function () {
        jQuery('.related.related-products-wrapper').insertAfter(".single-product .tabbed-content");
        var reviews = jQuery(".trustspot-main-widget").detach();
        jQuery(reviews).insertAfter(".related.related-products-wrapper");
    }, 500);

    // Cleanup timeout
    jQuery(window).on('beforeunload', function() {
        if (moveContentTimeout) {
            clearTimeout(moveContentTimeout);
        }
    });

    // ============================================
    // Bundle product variations ID assignment
    // ============================================
    var qa = 1;
    jQuery(".bundle_form .bundled_product_summary").each(function () {
        jQuery(this).attr('id', 'variations' + qa);
        qa++;
    });

    // ============================================
    // Bundle swatch control handler
    // ============================================
    jQuery('.swatch-control .swatch-wrapper').on('click', function () {
        var $swatch = jQuery(this);
        var $parentBundle = $swatch.parents('.bundled_product');
        
        if ($parentBundle.attr('id') === 'variations1' && $swatch.attr('data-attribute') === 'pa_size') {
            jQuery('.swatch-wrapper').removeClass('selected');
            var sizeVal = $swatch.attr('data-value');
            
            jQuery('.swatch-control [id^=pa_size_]').val(sizeVal).trigger('change');
            
            jQuery('.bundle_form .bundled_product_summary .select-option').each(function () {
                var $option = jQuery(this);
                if ($option.attr('data-value') === sizeVal) {
                    if ($option.parents('.bundled_product').attr('id') !== 'variations1') {
                        $option.addClass('selected');
                    } else {
                        $option.parents('.bundled_product').removeAttr('id');
                    }
                }
            });
        }
    });

    // ============================================
    // Utility function to get URL parameters
    // ============================================
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // ============================================
    // Add pa_size to product links from email campaigns
    // ============================================
    var pa_size = getUrlParameter('pa_size');
    if (pa_size !== null) {
        var $productLinks = jQuery('.product-small .box-image .image-fade_in_back > a');
        if ($productLinks.length) {
            $productLinks.each(function () {
                var currentHref = jQuery(this).attr('href');
                var separator = currentHref.indexOf('?') !== -1 ? '&' : '?';
                jQuery(this).attr('href', currentHref + separator + 'pa_size=' + pa_size);
            });
        }
    }

    // ============================================
    // Set size from URL parameter attribute_pa_size
    // ============================================
    if (jQuery('body').hasClass('single-product')) {
        var selSize = getUrlParameter('attribute_pa_size');
        if (selSize !== null && selSize !== '') {
            var sizeSelectTimeout = setTimeout(function () {
                var $sizePicker = jQuery('#picker_pa_size #pa_size');
                if ($sizePicker.length) {
                    $sizePicker.val(selSize).trigger('change');
                }
            }, 3000);

            // Cleanup timeout
            jQuery(window).on('beforeunload', function() {
                if (sizeSelectTimeout) {
                    clearTimeout(sizeSelectTimeout);
                }
            });
        }
    }
});



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