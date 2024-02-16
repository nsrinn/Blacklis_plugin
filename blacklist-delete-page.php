<?php
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    // Perform deletion logic here
    $user_id = $_GET['user_id'];

    // Your deletion logic goes here

    // Redirect back to the blacklist page after deletion
    wp_redirect(admin_url('admin.php?page=blacklist_sections_new_bl_add'));
    exit;
} else {
    // Handle invalid request, e.g., redirect to the blacklist page
    wp_redirect(admin_url('admin.php?page=blacklist_sections_new_bl_add'));
    exit;
}
?>
