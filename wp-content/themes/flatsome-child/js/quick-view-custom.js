/**
 * Custom Quick View Enhancement
 * Same logic as parent file, moved to child theme
 */
(function($) {
    'use strict';

    var selectedSize;
    var quickViewTimeout;

    // Capture size when quick-view button is clicked
    $(document).on('click', '.quick-view[data-size]', function() {
        selectedSize = $(this).attr('data-size');
    });

    // Apply size selection when lightbox content is loaded
    $(document).on('mfpAfterOpen', function() {
        if (!selectedSize) return;

        // Clear any existing timeout
        if (quickViewTimeout) {
            clearTimeout(quickViewTimeout);
        }

        quickViewTimeout = setTimeout(function() {
            $('.product-quick-view-container .variations .value .r label').each(function() {
                if ($(this).children().val() === selectedSize) {
                    $(this).children().prop('checked', true);
                    $('.product-quick-view-container #pa_size').val(selectedSize).trigger('change');
                }
            });

            $('.product-quick-view-container #pa_size').css('display', 'none');
        }, 300);
    });

    // Cleanup timeout on page unload
    $(window).on('beforeunload', function() {
        if (quickViewTimeout) {
            clearTimeout(quickViewTimeout);
        }
    });

})(jQuery);

