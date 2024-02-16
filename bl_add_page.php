<?php
// bl_add_page.php

// Table creation on plugin activation
function create_blacklist_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'blacklisted_customers';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_name varchar(255) NOT NULL,
        last_name varchar(255) NOT NULL,
        country varchar(255) NOT NULL,
        street_address varchar(255) NOT NULL,
        town_city varchar(255) NOT NULL,
        state varchar(255) NOT NULL,
        pin_code varchar(20) NOT NULL,
        phone varchar(20) NOT NULL,
        email varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_activation_hook(__FILE__, 'create_blacklist_table');

// Enqueue scripts and styles
function enqueue_blacklistcustomer_scripts() {
    wp_enqueue_script('blacklist-script', plugins_url('/js/blacklistcustomer.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_blacklistcustomer_scripts');

function enqueue_blacklistcustomer_styles() {
    wp_enqueue_style('blacklist-style', plugins_url('/css/blacklistcustomer.css', __FILE__), array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_blacklistcustomer_styles');

// Form processing logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'blacklisted_customers';

    $first_name = sanitize_text_field($_POST['FirstName']);
    $last_name = sanitize_text_field($_POST['lastName']);
    $country = sanitize_text_field($_POST['Country']);
    $street_address = sanitize_text_field(implode(', ', $_POST['StreetAddress']));
    $town_city = sanitize_text_field($_POST['town_city']);
    $state = sanitize_text_field($_POST['state']);
    $pin_code = sanitize_text_field($_POST['pin_code']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_text_field($_POST['email']);

    $wpdb->insert(
        $table_name,
        array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'country' => $country,
            'street_address' => $street_address,
            'town_city' => $town_city,
            'state' => $state,
            'pin_code' => $pin_code,
            'phone' => $phone,
            'email' => $email,
        )
    );
}

// HTML for the form
?>
<!-- Your form HTML goes here -->

<div class="wrap">
    <h1 class="wp-heading-inline">Add New Blacklisted Customer</h1>

    <!-- Your form HTML goes here -->
    <form method="post" action="">
        <!-- Add your form fields and submit button here -->
        <div class="form-group">
            <label for="FirstName">First Name:</label>
            <input type="text" name="FirstName" id="FirstName">
        </div>
        <br>
        <!-- Add more form fields as needed -->
        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" id="lastName">
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
                    echo '<option value="' . esc_attr($code) . '">' . esc_html($name) . '</option>';
                }
                ?>
            </select>
        </div>
        <br>
        <div class="form-group">
            <label for="StreetAddress">Street Address:</label>
            <input type="text" name="StreetAddress[]" id="StreetAddress1" placeholder="Street Address Line 1" required>
            <input type="text" name="StreetAddress[]" id="StreetAddress2" placeholder="Street Address Line 2">
        </div>

        <br>


        <div class="form-group">
            <label for="town_city">Town/City:</label>
            <input type="text" name="town_city" id="town_city">
        </div>
        <br>

        <div class="form-group">
            <label for="State">State:</label>
            <input type="text" name="state" id="state">

            <!-- <select name="State" id="State" class="state-select"> -->
                <!-- States will be dynamically populated based on the selected country -->
            <!-- </select> -->
        </div>
        <br>

        <div class="form-group">
            <label for="pin_code">PIN Code:</label>
            <input type="text" name="pin_code" id="pin_code">
        </div>
        <br>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" name="phone" id="phone">
        </div>
        <br>
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email">
        </div>
        <br>






        <button type="submit">Submit</button>
    </form>
</div>