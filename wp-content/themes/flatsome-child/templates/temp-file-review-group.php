<?php
/* Template Name: TEMP Review Group Mapping */

get_header();

$query_args = [
    'post_type'      => 'ywar_reviews',
    'post_status'    => 'ywar-approved',
    'posts_per_page' => $posts_per_page,
    'orderby'        => 'ID',
    'order'          => 'ASC',
    'fields'         => 'ids', // Only get post IDs to improve performance
    'meta_query'     => [
        [
            'key'     => '_ywar_product_id', // This is the custom field key
            'compare' => 'EXISTS',           // Ensure it exists
        ],
        [
            'key'     => '_ywar_review_user_id', // Check for user ID
            'value'   => 0,                     // User ID should be 0
            'compare' => '=',                   // Match exactly
        ],
    ],
];


$query = new WP_Query($query_args);

if ($query->have_posts()) {
    echo '<table>';
    echo '<thead><tr><th>Product ID</th><th>User ID</th><th>Review ID</th><th>Link</th></tr></thead>';
    echo '<tbody>';

    foreach ($query->posts as $review_id) {
        $product_id = get_post_meta($review_id, '_ywar_product_id', true);
        $user_id    = get_post_meta($review_id, '_ywar_review_user_id', true);

        echo '<tr>';
        echo '<td>' . esc_html($product_id) . '</td>';
        echo '<td>' . esc_html($user_id) . '</td>';
        echo '<td>' . esc_html($review_id) . '</td>';
        echo '<td><a href="' . esc_url(admin_url('post.php?action=edit&post=' . $review_id)) . '">Edit</a></td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo 'No reviews found.';
}


get_footer();