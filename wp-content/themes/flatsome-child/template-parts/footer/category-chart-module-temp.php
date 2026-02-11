<?php

/************* JUST FOR TESTING 2021-07-19************/

$hoodie_img           = esc_attr(get_option('tall_hoodie_size_image'));
$tall_tees_img        = esc_attr(get_option('tall_tee_size_image'));
$button_ups_img       = esc_attr(get_option('button_ups'));
$trackpants_img       = esc_attr(get_option('trackpants'));
$crewneck_jumpers_img = esc_attr(get_option('crewneck_jumpers'));
$semi_tall            = esc_attr(get_option('semi_tall'));
$work_shirt           = esc_attr(get_option('work_shirt'));
$basketball_shorts    = esc_attr(get_option('basketball_shorts'));
$polo_shirt           = esc_attr(get_option('polo_shirt'));
$jogger_pants         = esc_attr(get_option('jogger_pants'));
$jacket_chart         = esc_attr(get_option('jacket'));
$singletO             = esc_attr(get_option('singlet'));


$category                  = get_queried_object();
$termid                    = $category->term_id;
$tallhoodie                = get_term_meta($termid, 'wh_meta_hoodies', true);
$talltee                   = get_term_meta($termid, 'wh_meta_tees', true);
$buttonups                 = get_term_meta($termid, 'wh_meta_buttons', true);
$trackpants                = get_term_meta($termid, 'wh_meta_trackpants', true);
$jumpers                   = get_term_meta($termid, 'wh_meta_crewneck_jumpers', true);
$wh_meta_semi_tall         = get_term_meta($termid, 'wh_meta_semi_tall', true);
$wh_meta_work_shirt        = get_term_meta($termid, 'wh_meta_work_shirt', true);
$wh_meta_basketball_shorts = get_term_meta($termid, 'wh_meta_basketball_shorts', true);
$wh_meta_polo_shirt        = get_term_meta($termid, 'wh_meta_polo_shirt', true);
$wh_meta_jogger_pants      = get_term_meta($termid, 'wh_meta_jogger_pants', true);
$wh_meta_jacket            = get_term_meta($termid, 'wh_meta_jacket', true);
$wh_meta_singlet           = get_term_meta($termid, 'wh_meta_singlet', true);

?>
<div id="inline2" style="display:none;">
	<?php
	if (is_product_category()) {
		if ($talltee == 'yes') { ?>
			<button class="tab1 tablink">Tall Tees</button>
		<?php }
		if ($tallhoodie == 'yes') { ?>
			<button class="tab3 tablink">Tall Hoodies</button>
		<?php }
		if ($buttonups == 'yes') { ?>
			<button class="tab2 tablink">Button-ups</button>
		<?php }
		if ($trackpants == 'yes') { ?>
			<button class="tab4 tablink">Trackpants</button>
		<?php }
		if ($jumpers == 'yes') { ?>
			<button class="tab5 tablink">Jumpers</button>
		<?php }
		if ($wh_meta_semi_tall == 'yes') { ?>
			<button class="tab6 tablink">Semi Tall</button>
		<?php }
		if ($wh_meta_work_shirt == 'yes') { ?>
			<button class="tab7 tablink">Work Shirt</button>
		<?php }
		if ($wh_meta_basketball_shorts == 'yes') { ?>
			<button class="tab8 tablink">Basketball Shorts</button>
		<?php }
		if ($wh_meta_polo_shirt == 'yes') { ?>
			<button class="tab9 tablink">Polo Shirt</button>
		<?php }
		if ($wh_meta_jogger_pants == 'yes') { ?>
			<button class="tab10 tablink">Jogger Pants</button>
		<?php }
		if ($wh_meta_jacket == 'yes') { ?>
			<button class="tab11 tablink">Jacket</button>
		<?php }
		if ($wh_meta_singlet == 'yes') { ?>
			<button class="tab12 tablink">Singlet</button>
		<?php }

	} else {

		global $post;



		$tall_tees        = get_post_meta($post->ID, 'tall_tees', true);
		$tall_hoodies     = get_post_meta($post->ID, 'tall_hoodies', true);
		$button_ups       = get_post_meta($post->ID, 'button_ups', true);
		$track_pants      = get_post_meta($post->ID, 'tackpants', true);
		$crewneck_jumpers = get_post_meta($post->ID, 'crewneck_jumpers', true);

		$post_semi_tall  = get_post_meta($post->ID, 'semi_tall', true);
		$post_work_shirt = get_post_meta($post->ID, 'work_shirt', true);
		$post_basket     = get_post_meta($post->ID, 'basketball_shorts', true);
		$post_polo       = get_post_meta($post->ID, 'polo_shirt', true);
		$post_jogger     = get_post_meta($post->ID, 'jogger_pants', true);
		$jacket          = get_post_meta($post->ID, 'jacket', true);
		$singlet         = get_post_meta($post->ID, 'singlet', true);
		if ($tall_tees == 'yes') { ?>
			<button class="tab1 tablink">Tall Tees</button>
		<?php }
		if ($tall_hoodies == 'yes') { ?>
			<button class="tab3 tablink">Tall Hoodies</button>
		<?php }
		if ($button_ups == 'yes') { ?>
			<button class="tab2 tablink">Button-ups</button>
		<?php }
		if ($track_pants == 'yes') { ?>
			<button class="tab4 tablink">Trackpants</button>
		<?php }
		if ($crewneck_jumpers == 'yes') { ?>
			<button class="tab5 tablink">Jumpers</button>
		<?php }
		if ($post_semi_tall == 'yes') { ?>
			<button class="tab6 tablink">Semi Tall</button>
		<?php }
		if ($post_work_shirt == 'yes') { ?>
			<button class="tab7 tablink">Work Shirt</button>
		<?php }
		if ($post_basket == 'yes') { ?>
			<button class="tab8 tablink">Basketball Shorts</button>
		<?php }
		if ($post_polo == 'yes') { ?>
			<button class="tab9 tablink">Polo Shirt</button>
		<?php }
		if ($post_jogger == 'yes') { ?>
			<button class="tab10 tablink">Jogger Pants</button>
		<?php }
		if ($jacket == 'yes') { ?>
			<button class="tab11 tablink">Jacket</button>
		<?php }
		if ($singlet == 'yes') { ?>
			<button class="tab12 tablink">Singlet</button>
		<?php }
	}
	?>

	<div class="tabcontent" id="tab1">
		<img src="<?php echo $tall_tees_img; ?>">
	</div>
	<div class="tabcontent" id="tab2" style="display:none;">
		<img src="<?php echo $button_ups_img; ?>">
	</div>
	<div class="tabcontent" id="tab3" style="display:none;">
		<img src="<?php echo $hoodie_img; ?>">
	</div>
	<div class="tabcontent" id="tab4" style="display:none;">
		<img src="<?php echo $trackpants_img; ?>">
	</div>
	<div class="tabcontent" id="tab5" style="display:none;">
		<img src="<?php echo $crewneck_jumpers_img; ?>">
	</div>
	<div class="tabcontent" id="tab6" style="display:none;">
		<img src="<?php echo $semi_tall; ?>">
	</div>
	<div class="tabcontent" id="tab7" style="display:none;">
		<img src="<?php echo $work_shirt; ?>">
	</div>
	<div class="tabcontent" id="tab8" style="display:none;">
		<img src="<?php echo $basketball_shorts; ?>">
	</div>
	<div class="tabcontent" id="tab9" style="display:none;">
		<img src="<?php echo $polo_shirt; ?>">
	</div>
	<div class="tabcontent" id="tab10" style="display:none;">
		<img src="<?php echo $jogger_pants; ?>">
	</div>
	<div class="tabcontent" id="tab11" style="display:none;">
		<img src="<?php echo $jacket_chart; ?>">
	</div>
	<div class="tabcontent" id="tab12" style="display:none;">
		<img src="<?php echo $singletO; ?>">
	</div>
</div>


<script>
	jQuery('.tab1').click(function () {
		jQuery('#tab1').show();
		jQuery('#tab2').hide();
		jQuery('#tab3').hide();
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab2').click(function () {
		jQuery('#tab2').show();
		jQuery('#tab1').hide();
		jQuery('#tab3').hide();
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab3').click(function () {
		jQuery('#tab3').show();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab4').click(function () {
		jQuery('#tab4').show();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab5').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab5').click(function () {
		jQuery('#tab5').show();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab6').click(function () {
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').show();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab7').click(function () {
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').show();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab8').click(function () {
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').show();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab9').click(function () {
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').show();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab10').click(function () {
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').show();
		jQuery('#tab11').hide();
		jQuery('#tab12').hide();
	});
	jQuery('.tab11').click(function () {
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').show();
		jQuery('#tab12').hide();
	});

	jQuery('.tab12').click(function () {
		jQuery('#tab5').hide();
		jQuery('#tab4').hide();
		jQuery('#tab3').hide();
		jQuery('#tab1').hide();
		jQuery('#tab2').hide();
		jQuery('#tab6').hide();
		jQuery('#tab7').hide();
		jQuery('#tab8').hide();
		jQuery('#tab9').hide();
		jQuery('#tab10').hide();
		jQuery('#tab11').hide();
		jQuery('#tab12').show();
	});






	jQuery(document).ready(function () {
		setTimeout(function () {
			jQuery("#inline2").find(".tablink:first").trigger('click');
		}, 1000);


	});
</script>

<style>
	.tablink {
		color: black;
		border: none;
		border-radius: 0 10px 0 10px;
		outline: none;
		cursor: pointer;
		font-weight: 700;
		background: #eee;
		padding: 0px 5px 0 5px;
		font-size: 14px;
		text-transform: capitalize;
		max-width: 200px !important;
		margin: 0;
	}

	.tablink:hover {
		background-color: #777;
		border-radius: 5px;
	}

	.tabcontent {
		color: black;
		padding: 10px;
		clear: both;
		text-align: center;
	}


	@media (max-width: 786px) {
		.tablink {
			font-size: 12px;
			width: 80px;
		}

	}
</style>