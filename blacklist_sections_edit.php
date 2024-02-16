<?php
// bl_edit_page.php

// Include WordPress core functionality
// require_once('../../../wp-load.php');

// Check if user is logged in and has appropriate capabilities
if (!current_user_can('manage_options')) {
    wp_die(__('Sorry, you do not have permission to access this page.'));
}

// Get customer ID from the URL
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Retrieve customer data from the database
global $wpdb;
$table_name = $wpdb->prefix . 'blacklisted_customers';
$customer = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $customer_id), ARRAY_A);

// Check if customer exists
if (!$customer) {
    wp_die(__('Sorry, the requested customer does not exist.'));
}


// Process form data when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and update customer data
    $updated_data = array(
        'first_name'      => sanitize_text_field($_POST['FirstName']),
        'last_name'       => sanitize_text_field($_POST['lastName']),
        'country'         => sanitize_text_field($_POST['Country']),
        'street_address'  => sanitize_text_field(implode(', ', $_POST['StreetAddress'])),
        'town_city'       => sanitize_text_field($_POST['town_city']),
        'state'           => sanitize_text_field($_POST['state']),
        'pin_code'        => sanitize_text_field($_POST['pin_code']),
        'phone'           => sanitize_text_field($_POST['phone']),
        'email'           => sanitize_text_field($_POST['email']),
    );

    $wpdb->update(
        $table_name,
        $updated_data,
        array('id' => $customer_id)
    );

    // Optionally, you can redirect the user to a success page or perform other actions.
    // Redirect to the blacklist_sections_bl_view slug after updating the customer
wp_redirect(admin_url('admin.php?page=blacklist_sections_bl_view&success=1'));
exit;

}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Edit Blacklisted Customer</h1>

    <form method="post" action="">
        <!-- Populate form fields with existing customer data -->
        <div class="form-group">
            <label for="FirstName">First Name:</label>
            <input type="text" name="FirstName" id="FirstName" value="<?php echo esc_attr($customer['first_name']); ?>">
        </div>
        <br>
        <!-- Add more form fields as needed -->
        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" id="lastName" value="<?php echo esc_attr($customer['last_name']); ?>">
        </div>
        <br>
        <div class="form-group">
            <label for="Country">Country:</label>
            <select name="Country" id="Country" class="country-select">
                <?php
                // Array of countries (you can replace this with your own array or fetch from a database)
                $countries = array(
                    'USA' => 'United States',
                    'CAN' => 'Canada',
                    'GBR' => 'United Kingdom',
                    'AUS' => 'Australia',
                    'IND' => 'India',
                    // Add more countries as needed
                );
                // $countries = wp_get_countries();

                // Loop through the countries and create options
                foreach ($countries as $code => $name) {
                    $selected = ($code == esc_attr($customer['country'])) ? 'selected="selected"' : '';
                    echo '<option value="' . esc_attr($code) . '" ' . $selected . '>' . esc_html($name) . '</option>';
                }
                ?>
            </select>
        </div>

        <br>
        <div class="form-group">
            <label for="StreetAddress">Street Address:</label>
            <input type="text" name="StreetAddress[]" id="StreetAddress1" placeholder="Street Address Line 1" value="<?php echo esc_attr($customer['street_address']); ?>" required>
            <input type="text" name="StreetAddress[]" id="StreetAddress2" placeholder="Street Address Line 2" value="<?php echo esc_attr($customer['street_address']); ?>">
        </div>


        <br>


        <div class="form-group">
            <label for="town_city">Town/City:</label>
            <input type="text" name="town_city" id="town_city" value="<?php echo esc_attr($customer['town_city']); ?>">
        </div>
        <br>

        <div class="form-group">
            <label for="State">State:</label>
            <input type="text" name="state" id="state" value="<?php echo esc_attr($customer['state']); ?>">

            <!-- <select name="State" id="State" class="state-select"> -->
            <!-- States will be dynamically populated based on the selected country -->
            <!-- </select> -->
        </div>
        <br>

        <div class="form-group">
            <label for="pin_code">PIN Code:</label>
            <input type="text" name="pin_code" id="pin_code" value="<?php echo esc_attr($customer['pin_code']); ?>">
        </div>
        <br>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" name="phone" id="phone" value="<?php echo esc_attr($customer['phone']); ?>">
        </div>
        <br>
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" value="<?php echo esc_attr($customer['email']); ?>">
        </div>
        <br>
        <!-- Add more form fields as needed -->

        <button type="submit">Update</button>
    </form>
</div>