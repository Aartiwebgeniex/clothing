<?php
/* Template Name: Batch */

get_header();
?>
<?php

if (isset($_GET['cr'])) {
	if ($_GET['cr'] === 'cron-hit') {
		// This script is intended to be run as a cron job.
// It processes product reviews in batches for customers.

		// Include WordPress functions if running outside of WordPress environment
// require_once('/path/to/wp-load.php'); // Uncomment and set the correct path if needed

		global $wpdb;

		$batchcount = 10;

		// Fetch the first record where status is not 'completed'
		$latest_record = $wpdb->get_row(
			"SELECT * FROM {$wpdb->prefix}product_review_batch WHERE status != 'completed' ORDER BY id ASC LIMIT 1"
		);

		if ($latest_record) {
			// Extract variables from the record
			$product_id        = intval($latest_record->product_id);
			$customer_id       = intval($latest_record->customer_id);
			$last_position     = $latest_record->last_position;
			$group_ids         = array_map('intval', explode(',', $latest_record->group_ids));
			$previous_rating   = intval($latest_record->rating);
			$previous_message  = sanitize_text_field($latest_record->message);
			$parent_review_id  = isset($latest_record->review_id) ? intval($latest_record->review_id) : '';
			$parent_comment_id = isset($latest_record->comment_id) ? intval($latest_record->comment_id) : '';



			// Ensure last_position is valid
			$position = array_search($last_position, $group_ids);

			if ($position === false) {
				// If last_position is not found or empty, start from the beginning
				$position = -1;
			}

			// Get the next batch of product IDs
			$next_batch = array_slice($group_ids, $position + 1, $batchcount);

			if (!empty($next_batch)) {
				// Update last_position to the last ID in the next batch
				$next_batch_last_value = end($next_batch);

				$wpdb->update(
					$wpdb->prefix . 'product_review_batch',
					['last_position' => $next_batch_last_value],
					['id' => $latest_record->id],
					['%d'],
					['%d']
				);

				if(!$customer_id){
					$customer_id = 78771;
				}
				// Submit reviews for each product ID in the next batch
				foreach ($next_batch as $next_product_id) {

					submit_review($next_product_id, $previous_rating, $previous_message, $customer_id, 2, $parent_review_id, $parent_comment_id);


				}


				// Check for remaining product IDs after the current batch
				$next_position    = array_search($next_batch_last_value, $group_ids);
				$remaining_values = array_slice($group_ids, $next_position + 1);

				if (!empty($remaining_values)) {
					// Set status to 'pending' if there are more products to process
					$wpdb->update(
						$wpdb->prefix . 'product_review_batch',
						['status' => 'pending'],
						['id' => $latest_record->id],
						['%s'],
						['%d']
					);
				} else {
					// Set status to 'completed' and reset last_position if all products are processed
					$wpdb->update(
						$wpdb->prefix . 'product_review_batch',
						['status' => 'completed', 'last_position' => 0],
						['id' => $latest_record->id],
						['%s', '%d'],
						['%d']
					);
				}
			} else {
				// No more batches available; set status to 'completed'
				$wpdb->update(
					$wpdb->prefix . 'product_review_batch',
					['status' => 'completed', 'last_position' => 0],
					['id' => $latest_record->id],
					['%s', '%d'],
					['%d']
				);
			}
		} else {
			// No records found with status not 'completed'
			echo "No pending batches to process.";
		}

		?>


<?php

	}
}
get_footer();
?>