<?php
/* Template Name: Review Group Mapping */

get_header();

if (isset($_GET['cr'])) {
	if ($_GET['cr'] === 'cron-hit') {

		// Define the number of posts per batch
		$posts_per_page = 10; // Set your desired batch size

		// Retrieve and declare the last processed post ID as a global variable
		global $last_processed_id;
		$last_processed_id = get_option('last_processed_post_id', 0);

		// Debug: Display last processed ID
		echo '<p>Last processed post ID: ' . esc_html($last_processed_id) . '</p>';

		// Get the total count of reviews waiting to be processed for debugging purposes
		$total_reviews_query = new WP_Query([
			'post_type'      => 'ywar_reviews',
			'post_status'    => 'ywar-approved',
			'posts_per_page' => -1, // Get all posts to count them
			'orderby'        => 'ID',
			'order'          => 'ASC',
		]);

		$total_reviews_count = $total_reviews_query->found_posts;

		// Debug: Display total review count
		echo '<p>Total reviews count: ' . esc_html($total_reviews_count) . '</p>';

		// Prepare query arguments to fetch the next batch of posts
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
			],
		];


		// Define the function to filter the WHERE clause
		function filter_where_last_processed_id($where)
		{
			global $last_processed_id, $wpdb;
			if ($last_processed_id > 0) {
				// Ensure the ID comparison is correct
				$where .= $wpdb->prepare(" AND {$wpdb->posts}.ID > %d", $last_processed_id);
			}
			return $where;
		}

		// Add the filter before running the query
		add_filter('posts_where', 'filter_where_last_processed_id');

		// Run the query to get the next batch of posts
		$review_query = new WP_Query($query_args);

		// Remove the filter after the query to avoid affecting other queries
		remove_filter('posts_where', 'filter_where_last_processed_id');

		if ($review_query->have_posts()) {

			$post_ids = $review_query->posts;

			// Debug: Display batch count
			echo '<p>Number of posts fetched in this batch: ' . esc_html(count($post_ids)) . '</p>';

			// Initialize a flag to check if any posts were processed
			$any_processed = false;

			// If there are posts to process
			if (!empty($post_ids)) {
				// Process each post
				foreach ($post_ids as $post_id) {
					$review_post = get_post($post_id);

					if ($review_post && $review_post->post_type === 'ywar_reviews') {
						// Get review details
						$review_id      = $review_post->ID;
						$review_content = $review_post->post_content;
						$review_title   = get_the_title($review_id);
						$review_rating  = get_post_meta($review_id, '_ywar_rating', true);
						$review_email   = get_post_meta($review_id, '_ywar_review_author_email', true);
						$post_status    = get_post_status($review_id);

						// Get user ID from email
						$user           = get_user_by('email', $review_email);
						$review_user_id = !empty($user->ID) ? $user->ID : (get_post_meta($review_id, '_ywar_review_user_id', true) ?: '');

						// Get associated product ID
						$product_id        = get_post_meta($review_id, '_ywar_product_id', true);
						$parent_review_id  = get_post_meta($review_id, 'parent_review_id', true);
						$parent_comment_id = get_post_meta($review_id, 'parent_comment_id', true);

						// Check if the product exists and is not in trash
						$product = wc_get_product($product_id);
						if (!$product || 'trash' === get_post_status($product_id)) {
							echo "Product ID " . esc_html($product_id) . " is invalid or in trash. Skipping.<br>";
							continue; // Skip this review if the product is invalid
						}

						// Prepare data for display
						$output = [
							'Review ID'         => $review_id,
							'Review Title'      => $review_title,
							'Review Content'    => $review_content,
							'Review Email'      => $review_email,
							'Review User ID'    => $review_user_id,
							'Rating'            => $review_rating,
							'Product ID'        => $product_id,
							'Parent Review ID'  => $parent_review_id,
							'Parent Comment ID' => $parent_comment_id
						];

						// Display output using <pre> tag for formatting
						echo '<pre>' . print_r($output, true) . '</pre>';



						$product_groups     = get_field('re_product_groups', 'option');
						$merged_product_ids = [];

						if ($product_groups) {
							foreach ($product_groups as $group) {
								$group_name  = sanitize_text_field($group['product_group_name']); // Replace with actual field name
								$product_ids = array_map('intval', explode(',', $group['product_ids'])); // Convert IDs to an array

								// Check if the current product ID exists in the product_ids array
								if (in_array($product_id, $product_ids)) {
									// Exclude the current product_id from the product_ids array
									$filtered_product_ids = array_diff($product_ids, [$product_id]);

									// Merge the filtered product_ids into the merged array
									$merged_product_ids = array_merge($merged_product_ids, $filtered_product_ids);
								}
							}

							// Remove duplicates from the merged product IDs array
							$merged_product_ids = array_unique($merged_product_ids);

							// Convert the array of IDs to a comma-separated string
							$comma_separated_product_ids = implode(',', $merged_product_ids);
						}



						global $wpdb;

						// Prepare the table name
						$table_name = $wpdb->prefix . 'product_review_batch';

						// Check if the combination exists
						$exists = $wpdb->get_var($wpdb->prepare(
							"SELECT COUNT(*) FROM $table_name WHERE product_id = %d AND customer_id = %d",
							$product_id,
							$review_user_id
						));

						if (!$exists) {
							// Insert data
							$data = array(
								'product_id'    => $product_id,
								'customer_id'   => $review_user_id,
								'flag'          => 2,
								'rating'        => $review_rating,
								'message'       => $review_content,
								'group_ids'     => isset($comma_separated_product_ids) ? $comma_separated_product_ids : '',
								'last_position' => 0,
								'status'        => 'pending',
								'review_id'     => $parent_review_id,
								'comment_id'    => $parent_comment_id
							);

							$format = array(
								'%d', // product_id
								'%d', // customer_id
								'%d', // flag
								'%d', // rating
								'%s', // message
								'%s', // group_ids
								'%d', // last_position
								'%s', // status
								'%d', // review_id
								'%d'  // comment_id
							);

							$inserted = $wpdb->insert($table_name, $data, $format);

							if ($inserted) {
								echo 'Data inserted successfully.<br>';
							} else {
								// Display the specific error message
								echo 'Insert failed: ' . $wpdb->last_error . '<br>';
							}

						} else {
							echo 'The combination of product_id and customer_id already exists.<br>';
						}

						// Mark that at least one post was processed
						$any_processed = true;
					}
				}

				// Update the last processed post ID in the database after processing the entire batch
				$new_last_id = end($post_ids);
				update_option('last_processed_post_id', $new_last_id);
				echo '<p>Updated last_processed_post_id to: ' . esc_html($new_last_id) . '</p>';

				if (!$any_processed) {
					echo '<p>No valid posts processed in this batch.</p>';
				}
			} else {
				// No more posts to process
				echo "<p>All posts have been processed.</p>";
				// Optionally, delete the option if you no longer need to track progress
				// delete_option('last_processed_post_id');
			}
		} else {
			// No more posts to process
			echo "<p>All posts have been processed.</p>";
			// Optionally, delete the option if you no longer need to track progress
			// delete_option('last_processed_post_id');
		}

		// Reset post data
		wp_reset_postdata();

	}
}else{
	echo "Access denied";
}


get_footer();