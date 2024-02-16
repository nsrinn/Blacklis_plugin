<?php

?>
<div class="wrap">
    <h1>Blacklist Customers</h1>
    <div class="">
        <a href="<?php echo admin_url('admin.php?page=blacklist_sections_new_bl_add'); ?>" class="page-title-action">Add New</a>
        <!-- <a href="<?php echo admin_url('admin.php?page=blacklist_sections_new_bl_add'); ?>" class="page-title-action">Add New</a> -->

    </div>


    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'blacklisted_customers';
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if (!empty($results)) {
    ?>
        <div class="wrap">

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Country</th>
                        <th>Street Address</th>
                        <th>Town/City</th>
                        <th>State</th>
                        <th>Pin Code</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th colspan="2"> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $customer) : ?>
                        <tr>
                            <td><?php echo esc_html($customer['first_name']); ?></td>
                            <td><?php echo esc_html($customer['last_name']); ?></td>
                            <td><?php echo esc_html($customer['country']); ?></td>
                            <td><?php echo esc_html($customer['street_address']); ?></td>
                            <td><?php echo esc_html($customer['town_city']); ?></td>
                            <td><?php echo esc_html($customer['state']); ?></td>
                            <td><?php echo esc_html($customer['pin_code']); ?></td>
                            <td><?php echo esc_html($customer['phone']); ?></td>
                            <td><?php echo esc_html($customer['email']); ?></td>
                            <td>
                                <?php
                                $edit_url = admin_url('admin.php?page=blacklist_sections_edit&id=' . $customer['id']);
                                // echo "Edit URL: $edit_url";
                                ?>
                                <button class="btn btn-primary"><a href="<?php echo $edit_url; ?>">Edit </a></button>
                                

                            </td>

                            <td>
                                <a href="<?php echo admin_url('admin.php?page=blacklist_sections_bl_view&action=delete&user_id=' . $customer['id']); ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                <?php
                                global $wpdb;

                                if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['user_id'])) {
                                    $user_id = intval($_GET['user_id']);

                                    // Perform delete operation
                                    $table_name = $wpdb->prefix . 'blacklisted_customers';
                                    $wpdb->delete($table_name, array('ID' => $user_id));

                                    // Redirect back to the datatable page after deletion
                                    wp_redirect(admin_url('admin.php?page=blacklist_sections_bl_view'));
                                    exit;
                                }

                                ?>
                            </td>




                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php
    } else {
        echo '<p>No blacklisted customers found.</p>';
    }

    ?>
</div>