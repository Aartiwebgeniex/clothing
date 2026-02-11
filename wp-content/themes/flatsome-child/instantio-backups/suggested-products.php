<?php
/**
 * Suggested Products Template for Instantio
 *
 * This template displays suggested products in the side cart
 */

defined( 'ABSPATH' ) || exit;

// $suggested_products is passed from the cart template
if ( ! isset( $suggested_products ) || ! $suggested_products || ! $suggested_products->have_posts() ) {
    return;
}

$products = $suggested_products;
?>

<div class="ins-suggested-products collapsed">
    <div class="ins-suggested-products-header">
        <h3><?php esc_html_e( 'Products you might like', 'instantio' ); ?></h3>
    </div>

    <div class="ins-suggested-products-slider ins-suggested-content">
        <?php
        while ( $products->have_posts() ) : $products->the_post();
            global $product;

            $product_permalink = $product->is_visible() ? $product->get_permalink() : '';
            $thumbnail = apply_filters( 'ins_suggested_product_thumbnail', $product->get_image( 'woocommerce_thumbnail' ), $product );
            $thumbnail = $product_permalink ? sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ) : $thumbnail;
            $product_name = $product_permalink ? sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $product->get_name() ) : $product->get_name();
            $product_price = $product->get_price_html();
        ?>
        <div class="ins-suggested-product-item">
            <div class="ins-suggested-product-image">
                <?php echo $thumbnail; ?>
            </div>
            <div class="ins-suggested-product-info">
                <h4 class="ins-suggested-product-title"><?php echo $product_name; ?></h4>
                <div class="ins-suggested-product-price"><?php echo $product_price; ?></div>
                <?php
                // Add to cart button
                woocommerce_template_loop_add_to_cart( array( 'is_ins_suggested' => 'yes' ) );
                ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php wp_reset_postdata(); ?>
