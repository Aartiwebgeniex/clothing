<?php
/*
Template Name: Update Quantity
*/

if (isset($_GET['crun'])) {
    $csv = array();
    $filename = get_stylesheet_directory() . '/attachment/stock.csv';
    if (($handle = fopen($filename, "r+")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $csv[] = array('ProductCode' => $data[0], 'StockonHand' => $data[1], 'Updated' => isset($data[2]) ? $data[2] : '');
        }
        fclose($handle);
    }

    $batchSize = 100; // Number of records to process per batch
    $counter = 0; // Initialize counter variable

    // Filter the CSV array to include only the SKUs without the '1' flag and that are not empty
    $filteredCSV = array_filter($csv, function ($item, $index) {
        // Exclude the header row from modification
        return $index > 0 && $item['Updated'] !== '1' && !empty($item['ProductCode']);
    }, ARRAY_FILTER_USE_BOTH);

    foreach ($filteredCSV as $index => $single) {
        // Process only if the counter is within the batch size limit
        if ($counter < $batchSize) {
            global $wpdb;
            $sql = $wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value = %s",
                $single['ProductCode']
            );
            $results = $wpdb->get_results($sql);
            $newarray = wp_list_pluck($results, 'post_id');

            foreach ($newarray as $id) {
                if (!$id) {
                    continue;
                }
                $product = wc_get_product($id);
                if (!$product) {
                    continue;
                }
                $product->set_stock_quantity($single['StockonHand']);
                $product->save();
            }

            // Update 'Updated' flag to 1 for the processed product in the original CSV array
            $key = array_search($single['ProductCode'], array_column($csv, 'ProductCode'));
            if ($key !== false) {
                $csv[$key]['Updated'] = '1';
            }
        } else {
            break; // Exit the loop once the batch size limit is reached
        }

        $counter++; // Increment the counter variable
    }

    // Update the CSV file with the 'Updated' flag
    if (($handle = fopen($filename, "w")) !== FALSE) {
        foreach ($csv as $data) {
            fputcsv($handle, $data);
        }
        fclose($handle);
    }
}

echo "Access denied.";
?>