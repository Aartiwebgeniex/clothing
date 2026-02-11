<?php
/* Template Name: TEMP ENABEL Review Submission */

get_header();

 ?>
 
<?php
// Add this to your theme's functions.php or a custom plugin

function trigger_save_for_products_in_batches($batch_size = 50) {
    // Get the last processed product ID from the options table
    $last_processed_id = get_option('last_processed_product_id', 0);

    // Arguments for fetching products
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'ID',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_enable_reviews',
                'value' => '1',
                'compare' => '='
            )
        ),
        'post__not_in' => array($last_processed_id),
    );

    $products = get_posts($args);

    if (empty($products)) {
        echo "No more products to process.";
        return;
    }

    foreach ($products as $product) {
        $product_id = $product->ID;
        $product_obj = wc_get_product($product_id);

        if ($product_obj) {
            // Ensure reviews are allowed and trigger save action
            $product_obj->set_reviews_allowed(true);
            $product_obj->save();
            echo "Processed Product ID: $product_id<br>";

            // Update the last processed product ID
            $last_processed_id = $product_id;
            update_option('last_processed_product_id', $last_processed_id);
        } else {
            echo "Failed to retrieve Product ID: $product_id<br>";
        }
    }

    echo "Batch processing complete. Reload the page to continue.";
}

// Run the function
trigger_save_for_products_in_batches();

?>

<?php
get_footer();
?>