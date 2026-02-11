<style>
	#visual_term_pa_size .size-style {
		display: flex;
		gap: 10px;
		align-items: baseline;
		justify-content: center;
	}

	#visual_term_pa_size .size-style .size-title {
		border-radius: 6px;
		font-size: 16px;
		font-weight: 600;
		padding: 5px 15px;
		background: #000;
		color: #fff;
	}

	#visual_term_pa_size .size-style .size-all-list span {
		display: inline-block;
	}

	#visual_term_pa_size .size-style .size-all-list {
		display: flex;
		flex-wrap: wrap;
		gap: 5px;
	}

	@media(max-width:991px) {

		#visual_term_pa_size .size-style .size-title,
		#visual_term_pa_size .size-style .size-all-list span {
			font-size: 14px;
			padding: 5px 10px;
		}

		#visual_term_pa_size .size-style {
			gap: 5px;

		}

		.help_huide #visual_term_pa_size {
			flex-direction: column;
		}
	}

	@media(max-width:767px) {
		.help_huide #visual_term_pa_size {
			justify-content: start;
			margin: 10px 0px 30px;
		}

		#visual_term_pa_size .size-style {
			justify-content: start;
			gap: 10px;
		}

		#visual_term_pa_size .size-style .size-all-list {
			gap: 10px;
			width: 90%;
		}

		#visual_term_pa_size .size-style .size-title {
			width: 80px;
			text-align: center;
		}
	}
</style>
<?php
if (is_product_category()) {  // this one is needed else shop page will throw error
	if (is_product_category('weekly-deals')) { ?>
		<?php echo do_shortcode('[cacarousel]'); ?>
	<?php }

	if (is_product_category('bundles')) { ?>
		<a href="<?php echo esc_url(home_url('/buy/5-for-99/')); ?>"> <img src="<?php echo esc_url(get_site_url() . '/wp-content/uploads/2021/11/image-2.png'); ?>" alt="" style="width:100%;display:none;"></a>
	<?php } ?>
	<div class="select-size-wrapper" style="clear:both;">
		<?php
		$category            = get_queried_object();
		$termid              = $category->term_id;
		$taxonomy            = $category->taxonomy; // Ensure you have the taxonomy available, important for ACF to correctly identify the field
		$image_link          = get_field('rudr_url', $taxonomy . '_' . $termid);
		$custom_banner_image = get_field('rudr_text', $taxonomy . '_' . $termid);
		if (!$custom_banner_image) {
			$custom_banner_image = get_field('quality_control_banner', 'option');
		}
		if ($custom_banner_image) {
			?>
			<?php if ($image_link) { ?> <a href="<?php echo esc_url($image_link); ?>">
				<?php } ?>
				<img src="<?php echo esc_url($custom_banner_image); ?>" alt="" style="width:100%;display:block;">
				<br />
				<?php if ($image_link) { ?>
				</a>
				<br />
			<?php }
		}
		?>


		<?php if (is_product_category('prints')) { ?>
			<p style="text-align: center;font-weight:bold;clear:both;display:none;margin-bottom:5px;">CHOOSE YOUR PRINTS - CART WILL AUTO UPDATE</p>
		<?php }
		if (is_product_category('5-for-99')) { ?>
			<p style="text-align: center;font-weight:bold;clear:both;margin-bottom:5px;">CHOOSE YOUR 5 ITEMS - CART WILL AUTO UPDATE</p>
		<?php } else if (is_product_category('6-for-99')) { ?>
				<p style="text-align: center;font-weight:bold;clear:both;margin-bottom:5px;">CHOOSE YOUR 6 ITEMS - CART WILL AUTO UPDATE</p>
		<?php } ?>

		<?php if (!is_product_category('accessories')) { ?>

			<select id="term_pa_size" style="display:none;">
				<option value="">Select Size</option>
				<option value="all">All</option>
				<option value="small">Small</option>
				<option value="medium">Medium</option>
				<option value="large">Large</option>
				<option value="extra-large">XL</option>
				<option value="2xl">2XL</option>
				<option value="3xl">3XL</option>
				<option value="4xl">4XL</option>
				<option value="5xl">5XL</option>
			</select>

			<?php
			$style = false;
			if (is_product_category()) {  // this one is needed else shop page will throw error
				if (is_product_category('5-for-99') || is_product_category('blank-tall-tees')) {
					$style = true;
				}
			}
			?>
			<div class="help_huide">
				<div class="help_huide_links">
					<a href="#" id="toggleHelp" style="color:black;text-decoration:underline;font-size:15px;">Refine by SIZE<?php if ($style) { ?>/STYLE<?php } ?></a> &nbsp;&nbsp;


					<span class="various2_hide">| &nbsp;
						<a id="various2" title="" href="#inline2">
							<span style="color:black;text-decoration:underline;font-size:15px;text-transform: capitalize;" title="Size Guide">Size Guide</span>
						</a>
					</span>



				</div>
				<div id="visual_term_pa_size" style="display:none;">
					<div class="size-style">
						<div class="size-title">SIZE :</div>
						<div class="size-all-list" id="termmm_size">
							<span class="size-option" data-value="all">All</span>
							<span class="size-option" data-value="small">Small</span>
							<span class="size-option" data-value="medium">Medium</span>
							<span class="size-option" data-value="large">Large</span>
							<span class="size-option" data-value="extra-large">XL</span>
							<span class="size-option" data-value="2xl">2XL</span>
							<span class="size-option" data-value="3xl">3XL</span>
							<span class="size-option" data-value="4xl">4XL</span>
							<span class="size-option" data-value="5xl">5XL</span>
						</div>
					</div>
					<?php if ($style) { ?>
						<div class="size-style" id="termmm_style">
							<div class="size-title">STYLE :</div>
							<div class="size-all-list">
								<span class="size-option" data-value="all" data-cat="0">All</span>
								<span class="size-option" data-value="blank-tall-tees" data-cat="653">Blank Tall Tees</span>
								<span class="size-option" data-value="semi-tall" data-cat="585">Semi-Tall Tees</span>
								<?php 	if (is_product_category('5-for-99')) { ?>
								<span class="size-option" data-value="oversized-tees" data-cat="654">Oversized Tees</span>
								<span class="size-option" data-value="prints" data-cat="140">Graphic Tees</span>
								<?php } ?>
							</div>
						</div>

					<?php } ?>
				</div>
			</div>





		<?php } ?>




	</div>
<?php } ?>
