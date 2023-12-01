<?php
/**
 * @package Fanooq
 * @version 1.0.0
 */
/*
Plugin Name: Fanooq
Plugin URI: https://lifestylens.com/
Description: This is not just a plugin.
Author: Jatin Babbar
Version: 1.0.0
Author URI: https://lifestylens.com/
*/

// require_once( ABSPATH . 'wp-content/plugins/fanooq/hooks.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/functions.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/debug.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/menu.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/listing.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/settings.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/add-fanooq.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/gpt.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/cron.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/queries.php' );
require_once( ABSPATH . 'wp-content/plugins/fanooq/add-rss.php' );


register_activation_hook( __FILE__, "activate_myplugin" );
register_deactivation_hook( __FILE__, "deactivate_myplugin" );

function activate_myplugin() { 
	init_db_myplugin();
	create_rss_table();
}

function deactivate_myplugin() { 
	deactivate_db_myplugin_func();
	deactivate_db_rss_myplugin_func();
}

function init_db_myplugin() {

	// WP Globals
	global $table_prefix, $wpdb;
    
    $tablename = 'fanooq_web_links'; 
	// Customer Table
	$customerTable = $table_prefix . $tablename;
    
	// Create Customer Table if not exist
	if( $wpdb->get_var( "show tables like '$customerTable'" ) != $customerTable ) {
		$sql = "CREATE TABLE `$customerTable` (";
		$sql .= " `id` int(11) NOT NULL auto_increment, ";
		$sql .= " `links` varchar(255) NOT NULL, ";
		$sql .= " `rss_feed_url` varchar(255) NOT NULL, ";
		// $sql .= " `category` varchar(255) NOT NULL, ";
		$sql .= " `status` enum('0','1','2') NOT NULL DEFAULT '0', ";
        $sql .= " `error_message` varchar(255) DEFAULT NULL, ";
        $sql .= " `generated_post_id` varchar(255) DEFAULT NULL, ";
		$sql .= " `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(), ";
		$sql .= " PRIMARY KEY `id` (`id`) ";
		$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;";

		// Include Upgrade Script
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	
		// Create Table
		dbDelta( $sql );
	}

}

function create_rss_table(){
	// WP Globals
	global $table_prefix, $wpdb;
    
    $tablename = 'fanooq_rss_links'; 
	// Customer Table
	$customerTable = $table_prefix . $tablename;
    
	// Create Customer Table if not exist
	if( $wpdb->get_var( "show tables like '$customerTable'" ) != $customerTable ) {
		$sql = "CREATE TABLE `$customerTable` (";
		$sql .= " `id` int(11) NOT NULL auto_increment, ";
		$sql .= " `links` varchar(255) NOT NULL, ";
		$sql .= " `status` enum('0','1','2') NOT NULL DEFAULT '0', ";
		$sql .= " `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(), ";
		$sql .= " PRIMARY KEY `id` (`id`) ";
		$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;";

		// Include Upgrade Script
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	
		// Create Table
		dbDelta( $sql );
	}
}

function deactivate_db_myplugin_func() {
	global $table_prefix, $wpdb;
    $tablename = 'fanooq_web_links'; 
	$linksTable = $table_prefix . $tablename;
    $sql = "DROP table $linksTable";
    $wpdb->query($sql);
}

function deactivate_db_rss_myplugin_func() {
	global $table_prefix, $wpdb;
    $tablename = 'fanooq_rss_links'; 
	$linksTable = $table_prefix . $tablename;
    $sql = "DROP table $linksTable";
    $wpdb->query($sql);
}

?>