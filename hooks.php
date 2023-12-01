<?php 

register_activation_hook( __FILE__, "activate_myplugin" );
register_deactivation_hook( __FILE__, "deactivate_myplugin" );

function activate_myplugin() { 
	init_db_myplugin();
}

function deactivate_myplugin() { 
    die('test');
	deactivate_db_myplugin_func();
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

function deactivate_db_myplugin_func() {
	global $table_prefix, $wpdb;
    $tablename = 'fanooq_web_links'; 
	$linksTable = $table_prefix . $tablename;
    $sql = "DROP table $linksTable";
    $wpdb->query($sql);
}