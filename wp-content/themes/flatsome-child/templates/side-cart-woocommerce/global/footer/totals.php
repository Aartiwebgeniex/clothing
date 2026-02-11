<?php
/**
 * Totals
 *
 * This template can be overridden by copying it to yourtheme/templates/side-cart-woocommerce/global/footer/totals.php.
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

extract( Xoo_Wsc_Template_Args::footer_totals() );

?>

<?php if( WC()->cart->is_empty() ) return; ?>

<div class="xoo-wsc-ft-totals">
 
		<div class="xoo-wsc-ft-amt xoo-wsc-ft-amt" style="padding:0 15px;">
			<span class="xoo-wsc-ft-amt-label">Total</span>
			<span class="xoo-wsc-ft-amt-value amount" > 
			
			
			<?php
			 $cur = get_woocommerce_currency();
			?>
			
			 
			   
                    <?php if (WC()->cart->subtotal < 120 && $cur == 'AUD') { ?>
                     
                            <?php echo number_format((WC()->cart->subtotal + 9.95), 2, '.', ''); ?>
                    
                    <?php } else { ?>
                        
                            <?php echo WC()->cart->get_cart_subtotal(); ?>
                       
                    <?php } ?>
         
			
			
			
			
			
			
			
			
			 
			</span>
		</div>
	 

	<?php do_action( 'xoo_wsc_totals_end' ); ?>

</div>
