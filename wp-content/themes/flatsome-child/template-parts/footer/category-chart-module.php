<?php
$matched_charts     = [];
$product_categories = [];

if (is_product_category()) {
	// If on a category page, get the current category ID
	$term_id            = get_queried_object_id();
	$multiselect_values = get_field('product_chart', 'category_' . $term_id);
	$product_categories = [$term_id];
} elseif (is_product()) {
	// If on a single product page, get the product's primary category
	$product_id         = get_the_ID();
	$multiselect_values = get_field('product_chart', $product_id);

	if (!$multiselect_values) {
		// Get primary category from Yoast
		$primary_cat_id = get_post_meta($product_id, '_yoast_wpseo_primary_product_cat', true);
		if ($primary_cat_id) {
			$product_categories[] = $primary_cat_id;
		}
	}
} else {
	$multiselect_values = [];
}
 
// If still no charts found, check global level
if (!$multiselect_values && !empty($product_categories)) {
	$global_charts = get_field('rep_size_chart', 'option');
	foreach ($global_charts as $chart) {
		if (in_array($chart['select_category_for_chart'], $product_categories)) {
			$multiselect_values = $chart['select_charts'];
			break;
		}
	}
}

 
if ($multiselect_values) {
	foreach ($multiselect_values as $chart_id) {
		$chart            = get_post($chart_id);
		$matched_charts[] = [
			'chart_name'  => $chart->post_title,
			'chart_image' => get_the_post_thumbnail_url($chart_id)
		];
	}
}

 
?>

<?php if ($multiselect_values): ?>
	<div id="inline2" style="display: none;">
		<?php foreach ($multiselect_values as $chart_id): ?>
			<?php $chart = get_post($chart_id); ?>
			<button class="<?php echo sanitize_title($chart->post_title); ?> tablink">
				<?php echo $chart->post_title; ?>
			</button>
		<?php endforeach; ?>

		<?php if ($matched_charts): ?>
			<?php foreach ($matched_charts as $chart): ?>
				<div class="tabcontent" id="<?php echo sanitize_title($chart['chart_name']); ?>" style="display: none;">
					<img src="<?php echo esc_url($chart['chart_image']); ?>" alt="<?php echo esc_attr($chart['chart_name']); ?>">
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
<?php else: ?>
	<style>
		.various2_hide {
			display: none !important;
		}
	</style>
<?php endif; ?>