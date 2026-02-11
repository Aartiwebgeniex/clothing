/* global flatsome_infinite_scroll, Packery, ga */

; (function ($) {
  $.fn.flatsomeInfiniteScroll = function () {
    return this.each(function () {
      var $container = jQuery('.shop-container .products')
      var paginationNext = '.woocommerce-pagination li a.next'

      if ($container.length === 0 ||
        jQuery(paginationNext).length === 0 ||
        $container.hasClass('ux-infinite-scroll-js-attached')) {
        return
      }

      var viewMoreButton = jQuery('button.view-more-button.products-archive')
      var byButton = flatsome_infinite_scroll.type === 'button'
      var isMasonry = flatsome_infinite_scroll.list_style === 'masonry'
      // Set packery instance as outlayer when masonry is set.
      var outlayer = isMasonry ? Packery.data($container[0]) : false

      var $infiScrollContainer = $container.infiniteScroll({
        path: paginationNext,
        append: '.shop-container .product',
        checkLastPage: true,
        status: '.page-load-status',
        hideNav: '.archive .woocommerce-pagination',
        button: '.view-more-button',
        history: flatsome_infinite_scroll.history,
        historyTitle: true,
        debug: false,
        outlayer: outlayer,
        scrollThreshold: parseInt(flatsome_infinite_scroll.scroll_threshold)
      })

      if (byButton) {
        viewMoreButton.removeClass('hidden')
        $infiScrollContainer.infiniteScroll('option', {
          scrollThreshold: false,
          loadOnScroll: false
        })
      }

      $infiScrollContainer.on('load.infiniteScroll', function (event, response, path) {
        flatsomeInfiniteScroll.attachBehaviors(response)
      })

      $infiScrollContainer.on('request.infiniteScroll', function (event, path) {
        if (byButton) viewMoreButton.addClass('loading')
      })

      $infiScrollContainer.on('append.infiniteScroll', function (event, response, path, items) {
        jQuery(document).trigger('flatsome-infiniteScroll-append', [response, path, items])
        if (byButton) viewMoreButton.removeClass('loading')

        // Fix Safari bug
        jQuery(items).find('img').each(function (index, img) {
          img.outerHTML = img.outerHTML
        })

        // Load fragments and init_handling_after_ajax for new items.
        jQuery(document).trigger('yith_wcwl_reload_fragments')
        jQuery(document).trigger('flatsome-equalize-box')

        Flatsome.attach('lazy-load-images', $container)
        flatsomeInfiniteScroll.animateNewItems(items)

        // CUSTOM START
        var selectedSize = jQuery('#term_pa_size').val();
        var $selectedStyle = jQuery('#termmm_style .size-option.selected');
        var sizeFilterActive = selectedSize && selectedSize !== 'all' && selectedSize !== '';
        var styleFilterActive = $selectedStyle.length && $selectedStyle.attr('data-cat') !== '0';

        // Hide new items immediately if any filter is active
        if (sizeFilterActive || styleFilterActive) {
          jQuery(items).hide();
        }

        setTimeout(function () {
          if (typeof window.hideUnavailableSizes === 'function') {
            window.hideUnavailableSizes();
          }
          // Re-apply filters only if active
          if (sizeFilterActive) {
            jQuery('#term_pa_size').trigger('change');
          }
          if (styleFilterActive) {
            if (typeof window.filterProductsByStyle === 'function') {
              window.filterProductsByStyle($selectedStyle.attr('data-cat'));
            }
          }
          // Continue auto-fetch if still no visible products
          setTimeout(function() {
            if (typeof window.autoFetchIfNoResults === 'function') {
              window.autoFetchIfNoResults();
            }
          }, 200);
        }, 1000);
        // CUSTOM END

        if (isMasonry) {
          setTimeout(function () {
            $infiScrollContainer.imagesLoaded(function () {
              $infiScrollContainer.packery('layout')
            })
          }, 500)
        }

        if (window.ga && ga.loaded && typeof ga === 'function') {
          var link = document.createElement('a')
          link.href = path
          ga('set', 'page', link.pathname)
          ga('send', 'pageview')
        }
      })

      var flatsomeInfiniteScroll = {
        attachBehaviors: function (response) {
          Flatsome.attach('quick-view', response)
          Flatsome.attach('tooltips', response)
          Flatsome.attach('add-qty', response)
          Flatsome.attach('wishlist', response)
        },
        animateNewItems: function (items) {
          if (isMasonry) return
          jQuery(items).hide().fadeIn(parseInt(flatsome_infinite_scroll.fade_in_duration))
        }
      }

      // Initialize completed.
      $container.addClass('ux-infinite-scroll-js-attached')
    })
  }

  // Doc ready.
  $(function () {
    $(document.body).flatsomeInfiniteScroll()

    $(document).on('facetwp-loaded yith-wcan-ajax-filtered experimental-flatsome-pjax-request-done', function () {
      $(document.body).flatsomeInfiniteScroll()
    })
  })
})(jQuery)
