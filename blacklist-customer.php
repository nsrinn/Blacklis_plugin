<?php 
/**
 
 * Plugin Name:       Blacklist Customer
 * Plugin URI:        https://github.com/nsrinn/plugin-blacklist-customer
 * Description:       Blacklist Customer
 * Version:           1.1.
 * Author:            Nasrin
 * Author URI:         https://github.com/nsrinn
 */

// Enqueue scripts and styles
// include_once(plugin_dir_path(__FILE__) . 'js/blacklistcustomer.js');
// include_once(plugin_dir_path(__FILE__) . 'css/blacklistcustomer.css');

// function blc_enqueue_scripts()
// {
//     // Register and enqueue styles
//     // wp_register_style('stylesheet', get_stylesheet_uri(), [], filemtime(get_template_directory() . '/style.css'), 'all');
//     wp_register_style('bootstrap-style', get_template_directory_uri() . '/css/bootstrap.min.css', [], false, 'all'); //true means on footer section if false then on header section 


//     wp_enqueue_style('stylesheet');
//     wp_enqueue_style('bootstrap-style');


//     // Register and enqueue scripts
//     // wp_register_script('jsfile', get_template_directory_uri() . '/js/blacklistcustomers.js', [], filemtime(get_template_directory() . '/js/blacklistcustomers.js'), true);
//     wp_register_script('bootstrap-jsfile', get_template_directory_uri() . '/js/bootstrap.min.js', ['jquery'], false, true); 


//     wp_enqueue_script('jsfile');
//     wp_enqueue_script('bootstrap-jsfile');
// }



// add_action('wp_enqueue_scripts', 'blc_enqueue_scripts');
// Your plugin code...

// Define your plugin functionality, classes, functions, etc.

// Enqueue Bootstrap
function enqueue_bootstrap() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');

    // Enqueue Bootstrap JavaScript
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');



add_filter('manage_woocommerce_page_wc-orders_columns', 'add_wc_order_list_custom_column');
function add_wc_order_list_custom_column($columns) {
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach ($columns as $key => $column) {
        $reordered_columns[$key] = $column;

        if ($key === 'order_status') {
            // Inserting after "Status" column
            $reordered_columns['my-column1'] = __('Fraud', 'theme_domain');
        }
    }
    return $reordered_columns;
}

add_action('manage_woocommerce_page_wc-orders_custom_column', 'display_wc_order_list_custom_column_content', 10, 2);
function display_wc_order_list_custom_column_content($column, $order)
{
    switch ($column) {
        case 'my-column1':
            // Check if the customer is blacklisted
            $is_blacklisted = check_blacklist_on_order($order->get_id());

            // Display Yes or No based on blacklist status
            echo $is_blacklisted ? 'Yes' : 'No';
            break;
    }
}




function check_blacklist_on_order($order_id)
{
    global $wpdb;
    $order = wc_get_order($order_id);
    $customer_email = $order->get_billing_email();
    $customer_phone = $order->get_billing_phone();
    $customer_firstname = $order->get_billing_first_name();
    $customer_lastname = $order->get_billing_last_name();
    $customer_country = $order->get_billing_country();
    $customer_state = $order->get_billing_state();
    $customer_city = $order->get_billing_city();
    $customer_streetaddress = $order->get_billing_address_1();

    // Get the blacklisted customers from the custom table
    $blacklisted_customers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blacklisted_customers");
    $matched_fields = array();

    // Check if any field is in the blacklist
    foreach ($blacklisted_customers as $entry) {
        if (
            $entry->email == $customer_email ||
            $entry->phone == $customer_phone ||
            $entry->first_name == $customer_firstname ||
            $entry->last_name == $customer_lastname ||
            $entry->country == $customer_country ||
            $entry->state == $customer_state ||
            $entry->town_city == $customer_city ||
            $entry->street_address == $customer_streetaddress
        ) {
            // Add the matched field names to the array
            if ($entry->email == $customer_email) {
                $matched_fields[] = 'Email';
            }
            if ($entry->phone == $customer_phone) {
                $matched_fields[] = 'Phone';
            }
            if ($entry->first_name == $customer_firstname) {
                $matched_fields[] = 'Firstname';
            }
            if ($entry->last_name == $customer_lastname) {
                $matched_fields[] = 'Lastname';
            }
            if ($entry->country == $customer_country) {
                $matched_fields[] = 'Country';
            }
            if ($entry->state == $customer_state) {
                $matched_fields[] = 'State';
            }
            if ($entry->town_city == $customer_city) {
                $matched_fields[] = 'City';
            }
            if ($entry->street_address == $customer_streetaddress) {
                $matched_fields[] = 'Street Address';
            }
        }
    }

    // If any matched fields, add a note to the order
    if (!empty($matched_fields)) {
        $note_content = 'Matched fields in blacklist: ' . implode(', ', $matched_fields);

        // Use the dedicated function to add order notes
        $order->add_order_note(
            $note_content,
            true, // true for internal note, false for customer note
            false // false for public note, true for private note
        );

        return true;
    }

    return false;
}





// menu and submenus
function add_custom_woocommerce_sections() {
    add_menu_page(
        'Blacklist Sections',
        'Blacklist Sections',
        'manage_options',
        'blacklist_sections',
        'blacklist_sections_page',
        'dashicons-dismiss',
        30
    );

    // Add submenu pages
    add_submenu_page(
        'blacklist_sections',
        'Add New',
        'Add New',
        'manage_options',
        'blacklist_sections_new_bl_add',
        'blacklist_custom_sections_new_bl_add_page' // Corrected function name
    );

    add_submenu_page(
        'blacklist_sections',
        'All Blacklist Customers',
        'All Blacklist Customers',
        'manage_options',
        'blacklist_sections_bl_view',
        'blacklist_sections_bl_view_page'
    );

    add_submenu_page(
        'null', // Parent menu slug
        'Edit Page',
        'Edit Page',
        'manage_options',
        'blacklist_sections_edit',
        'blacklist_sections_edit_page'
    );
    
}

add_action('admin_menu', 'add_custom_woocommerce_sections');

function blacklist_sections_page() {
    include plugin_dir_path(__FILE__) . 'blacklist_sections.php';
}

function blacklist_custom_sections_new_bl_add_page() {
    include plugin_dir_path(__FILE__) . 'bl_add_page.php';
}

function blacklist_sections_bl_view_page() {
    include plugin_dir_path(__FILE__) . 'bl_view_page.php';
}

function blacklist_sections_wl_ip_addr_page() {
    include plugin_dir_path(__FILE__) . 'wl_ip_addr_page.php';
}

// Main plugin file

// Step 1: Hide the submenu page
function hide_blacklist_sections_edit_submenu() {
    remove_submenu_page('blacklist_sections', 'blacklist_sections_edit');
}
add_action('admin_menu', 'hide_blacklist_sections_edit_submenu');

// Step 2: Implement custom capability check for edit page
function blacklist_sections_edit_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Sorry, you are not allowed to access this page.'));
    }

    include_once('blacklist_sections_edit.php');
}


// step two : order fraud
// class Custom_Anti_Fraud {
//     public function __construct() {
//         add_action('woocommerce_after_checkout_validation', array($this, 'check_billing_address'));
//         add_action('woocommerce_new_order', array($this, 'update_fraud_status'));
//         add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
//         add_action('manage_shop_order_posts_custom_column', array($this, 'display_fraud_status_column_content'), 10, 2);
//         add_filter('manage_edit-shop_order_columns', array($this, 'add_fraud_status_column'));
        
//         // Do not include register_activation_hook inside the constructor
//         register_activation_hook(__FILE__, array($this, 'activate_plugin'));
//     }

//     public function enqueue_admin_scripts() {
//         // Enqueue your scripts and styles here, specifically for WooCommerce
//         wp_enqueue_style('custom-anti-fraud-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css');
//     }

//     public function activate_plugin() {
//         // Add your activation logic here, e.g., creating custom tables or adding options
//         global $wpdb;
//         $table_name = $wpdb->prefix . 'blacklisted_customers';
        
//         // Assuming you want to add the 'fraud' column to the 'wp_woocommerce_order_items' table
//         $wpdb->query("ALTER TABLE {$wpdb->prefix}woocommerce_order_items ADD COLUMN fraud VARCHAR(3) DEFAULT 'no'");
//     }

//     // Rest of the methods remain unchanged...
// }

// // Instantiate the class outside the constructor and hook the activation function
// $custom_anti_fraud_instance = new Custom_Anti_Fraud();

// // execute the activation function when the plugin is activated
// register_activation_hook(__FILE__, array($custom_anti_fraud_instance, 'activate_plugin'));


 
