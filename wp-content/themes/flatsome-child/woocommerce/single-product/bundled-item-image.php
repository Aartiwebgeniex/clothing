<?php
/**
 * Bundled Product Image template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-image.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.21.0
 */


// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>
<div class="<?php echo esc_attr(implode(' ', $gallery_classes)); ?>">

	<?php

	/* CUSTOM START */

	//$feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
	$feat_image = wp_get_attachment_url(get_post_thumbnail_id($product_id));
	$feat_image;
	if (empty($feat_image)) {
		/* CUSTOM END */

		if (has_post_thumbnail($product_id)) {

			$image_post_id = get_post_thumbnail_id($product_id);
			$image_title   = esc_attr(get_the_title($image_post_id));
			$image_data    = wp_get_attachment_image_src($image_post_id, 'full');
			$image_link    = $image_data[0];
			/* CUSTOM START - single line replaced as following */
			//$image         = get_the_post_thumbnail( $product_id, $image_size, array(
			$image = get_the_post_thumbnail($product_id, apply_filters('bundled_product_large_thumbnail_size', 'full'), array(
				'title'                   => $image_title,
				'data-caption'            => get_post_field('post_excerpt', $image_post_id),
				'data-large_image'        => $image_link,
				'data-large_image_width'  => $image_data[1],
				'data-large_image_height' => $image_data[2],
			));

			$html = '<figure class="bundled_product_image woocommerce-product-gallery__image">';
			$html .= sprintf('<a href="%1$s" class="image zoom" title="%2$s" data-rel="%3$s">%4$s</a>', $image_link, $image_title, $image_rel, $image);
			$html .= '</figure>';

		} else {

			$html = '<figure class="bundled_product_image woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf('<a href="%1$s" class="placeholder_image zoom" data-rel="%3$s"><img class="wp-post-image" src="%1$s" alt="%2$s"/></a>', wc_placeholder_img_src(), __('Bundled product placeholder image', 'woocommerce-composite-products'), $image_rel);
			$html .= '</figure>';
		}

		/* CUSTOM START */

	} else {

		if (wp_get_attachment_url(get_post_thumbnail_id($product_id))) {

			$poid = get_the_ID();

			$podata = get_post_meta($poid);
			if (isset($podata['bundle_square_image'][0])) {
				$poimgid = $podata['bundle_square_image'][0];
			} else {
				$poimgid = '';
			}

			$newfeatimg = get_post($poimgid);
			//echo "<pre>"; print_r($newfeatimg); echo "</pre>";
	
			$squareImageTag = '<img src="' . $newfeatimg->guid . '" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="" title="bundle-7for99">';
			$newfeatimg->post_type;
			$proid = $product_id;
			//echo $bundled_item->data->data['bundle_id'];
	

			$productaa  = new WC_Product_Variable($proid);
			$variations = $productaa->get_available_variations();

			//$var_data = [];
			$squareImagesArray = array();
			//print_r($variations );
			foreach ($variations as $variation) {

				$varid = $variation['variation_id'];
				if (isset($variation['attributes']['attribute_pa_colour'])) {
					$c = $variation['attributes']['attribute_pa_colour'];
				} else {
					$c = '';
				}
				$color       = $c;
				$key_1_value = get_post_meta($varid, 'single_product_square_image', true);

				$post_7 = get_post($key_1_value);
				//echo "</br>";
				$image                           = $post_7->guid;
				$squareImagesArray[trim($color)] = trim($image);
			}

			//echo '<pre>'; print_r($squareImagesArray); echo '</pre>';
	
			$image_post_id = get_post_thumbnail_id($product_id);
			$image_title   = esc_attr(get_the_title($image_post_id));
			$image_data    = wp_get_attachment_image_src($image_post_id, 'full');
			$image_link    = $image_data[0];
			$image         = get_the_post_thumbnail($product_id, apply_filters('bundled_product_large_thumbnail_size', 'full'), array(
				'title'                   => $image_title,
				'data-large_image'        => $image_link,
				'data-large_image_width'  => $image_data[1],
				'data-large_image_height' => $image_data[2],
			));

			$_product = wc_get_product($proid);
			if ($_product->is_type('simple') && 0) {
				$image_post_id = get_post_thumbnail_id($proid);
				$image_title   = esc_attr(get_the_title($image_post_id));
				$image_data    = wp_get_attachment_image_src($image_post_id, 'full');
				$image_link    = $image_data[0];
				//$_product_image = '<img src="'.$image_link.'" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="" title="bundle-7for99">';
				$_product_image = get_the_post_thumbnail($proid, apply_filters('bundled_product_large_thumbnail_size', 'full'), array(
					'srcset'                  => $image_link,
					'title'                   => $image_title,
					'data-large_image'        => $image_link,
					'data-large_image_width'  => $image_data[1],
					'data-large_image_height' => $image_data[2],
				));
				$html           = '<figure class="bundled_product_image woocommerce-product-gallery__image" allsquareimages="' . htmlentities(json_encode($squareImagesArray)) . '">';
				$html .= sprintf('<a href="%1$s" class="image zoom" title="%2$s" data-rel="%3$s">%4$s</a>', $image_link, $image_title, $image_rel, $_product_image);
				$html .= '</figure>';
			} else {
				$image_post_id = get_post_thumbnail_id($proid);

				// CUSTOM START
				if ($image_post_id == "") {
					$image_post_id = get_post_thumbnail_id($product_id);
				}
				$image_title = esc_attr(wp_get_attachment_caption($image_post_id));
				// CUSTOM END
	
				// $image_title   = esc_attr( get_the_title( $image_post_id ) );
				$image_data = wp_get_attachment_image_src($image_post_id, 'full');
				$image_link = $image_data[0];

				// CUSTOM START
				$_product_image = get_the_post_thumbnail($product_id, apply_filters('bundled_product_large_thumbnail_size', 'full'), array(
					'srcset'                  => $image_link,
					'title'                   => $image_title,
					'data-large_image'        => $image_link,
					'data-large_image_width'  => $image_data[1],
					'data-large_image_height' => $image_data[2],
				));
				// CUSTOM END
	
				// $_product_image = '<img src="'.$image_link.'" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="" title="bundle-7for99">';
				$html = '<figure class="bundled_product_image woocommerce-product-gallery__image" allsquareimages="' . htmlentities(json_encode($squareImagesArray)) . '">';
				$html .= sprintf('<a href="%1$s" class="image zoom" title="%2$s" data-rel="%3$s">%4$s</a>', $image_link, $image_title, $image_rel, $_product_image);
				$html .= '</figure>';
			}

		} else {

			$html = '<figure class="bundled_product_image woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf('<a href="%1$s" class="placeholder_image zoom" data-rel="%3$s"><img class="wp-post-image" src="%1$s" alt="%2$s"/></a>', wc_placeholder_img_src(), __('Bundled product placeholder image', 'woocommerce-composite-products'), $image_rel);
			$html .= '</figure>';
		}
	}

	/* CUSTOM END */

	echo apply_filters('woocommerce_bundled_product_image_html', $html, $product_id, $bundled_item);

	?>
</div>