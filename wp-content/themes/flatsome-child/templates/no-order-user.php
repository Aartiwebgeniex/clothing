<?php
/**
 * Template Name: Users with No Orders
 */

defined('ABSPATH') || exit;

// Check if the current user has permission to view this page
if (!current_user_can('manage_woocommerce')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'your-textdomain'));
}

get_header();
?>

<div class="container">
    <h1><?php _e('Users with No Orders', 'your-textdomain'); ?></h1>
    <div>
        <p><strong><?php _e('Total Results:', 'your-textdomain'); ?></strong> <span id="total-results">Loading...</span></p>
    </div>
    <table id="no-orders-table" class="display" style="width:100%">
        <thead>
            <tr>
                <th><?php _e('Registration Date', 'your-textdomain'); ?></th>
                <th><?php _e('Email', 'your-textdomain'); ?></th>
                <th><?php _e('Username', 'your-textdomain'); ?></th>
                <th><?php _e('Order Count', 'your-textdomain'); ?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        jQuery('#no-orders-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo admin_url('admin-ajax.php'); ?>",
                "type": "POST",
                "data": {
                    "action": "fetch_no_order_users"
                }
            },
            "columns": [
                { "data": "registered" },
                { "data": "email" },
                { "data": "username" },
                { "data": "order_count" }
            ],
            "pageLength": 100,
            "paging": true,
            "serverMethod": "post",
            "drawCallback": function (settings) {
                let totalRecords = settings.json.totalRecords;
                document.getElementById('total-results').textContent = totalRecords;
            }
        });
    });
</script>

<?php
get_footer();
