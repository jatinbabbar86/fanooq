<?php 

function update_fanooq_web_links($status, $id='', $error='', $post_id='', $link='' ){
    global $table_prefix, $wpdb;
    $tablename = 'fanooq_web_links'; 
    $linksTable = $table_prefix . $tablename;
    $update = "UPDATE `$linksTable` "; 
    $update .= "SET `status` = '$status'"; 
    if($error != '') {
        $update .= ", `error_message` = '".htmlentities($error)."' "; 
    }
    if($post_id != '') {
        $update .= ", `generated_post_id` = '".$post_id."' "; 
    }
    if($link != '') {
        $update .= "WHERE `links` = '".$link."'";
    } else {
        $update .= "WHERE `id` = ".$id;
    }
    echo $update;
    $wpdb->query($update);
}

function get_links_rss_table() {
    global $table_prefix, $wpdb;
    $tablename = 'fanooq_web_links'; 
    $linksTable = $table_prefix . $tablename;
    $sql = "SELECT `id`,`links` from $linksTable WHERE `status` = '0'";
    return $wpdb->get_results($sql);
}

?>