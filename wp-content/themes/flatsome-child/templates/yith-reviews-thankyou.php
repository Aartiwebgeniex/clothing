<?php
/* Template Name: Custom Review Submission */

get_header();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .thank-you-container,
    .review-form-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
        background-color: #f9f9f9;
        padding: 20px;
    }

    .thank-you-card,
    .review-form-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        display: flex;
        gap: 25px;
    }

    .review-form-details form .label-rating {
        display: flex;
        align-items: center;
    }

    .review-form-details label {
        font-size: 16px;
        color: #666;
        margin: 5px 0;
        font-weight: bolder;
    }

    .review-form-card .review-info {
        width: 350px;
        text-align: center;
        gap: 10px;
        display: flex;
        flex-direction: column;
    }
    .review-form-card .review-form-details {
        width: 100%;
    }

    .review-form-card .review-info img {
        width: 100%;
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin-right: 0px;
    }

    .star_rating input {
        cursor: pointer;
        position: relative;
        width: 19px;
        height: 19px;
        line-height: 1em;
        margin: 0px;
    }
    .star_rating input:before {
        content: "";
        background-color: white;
    }
    .star_rating input:before, .star_rating input:after {
        display: block;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        position: absolute;
    }
    .star_rating input:checked:after, .star_rating input:checked ~ input:after {
        color: #B7995B;
        -webkit-animation: 0.5s dance infinite;
        animation: 0.5s dance infinite;
    }
    .star_rating input:after {
        content: "\f005";
        font-family: FontAwesome;
        color: #eee;
        transition: color 0.2s;
    }
    .star_rating:before, .woocommerce-page .star_rating:before{
        display: none;
    }
    .star_rating {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-direction: row-reverse;
        justify-content: start;
    }
    .review-form-card {
        width: 800px;
    }
    .loader {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .loader-button {
        display: flex;
        align-items: center;
    }
    .loader-button button {
        margin-bottom: 0px;
        margin-right: 15px;
        background: #9E3333;
        color: #fff;
    }
    .loader-button button:hover{
        box-shadow: inset 0 0 0 100px rgba(0,0,0,.2);
    }

    .thank-you-card img {
        max-width: 150px;
        height: auto;
        border-radius: 10px;
        margin-right: 20px;
    }

    .thank-you-card h1,
    .review-form-card h1 {
        margin: 0 0 10px;
        font-size: 24px;
        color: #333;
        font-weight: bold;
    }

    .thank-you-card p,
    .review-form-card p {
        font-size: 16px;
        color: #666;
        margin: 5px 0;
    }

    .thank-you-card p.confirmation {
        font-weight: bold;
        color: green;
        font-size: 20px;
    }

    .thank-you-details,
    .review-form-details {
        display: flex;
        flex-direction: column;
    }

    .thank-you-details .icon-star {
        color: #B7995B;
        margin: 0 2px;
    }

    @media (max-width: 768px) {

        .thank-you-card,
        .review-form-card {
            flex-direction: column;
            text-align: center;
        }

        .thank-you-card img, .review-form-card img {
            margin: 0 0 0px;
            max-width: 100%;
            width: 100%;
        }
        .review-form-card .review-info {
            width: 100%;
        }
        .star_rating, .loader-button {
            justify-content: center;
        }
    }

    .rating {
        display: flex;
        gap: 5px;
    }


    .rating label {
        cursor: pointer;
        font-size: 24px;
    }

    .rating input:checked~label {
        color: gold;
    }
    .error-card {
    margin: 100px auto;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
}
#main, #wrapper {
    background-color: #f9f9f9;

}

</style>

<?php
// Function to decrypt base64 encoded IDs

// Check if the necessary parameters are set in the URL
if (isset($_GET['r'])) {
    $hash                           = $_GET['r'];
    $decoded     = base64_decode($hash);
    list($customer_id, $product_id) = explode('|', $decoded);

    $customer_id = $customer_id;
    $product_id  = $product_id;

    // $customer_id  = intval(decrypt_base64($customer_id_encrypted));
    //$product_id   = intval(decrypt_base64($product_id_encrypted));
    $productArray = wc_get_product($product_id);
    if ($productArray) {
        // Ensure the customer is valid
        $customer = get_user_by('ID', $customer_id);

        if ($customer) {
            // Check if the customer has already reviewed this product
            $existing_reviews = new WP_Query([
                'post_type'  => 'ywar_reviews',
                'meta_query' => [
                    [
                        'key'   => '_ywar_product_id',
                        'value' => $product_id,
                    ],
                    [
                        'key'   => '_ywar_review_user_id',
                        'value' => $customer_id,
                    ],
                ],
            ]);

            if ($existing_reviews->have_posts()) {
                $existing_reviews->the_post();
                $previous_rating    = get_post_meta(get_the_ID(), '_ywar_rating', true);
                $message_to_display = 'You have already reviewed this product.' . "<br>" . 'Your previous rating: ' . str_repeat('<i class="icon-star"></i>', $previous_rating);

            } else {
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'], $_POST['message'])) {
                    $rating  = intval($_POST['rating']);
                    $message = sanitize_text_field($_POST['message']);

                    // Determine the review status based on the rating
                    $status = ($rating > 3) ? 'ywar-approved' : 'ywar-pending';

                    // Insert the review
                    $review_data = [
                        'post_title'   => 'Review by ' . $customer->display_name,
                        'post_content' => $message,
                        'post_status'  => $status,
                        'post_author'  => $customer_id,
                        'post_type'    => 'ywar_reviews',
                        'meta_input'   => [
                            '_ywar_product_id'                  => $product_id,
                            '_ywar_review_user_id'              => $customer_id,
                            '_ywar_rating'                      => $rating,
                            '_ywar_review_author'               => $customer->display_name,
                            '_ywar_review_author_email'         => $customer->user_email,
                            '_ywar_votes'                       => [],
                            '_ywar_upvotes_count'               => 0,
                            '_ywar_downvotes_count'             => 0,
                            '_ywar_inappropriate_list'          => [],
                            '_ywar_inappropriate_count'         => 0,
                            '_ywar_helpful'                     => 0,
                            '_ywar_featured'                    => 'no',
                            '_ywar_stop_reply'                  => 'no',
                            '_ywar_in_reply_of'                 => 0,
                            '_ywar_review_author_custom_avatar' => '',
                            '_ywar_review_author_IP'            => $_SERVER['REMOTE_ADDR'],
                            '_ywar_review_edit_blocked'         => 'no',
                            '_ywar_thumb_ids'                   => [],
                            '_ywar_guest_cookie'                => '',
                        ],
                    ];

                    $review_id          = wp_insert_post($review_data);
                    $message_to_display = $review_id && !is_wp_error($review_id) ? 'Thank you for your review!' : 'There was an error submitting your review. Please try again.';

                    // Insert the review into the comments table if successful
                    if ($review_id && !is_wp_error($review_id)) {
                        $comment_data = [
                            'comment_post_ID'      => $product_id,
                            'comment_author'       => $customer->display_name,
                            'comment_author_email' => $customer->user_email,
                            'comment_content'      => $message,
                            'comment_approved'     => ($rating > 3) ? 1 : 0,
                            'comment_type'         => 'review',
                            'user_id'              => $customer_id,
                        ];

                        $comment_id = wp_insert_comment($comment_data);

                        if (!is_wp_error($comment_id)) {
                            add_comment_meta($comment_id, 'rating', $rating);
                        }

                        // Create an instance of the YITH_YWAR_Review class
                        $review = new YITH_YWAR_Review($review_id);

                        // Trigger actions to update stats
                        do_action('yith_ywar_review_created', $review);
                        yith_ywar_get_review_stats($productArray, true);
                    }
                } else {
                    // Display the form
                    ?>
                <div class="review-form-container">
                    <div class="review-form-card">
                        <div class="review-info">
                            <?php echo $productArray ? $productArray->get_image() : ''; ?>
                            <p><strong>Product:</strong> <?php echo $productArray->get_name(); ?></p>
                        </div>
                        <div class="review-form-details">
                            <h1>Submit your review</h1>
                            <p><strong>Name:</strong> <?php echo esc_attr($customer->display_name); ?></p>
                            <form method="POST">


                                <div class="label-rating">
                                    <label for="rating">Rating:&nbsp;</label>
                                    <span class="star_rating">
                                        <input type="radio" name="rating" value="5" id="star1">
                                        <input type="radio" name="rating" value="4" id="star2">
                                        <input type="radio" name="rating" value="3" id="star3">
                                        <input type="radio" name="rating" value="2" id="star4">
                                        <input type="radio" name="rating" value="1" id="star5">
                                    </span>
                                </div>
                                <p>
                                    <label for="message">Review:</label>
                                    <textarea id="message" name="message" rows="4"></textarea>
                                </p>
                                <div class="loader-button">
                                    <button type="submit">Submit Review</button>
                                  <!--  <div class="loader"></div>-->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                }
            }
        } else {
            $message_to_display = 'Invalid customer ID.';
        }
    }else{
        $message_to_display = 'Missing required information.';
       

    }

?>

<?php if (isset($message_to_display)): ?>
    <div class="thank-you-container">
        <div class="thank-you-card">
            <?php echo $productArray ? $productArray->get_image() : ''; ?>
            <div class="thank-you-details">
                <h1><?php echo $message_to_display; ?></h1>
                <?php if ($productArray): ?>
                    <p><strong>Product:</strong> <?php echo $productArray->get_name(); ?></p>

                    <?php if ($message_to_display === 'Thank you for your review!'): ?>
                        <p><strong>Rating: </strong> <?php echo str_repeat('<i class="icon-star"></i>', $rating); ?></p>
                        <p><strong>Message: </strong> <?php echo nl2br($message); ?></p>
                        <p class="confirmation">Your review has been submitted.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
} else {
    $message_to_display = 'Missing required information.';
    echo "<div class='error-card'>".$message_to_display."</div>";
}
?>

<div class="container section-title-container">
<h1 class="section-title section-title-center"><b></b><span class="section-title-main">OUR FAVOURITES</span><b></b></h1>
</div>
<?php 
echo do_shortcode('[featured_products per_page="4" orderby="date" order="desc"]');
?>
</div>





<?php
get_footer();
?>