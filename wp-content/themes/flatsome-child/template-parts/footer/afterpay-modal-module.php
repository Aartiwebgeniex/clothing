<!-- Html for afterpay modal start-->
<div id="afterpay" style="display: none;" class="afterpay">
	<div class="outer pop-au">
		<div class="inner">
			<div class="top">
				<div class="left">
					<div class="logo">
						<img src="
							<?php echo get_stylesheet_directory_uri(); ?>/images/logo_scroll.png" alt="">
					</div>
					<div class="head">Shop Now. pay later. 100% interest free</div>
					<div class="para">Simple instalment plans available instantly at checkout</div>
				</div>
				<div class="rght">
					<img src="
							<?php echo get_stylesheet_directory_uri(); ?>/images/afterpay.jpg" alt="">
				</div>
			</div>
			<div class="bottom1">
				<div class="icons">
					<div class="block">
						<div class="icn">
							<img src="
										<?php echo get_stylesheet_directory_uri(); ?>/images/cart.png" alt="">
							<div class="icn_no">1</div>
						</div>
						<div class="icn_head">Select after pay as your payment method</div>
						<div class="icn_para">use you exsisting debit or credit card</div>
					</div>
					<div class="block">
						<div class="icn">
							<img src="
											<?php echo get_stylesheet_directory_uri(); ?>/images/watch.png" alt="">
							<div class="icn_no">2</div>
						</div>
						<div class="icn_head">complete your checkout in seconds</div>
						<div class="icn_para">No long forms, instant approval online</div>
					</div>
					<div class="block">
						<div class="icn">
							<img src="
												<?php echo get_stylesheet_directory_uri(); ?>/images/open-box.png" alt="">
							<div class="icn_no">3</div>
						</div>
						<div class="icn_head">pay over 4 equal insallments</div>
						<div class="icn_para">Pay fortnightly, enjoy your purchase straight away!</div>
					</div>
				</div>
				<div class="fter">
					<p>
						<b>All you need is:</b>
					</p>
					<p>1) An Australlian visa or master card; 2) To be over 18 year of age; 3) To live in australia To see Afterpay's complete terms, Visit
						<a href="https://www.afterpay.com.au/terms" target="_blank">https://www.afterpay.com.au/terms</a>
					</p>
					<p>&nbsp;</p>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Html for afterpay modal end-->

<script>
	jQuery(document).ready(function () {
		setTimeout(function () {
			var review = jQuery('.bottomLine').clone();
			//var review = '<div style = "margin-bottom :10 px;">'+review+'</div>';
			jQuery('.bottomLine').remove();
			jQuery('.afterpay-payment-info').after(review);
			//jQuery('.yotpo-small').show();
		}, 5000);

		setTimeout(function () {
			var data_orignal = jQuery('.afterpay-payment-info .woocommerce-Price-amount').attr('data-original');
			var data_price = jQuery('.afterpay-payment-info .woocommerce-Price-amount').attr('data-price');
			var title = jQuery('.afterpay-payment-info .woocommerce-Price-amount').attr('title');
			var amount = jQuery('.afterpay-payment-info .woocommerce-Price-amount').html();
			jQuery('.afterpay-payment-info').html("or make 4 interest-free payments of <span id='afterpay_instalments' class= 'woocommerce-Price-amount amount' data-orignal ='" + data_orignal + "' data-price = '" + data_price + "' title = '" + title + "'>" + amount + "</span> fortnightly <br/> with	<a class='afterpay-box' href='#afterpay'> <img style='vertical-align:bottom' width='100' alt='Afterpay' src='<?php echo get_stylesheet_directory_uri(); ?>/images/logo_scroll.png'><span><u>More info</u> ");
		}, 1000);
	});
</script>

<?php if (is_product()) { ?>
	<script>
		jQuery(document).ready(function () {
			jQuery('.afterpay-box').fancybox();
		});
	</script>
<?php } ?>