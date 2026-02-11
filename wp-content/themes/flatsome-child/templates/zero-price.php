<?php
/**
 * Template Name: Zero Price Products
 */
get_header();
?>

<div class="container">
    <h1>Products with Zero Price</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Edit URL</th>
                <th>Front-end URL</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        
        $products = new WP_Query($args);
        $counter = 1;

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product = wc_get_product(get_the_ID());

                if ($product->is_type('variable')) {
                    $variations = $product->get_available_variations();
                    foreach ($variations as $variation) {
                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);
                        if ($variation_obj->get_price() == 0 && $variation_obj->is_in_stock()) {
                            echo '<tr>';
                            echo '<td>' . $counter . '</td>';
                            echo '<td>' . $variation_id . '</td>';
                            echo '<td>' . $variation_obj->get_name() . '</td>';
                            echo '<td><a href="' . get_edit_post_link($variation_id) . '">Edit</a></td>';
                            echo '<td><a href="' . get_permalink($variation_id) . '">View</a></td>';
                            echo '</tr>';
                            $counter++;
                        }
                    }
                } else {
                    if ($product->get_price() == 0 && $product->is_in_stock()) {
                        echo '<tr>';
                        echo '<td>' . $counter . '</td>';
                        echo '<td>' . $product->get_id() . '</td>';
                        echo '<td>' . $product->get_name() . '</td>';
                        echo '<td><a href="' . get_edit_post_link($product->get_id()) . '">Edit</a></td>';
                        echo '<td><a href="' . get_permalink($product->get_id()) . '">View</a></td>';
                        echo '</tr>';
                        $counter++;
                    }
                }
            }
        } else {
            echo '<tr><td colspan="5">No products found.</td></tr>';
        }
        wp_reset_postdata();
        ?>
        </tbody>
    </table>
</div>

<?php get_footer(); ?>
