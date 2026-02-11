<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see       https://docs.woocommerce.com/document/template-structure/
 * @package   WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;
$custom_related   =   get_post_meta( $product->get_id(), '_subscription_toggle_ids' );
if(!empty($custom_related)){?>
	<div class="customrelated related related-products-wrapper product-section">
		<?php $heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Recommended for you', 'woocommerce' ) );
		if ( $heading ) :?>
			<h3 class="product-section-title container-width product-section-title-related pt-half pb-half uppercase">
				<?php echo esc_html( $heading ); ?>
			</h3>
		<?php endif; $leftvalue = 300;?>
		<div class="row large-columns-4 medium-columns-3 small-columns-2 row-small slider row-slider slider-nav-reveal slider-nav-push is-draggable flickity-enabled slider-lazy-load-active" tabindex="0">
			<div class="flickity-viewport" style="height: 399.237px; touch-action: pan-y;">
				<div class="flickity-slider" >
			
	
						<?php foreach ( $custom_related as $related_prod ) : 
							  foreach($related_prod as $custom){ 
								$product1 = wc_get_product( $custom );
								// echo $product->get_title();
								// echo $product->get_price_html();
								// echo $product->get_permalink();
								// echo $product->get_image(); ?>
								
								<div class="product-small col has-hover product type-product status-publish has-post-thumbnail product_cat-bundles product_cat-long-sleeve-tall-tees product_cat-all-products product_tag-notforestore instock sale virtual taxable purchasable product-type-bundle is-selected" style="position: absolute; left: <?php echo $leftvalue; ?>%;">
								
								<?php //wc_get_template( 'content-product.php', array( 'product' => $related_prod ) ); ?>
								
									<div class="col-inner">
										<div class="product-small box ">
											<div class="box-image">
												<div class="image-zoom">										
													<a href="<?php echo $product1->get_permalink();?>">
														<?php echo $product1->get_image(); ?>
													</a>
												</div>
												<div class="image-tools is-small top right show-on-hover">
													<?php do_action( 'flatsome_product_box_tools_top' ); ?>
												</div>
												<div class="image-tools is-small hide-for-small bottom left show-on-hover">
													<?php do_action( 'flatsome_product_box_tools_bottom' ); ?>
												</div>
												<div class="image-tools grid-tools text-center hide-for-small bottom hover-slide-in show-on-hover">
													<?php do_action( 'flatsome_product_box_actions' ); ?>			
												</div>
											</div>
											<div class="box-text box-text-products flex-row align-top grid-style-3 flex-wrap">
												<div class="title-wrapper">
													<p class="name product-title woocommerce-loop-product__title">
														<a href="<?php echo $product1->get_permalink(); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><?php echo $product1->get_title(); ?></a>
													</p>
												</div>
												<div class="price-wrapper">
													<span class="price"><span class="woocommerce-Price-amount amount"><?php echo $product1->get_price_html();?></span></span>
												</div>
											</div>
										</div>
									</div>
								
								
								</div>
								
								
								
							  <?php $leftvalue = $leftvalue+25;}
							  endforeach;?>
				</div>
			</div>	
		</div>
	</div>
<style>
.customrelated.related .product_cat-bundles .image-tools{display:block !important;}
</style>	
	
 <?php }
 
else{


// Get Type.
$type             = get_theme_mod( 'related_products', 'slider' );
$repeater_classes = array();

if ( $type == 'hidden' ) return;
if ( $type == 'grid' ) $type = 'row';

 if ( get_theme_mod('category_force_image_height' ) ) $repeater_classes[] = 'has-equal-box-heights';
 if ( get_theme_mod('equalize_product_box' ) ) $repeater_classes[] = 'equalize-box';

$repeater['type']         = $type;
$repeater['columns']      = get_theme_mod( 'related_products_pr_row', 4 );
$repeater['columns__md']  = get_theme_mod( 'related_products_pr_row_tablet', 3 );
$repeater['columns__sm']  = get_theme_mod( 'related_products_pr_row_mobile', 2 );
$repeater['class']        = implode( ' ', $repeater_classes );
$repeater['slider_style'] = 'reveal';
$repeater['row_spacing']  = 'small';


if ( $related_products ) : ?>

	<div class="related related-products-wrapper product-section">

		<?php
// CUSTOM START word change below
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Recommended for you', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h3 class="product-section-title container-width product-section-title-related pt-half pb-half uppercase">
				<?php echo esc_html( $heading ); ?>
			</h3>
		<?php endif; ?>


	<?php get_flatsome_repeater_start( $repeater ); ?>

		<?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

		<?php endforeach; ?>

		<?php get_flatsome_repeater_end( $repeater ); ?>

	</div>

	<?php
endif;

wp_reset_postdata();

}

