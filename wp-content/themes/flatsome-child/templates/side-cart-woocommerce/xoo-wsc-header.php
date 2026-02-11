<?php
/**
 * Side Cart Header
 *
 * This template can be overridden by copying it to yourtheme/templates/side-cart-woocommerce/xoo-wsc-header.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 * @see     https://docs.xootix.com/side-cart-woocommerce/
 * @version 4.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

extract( Xoo_Wsc_Template_Args::cart_header() );

?>

<div class="xoo-wsch-top">

	<?php if( $showNotifications ): ?>
		<?php xoo_wsc_cart()->print_notices_html( 'cart' ); ?>
	<?php endif; ?>

	<?php if( $showBasket ): ?>
		<div class="xoo-wsch-basket">
			<span class="xoo-wscb-icon xoo-wsc-icon-bag2"></span>
			<span class="xoo-wscb-count"><?php echo xoo_wsc_cart()->get_cart_count() ?></span>
		</div>
	<?php endif; ?>

	<?php if( $heading ): ?>
		<span class="xoo-wsch-text"><?php echo $heading ?></span>
	<?php endif; ?>

	<?php if( $showCloseIcon ): ?>
		<span class="xoo-wsch-close <?php echo  $close_icon ?>"></span>
	<?php endif; ?>

	<div class="xoo-wsc-header">
        <div class="cart-popup-title text-center logo_part clearfix">                         
                            <div class="continue_shop">
							<a class="continue_shopping_btn xoo-wsc-close closeit xoo-wsch-close" href="javascript:void(0);"><i class="fa fa-angle-left"></i>  <span class="closeits">Continue Shopping</span></a></div>
                            <img src="<?php echo esc_url(get_site_url() . '/wp-content/uploads/2017/07/plus-2-clothing-logoWEB.jpg'); ?>" alt="logo">
							<?php
    // Ensure WooCommerce is active
    if ( class_exists( 'WooCommerce' ) ) {
        global $woocommerce;

        // Get the total count of items in the cart
        $cart_count = $woocommerce->cart->get_cart_contents_count();

        // Display the count
        echo '<div class="top_items">' . esc_html( $cart_count ) . ' Items</div>';
    }
?>

                           
                        </div>
                    <div class="top_checkout"><a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="button checkout wc-forward">Checkout</a></div>    

		  

		</div>
</div>

<?php xoo_wsc_helper()->get_template( 'global/header/shipping-bar.php' ) ?>

<style>
.header-cart-link{
	cursor:pointer;
}
a.closeit {
     display: inline-block;
	 
    
}
.cart_sidebar .xoo-wsc-header, .cart_sidebar .xoo-wsc-footer {
    padding: 0;
}
.xoo-wsc-header {
    background-color: #ffffff;
    color: #000000;
    border-bottom-width: 1px;
    border-bottom-color: #eeeeee;
    border-bottom-style: solid;
    padding: 10px 20px;
}
.xoo-wsc-header {
    position: relative;
    font-weight: 600;
}
.logo_part {
    padding: 5px 10px;
    color: #fff;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 17px;
    font-weight: 400;
}
.cart-popup-title {
    background: #000;
}
.logo_part .continue_shop {
    display: inline-block;
    text-align: left;
    float: left;
    text-indent: -5px;
    padding-left: 9px;
    margin: 7px 0 0;
    letter-spacing: 1.8px;
}
.logo_part .continue_shop a {
    color: #fff;
}
.continue_shop .xoo-wsc-close {
    transform: none;
    top: 9px;
    right: auto;
    font-size: 12px !important;
}
.logo_part img {
    width: 45px;
    margin: 0;
    padding: 0;
}
.logo_part .top_items {
    display: inline-block;
    float: right;
    margin: 15px 0 0;
}
.top_checkout {
    margin: 3px 3px 0;
}
.top_checkout .button {
    width: 100%;  font-size: 20px;
}
.top_checkout .button {
    background: #81d742 !important;
    margin: 0 !important;
}
.xoo-wsc-notification-bar {
    background-color: #DFF0D8;
    color: #3C763D;
    position: absolute;
    top: 0;
    z-index: 1;
    left: 0;
    right: 0;
    font-weight: 400;
    font-size: 15px;
    padding: 13px 15px;
    display: none;
} 
.cart_sidebar .xoo-wsch-top {
    display: block;
    margin: 0;
}
.cart_sidebar span.xoo-wsch-text {
    display: none;
}
.cart_sidebar span.xoo-wsch-close.xoo-wsc-icon-cross {
    display: none;
}

</style>



