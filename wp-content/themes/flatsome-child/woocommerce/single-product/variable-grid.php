<?php
global $woocommerce, $wc_bulk_variations, $post;
$matrix_data = woocommerce_bulk_variations_create_matrix($post->ID);


$matrix           = $matrix_data['matrix'];
$matrix_columns   = $matrix_data['matrix_columns'];
$matrix_rows      = $matrix_data['matrix_rows'];
$row_attribute    = $matrix_data['row_attribute'];
$column_attribute = $matrix_data['column_attribute'];

//Set up some locals
$row_index    = 0;
$column_index = 0;
$cell_index   = 0;
$info_boxes   = array();
?>

<?php do_action('woocommerce_bv_before_add_to_cart_form'); ?>


<div id="matrix_form">
	<div class="summary">
		<?php woocommerce_template_single_title(); ?>
		<?php woocommerce_template_single_price(); ?>
		<?php woocommerce_template_single_excerpt(); ?>
	</div>

	<!-- CUSTOM START -->
	<style>
		#matrix_form_table input {
			width: 35%;
			border: 1px solid #C4C4C4;
			margin: 0px;
			height: 25px;
			margin-bottom: 10px;
		}

		.coutstock {
			color: red;
			vertical-align: middle;
		}

		.circle_swatch {
			width: 20px;
			height: 20px;
			-moz-border-radius: 10px;
			-webkit-border-radius: 10px;
			border-radius: 10px;
		}


		.postid-4894 .cardinal-red,
		.postid-4894 .electric-blue-andamp-black,
		.postid-4894 .burgundy-andamp-black,
		.postid-4894 .brown-andamp-black,
		.postid-4894 .black-andamp-white,
		.postid-4894 .scorched-burgundy,
		.postid-4894 .burgundy {
			/*display:none;*/
		}

		@media screen and (max-width: 767px) {
			#matrix_form_table input {
				width: 35%;
				border: 1px solid #C4C4C4;
				margin: 0px;
				height: 25px;
				margin-bottom: 10px;
			}

			.coutstock {
				color: red;
				font-size: 14px;
				vertical-align: middle;
			}

			#matrix_form_table td.row-label {
				text-align: left;
			}

		}
	</style>
	<!-- CUSTOM END -->
	<form id="wholesale_form" action="" class="bulk_variations_form cart matrix" method="post" enctype='multipart/form-data'>
		<table id="matrix_form_table">
			<thead>
				<tr>
					<th></th>
					<?php foreach ($matrix_columns as $column): ?>
						<!-- CUSTOM START -->
						<?php
						//custom code start			
					
						switch (woocommerce_bulk_variations_get_title($column_attribute, $column)) {
							case 'Small':
								$column_name = 'SML';
								break;
							case 'Medium':
								$column_name = 'MED';
								break;
							case 'Large':
								$column_name = 'LGE';
								break;
							case 'XL':
								$column_name = 'XLG';
								break;
							default:
								$column_name = woocommerce_bulk_variations_get_title($column_attribute, $column);
								break;
						}

						?>
						<!-- CUSTOM END -->

						<th>
							<?php echo $column_name; //echo woocommerce_bulk_variations_get_title( $column_attribute, $column );  ?>
						</th>
					<?php endforeach; ?>
					<?php if ($wc_bulk_variations->get_setting('use_quantity_selectors', false)): ?>
						<th></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($matrix as $row => $columns): ?>
					<?php $column_index = 0; ?>
					<!-- CUSTOM START -->

					<?php

					//echo woocommerce_bulk_variations_get_title( $row_attribute, $matrix_rows[ $row_index ] );
				
					$color = woocommerce_bulk_variations_get_title($row_attribute, $matrix_rows[$row_index]);
					switch ($color) {
						case 'Black':
							$color = "#000000";
							break;
						case 'White':
							$color = "#FFFFFF";
							break;
						case 'Acid Wash':
							$color = "#A9A9A9";
							break;
						case 'Light Grey':
							$color = "#D3D3D3";
							break;
						case 'Dark Grey':
							$color = "#A9A9A9";
							break;
						case 'Navy Blue':
							$color = "#000080";
							break;
						case 'Burgundy':
							$color = "#8C001A ";
							break;
						case 'Tan':
							$color = "#D2B48C";
							break;
						case 'Stone':
							$color = "#b8b09b";
							break;
						case 'Army Green':
							$color = "#556b2f";
							break;
						case 'Charcoal':
							$color = "#36454f";
							break;
						case 'Scorched Burgundy':
							$color = "#843F42";
							break;
						case 'Grey Marle':
							$color = "#9E9FB1";
							break;
						case 'Camel':
							$color = "#c48157";
							break;
						case 'Yellow Haze':
							$color = "#eed4b1";
							break;
					}

					?>

					<?php
					$str  = woocommerce_bulk_variations_get_title($row_attribute, $matrix_rows[$row_index]);
					$slug = strtolower(trim(preg_replace('/[\s-]+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), '-'));

					?>
					<!-- CUSTOM END -->
					<tr class="<?php echo $row_index % 2 == 0 ? '' : 'alt'; ?>" data-index="<?php echo $row_index; ?>">
						<td class="row-label">
							<!-- CUSTOM START -->
							<div class="circle_swatch" style="background:<?php echo $color; ?>;float:left;">
							</div>&nbsp;
							<!-- CUSTOOM END -->
							<?php echo woocommerce_bulk_variations_get_title($row_attribute, $matrix_rows[$row_index]); ?>
						</td>

						<?php foreach ($columns as $key => $field_data): ?>
							<?php $column_index++; ?>

							<td>
								<?php if ($field_data): ?>

									<?php $variation      = new WC_Product_Variation($field_data['variation_id']);
									$managing_stock = WC_Bulk_Variations_Compatibility::is_wc_version_gte_2_7() ? $variation->get_manage_stock() : $variation->manage_stock;
									$vmsg           = $variation->get_stock_quantity() ? sprintf(__('Only %s available', 'woocommerce-bulk-variations'), $variation->get_stock_quantity()) : sprintf(__('Currently unavailable', 'woocommerce-bulk-variations'));
									?>

									<!-- CUSTOM START -->
									<?php
										if ($variation->get_stock_quantity() != 0) { ?>
										<p>
											<?php
											global $product;
											// Corrected line: Use get_id() instead of directly accessing the id property
											$pcid = $product->get_id();

											if ($variation->get_stock_quantity() > 50) {
												echo "(50+)";
											} else {
												echo "(" . $variation->get_stock_quantity() . ")";
											}
											?>
										</p>
									<?php } else {
											if ($variation->get_stock_quantity() == 0 or $variation->get_stock_quantity() == '') {
												echo "<span class='coutstock'>OUT OF STOCK</span>";
											}
										}
										?>

									</p>
									<?php if (!$variation->get_stock_quantity() == 0 or !$variation->get_stock_quantity() == '') { ?>
										<!-- CUSTOM END -->
										<input data-manage-stock="<?php echo $managing_stock; ?>" data-purchasable="<?php echo $variation->is_purchasable() ? '1' : '0'; ?>" data-instock="<?php echo $variation->is_in_stock() ? '1' : '0'; ?>" data-backorders="<?php echo $variation->backorders_allowed() ? '1' : '0'; ?>" data-max="<?php echo $variation->get_stock_quantity(); ?>" data-price="<?php echo $variation->get_price(); ?>" data-vmsg="<?php echo $vmsg; ?>" title="<?php echo esc_attr(woocommerce_bulk_variations_get_price($variation->get_price())); ?>" id="qty_input_<?php echo $cell_index; ?>" data-column="<?php echo $column_index; ?>" class="number qty_input" type="text" name="order_info[<?php echo $cell_index; ?>][quantity]" />
										<!-- CUSTOM START -->
									<?php } ?>
									<!-- CUSTOM END -->
									<?php if ($wc_bulk_variations->get_setting('show_prices_in_grid', true)): ?>
										<p>
											<?php echo $field_data['price_html']; ?>
										</p>
									<?php endif; ?>

									<?php $info_boxes['qty_input_' . $cell_index . '_info'] = array(
										$row_attribute    => $row,
										$column_attribute => $key,
										'variation_data'  => $field_data,
										'variation'       => $variation
									); ?>


									<input type="hidden" name="order_info[<?php echo $cell_index; ?>][variation_id]" value="<?php echo $field_data['variation_id']; ?>" />
									<input type="hidden" name="order_info[<?php echo $cell_index; ?>][variation_data][attribute_<?php echo $column_attribute; ?>]" value="<?php echo $key; ?>" />
									<input type="hidden" name="order_info[<?php echo $cell_index; ?>][variation_data][attribute_<?php echo $row_attribute; ?>]" value="<?php echo $row; ?>" />
								<?php else: ?>
									<?php
									/* CUSTOM START */
									echo "<span class='coutstock'>OUT OF STOCK</span>";
									/* CUSTOM END */
									?>


								<?php endif; ?>
							</td>
							<?php
							$cell_index++;
							$column_index++;
							?>
						<?php endforeach; ?>
						<?php if ($wc_bulk_variations->get_setting('use_quantity_selectors', false)): ?>
							<?php $column_index++; ?>
							<td width="32px">
								<div class="quantity buttons_added">
									<input type="hidden" name="order_info[<?php echo $cell_index; ?>][quantity]" value="1" />
									<input type="button" value="-" class="minus" rel="<?php echo $row_index; ?>">
									<input type="button" value="+" class="plus" rel="<?php echo $row_index; ?>">
								</div>
							</td>
						<?php endif; ?>
					</tr>

					<?php $row_index++; ?>


				<?php endforeach; ?>

			</tbody>
		</table>

		<?php do_action('woocommerce_bv_before_add_to_cart_button'); ?>
		<div class="matrix-add-to-cart-wrap" style="margin-top: 10px;">
			<button type="submit" class="single_add_to_cart_button button alt">
				<?php echo apply_filters('single_add_to_cart_text', __('Add to cart', 'woocommerce'), 'variable'); ?>
			</button>
		</div>
		<?php do_action('woocommerce_bv_after_add_to_cart_button'); ?>

		<div>
			<input type="hidden" name="add-variations-to-cart" value="true" />
			<input type="hidden" name="product_id" value="<?php echo esc_attr($post->ID); ?>" />
		</div>
	</form>

	<?php if (!$wc_bulk_variations->is_only_bulk_variation_form()): ?>
		<input class="button btn-back-to-single" type="button" value="<?php _e('<-- Singular Order Form', 'woocommerce-bulk-variations'); ?>" />
	<?php else: ?>
		<input class="button btn-back-to-product" type="button" value="<?php _e('<-- Product Page', 'woocommerce-bulk-variations'); ?>" />
	<?php endif; ?>

	<div id="matrix_form_info_holder" style="display:none;">
		<?php foreach ($info_boxes as $key => $field_data): ?>

			<?php $variation = $field_data['variation']; ?>

			<div id="<?php echo $key; ?>" class="qty_input_info">
				<div class="images">
					<?php echo $variation->get_image(); ?>
				</div>
				<div class="summary">
					<p itemprop="name" class="product_title entry-title">
						<?php echo $variation->get_title(); ?>
					</p>
					<?php echo $variation->get_price_html(); ?>
					<ul>
						<li>
							<?php echo WC_Bulk_Variations_Compatibility::wc_attribute_label($row_attribute); ?>:
							<?php echo woocommerce_bulk_variations_get_title($row_attribute, $field_data[$row_attribute]); ?>
						</li>
						<li>
							<?php echo WC_Bulk_Variations_Compatibility::wc_attribute_label($column_attribute); ?>:
							<?php echo woocommerce_bulk_variations_get_title($column_attribute, $field_data[$column_attribute]); ?>
						</li>

						<?php if ($variation->get_sku()): ?>
							<li>
								<?php echo $field_data['variation_data']['sku']; ?>
							</li>
						<?php endif; ?>

					</ul>


					<?php echo $field_data['variation_data']['availability_html'] ? $field_data['variation_data']['availability_html'] : '<p class="stock">&nbsp;</p>'; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<!-- CUSTOM START -->
<script>
	jQuery(document).ready(function () {
		//  var wholesaleForm = ''; 
		wholesaleForm = jQuery("#wholesale_form").detach();

		//   var beta = jQuery('#wholesale_form');
		//  var cloned = beta.clone();
		// beta.hide();
		// beta.remove();

		jQuery(wholesaleForm).insertBefore('.product-footer');
		jQuery("#wholesale_form").addClass('row');
		jQuery(".btn-bulk").css('display', 'none');
		jQuery("table.variations").css('display', 'none');
		$("#matrix_form_table tbody tr").each(function () {
			if ($(this).find('td input').length < 1) {
				$(this).hide();
			}
		});


		//jQuery('.product-footer').prepend(cloned); 
		//alert(stringify.JSON(wholesaleForm));
		//jQuery(wholesaleForm).insertBefore("footer");
		/*
			jQuery('#wholesale_form tbody tr').each(function(){
			var a = 0;
			 jQuery(this).find('td').each(function(){
	   
			 a = jQuery(this).find('input').length;
	   
			 })
			alert(a);
			
			
			
	   //jQuery(this).find('td input').length
	   
	   })*/

	})
</script>
<!-- CUSTOM END -->
<?php do_action('woocommerce_bv_after_add_to_cart_form'); ?>