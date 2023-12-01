<?php 
function fanooq_menu() {
    // Main menu
    add_menu_page(
        'All Fanooqs',           // Page title
        'Fanooqs',           // Menu title
        'manage_options',   // Capability
        'fanooq',           // Menu slug
        'fanooq_main_page', // Function to display the page
        'dashicons-welcome-write-blog' // Icon
    );

    add_submenu_page(
        'fanooq',           // Parent slug
        'Add New',          // Page title
        'Add New Fanooq',          // Menu title
        'manage_options',   // Capability
        'add_new_fanooq',   // Menu slug
        'add_new_fanooq'    // Function to display the page
    );

    add_submenu_page(
        'fanooq RSS',           // Parent slug
        'Add New RSS',          // Page title
        'Add New RSS',          // Menu title
        'manage_options',   // Capability
        'add_new_fanooq_rss',   // Menu slug
        'add_new_fanooq_rss'    // Function to display the page
    );
 

    add_submenu_page(
        'fanooq', 
        'Settings', 
        'Settings', 
        'manage_options', 
        'fanooq-settings', 
        'fanooq_settings'
    );
}

add_action('admin_menu', 'fanooq_menu');
?>