<?php 
function add_new_fanooq_rss()
{
    wp_enqueue_script('jquery');
    wp_enqueue_media();
    ?>
    <style>
       
       
    </style>
    <div class="wrap">
        <h1>Add RSS feeds</h1>
        <!-- <h3>Logo Settings</h3> -->
        <form method="post" action="options.php">
            <?php settings_fields('change_login_options_group'); ?>
            <?php do_settings_sections('change_login_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">URL</th>
                    <td>
                        <input type="text" id="wp_content_url" name="wp_content_url"
                            value="https://feeds.bbci.co.uk/news/technology/rss.xml" />
                    </td>
                </tr>
                <!-- <tr valign="top">
                    <th scope="row">Count</th>
                    <td>
                        <input type="text" id="wp_content_count" name="wp_content_count"
                            value="1" />
                    </td>
                </tr> -->
                <tr valign="top">
                    <th scope="row"></th>
                    <td>
                        <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Save">
                        <div id="upload-btn-saving" style="display: none;">Saving...</div>
                    </td>
                </tr>

            </table>
            <?php //submit_button(); ?>

        </form>



        <?php rss_listing(); ?>



        <script type="text/javascript">
            function isUrlValid(url) {
                return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
            }
            jQuery(document).ready(function ($) {
                $('#upload-btn').click(function (e) {

                    

                    $('#upload-btn').hide();
                    $('#upload-btn-saving').show();
                    val = $('#wp_content_url').val();
                    // count = $('#wp_content_count').val();

                    // if(count > 0 && count <=20) {
                        // if (val != '' ) {
                        if (isUrlValid(val)) {
                            //Valid URL 
                            var data = {
                                action: 'save_rss_url',
                                url: val
                            };
                            $.get(ajaxurl, data, function (response) {
                                console.log(response);
                                alert(response);
                                $('#upload-btn').show();
                                $('#upload-btn-saving').hide();
                            });
                        
                        } else {
                            // Invalid URL
                            alert('Invalid URL');
                            $('#upload-btn').show();
                            $('#upload-btn-saving').hide();
                        }
                    // } else if(count > 20){
                    //     alert('count should be between 1 to 20');
                    //     $('#upload-btn').show();
                    //     $('#upload-btn-saving').hide();
                    // } else {
                    //     alert('count should be between 1 to 20');
                    //     $('#upload-btn').show();
                    //     $('#upload-btn-saving').hide();
                    // }
                });
            });
        </script>
    </div>
    <?php
}


function rss_listing() {
    
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['page']) && $_GET['page'] == 'fanooq') {
        fanooq_delete_link();
    }
    global $wpdb;
    $delete_nonce = wp_create_nonce('fanooq_delete_link_' . $row->id);
    $table_name = $wpdb->prefix . 'fanooq_rss_links';
    $results = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");

    echo '<div class="wrap">';
    echo '<h1 style="width:200px; float: left;">Fanooq Links</h1>';
    // echo '<h1 style="float: right;"><input style="cursor: pointer;" type="button" onclick="location.href='.site_url('/wp-admin/admin.php?page=run_rss_links').'" value="Add Fanooq Link" ></h1>';
    // echo '<h1 style="float: right;"><input style="cursor: pointer;" type="button" onclick="location.href='.site_url('/wp-admin/admin.php?page=run_rss_links').'" value="All Fanooq Links" ></h1>';
    // echo '<h1 style="float: right;"><input style="cursor: pointer;" type="button" onclick="location.href='.site_url('/wp-admin/admin.php?page=run_rss_links').'" value="Run Rss Links" ></h1>';
    
    echo '<h1 style="float: right;"><a target="_blank" class="button" href="'.site_url('/wp-admin/admin.php?page=add_new_fanooq').'">Add Fanooq Links</a></h1>';
    echo '<h1 style="float: right;"><a target="_blank" class="button" href="'.site_url('/wp-admin/admin.php?page=fanooq').'">All Fanooq Links</a></h1>';
    echo '<h1 style="float: right;"><a target="_blank" class="button" href="'.site_url('/wp-admin/admin-ajax.php?action=run_rss_links').'">Run All RSS Links</a></h1>';


    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th colspan="1">ID</th>
                <th>Links</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>';
    echo '<tbody>';
    if(count($results) > 0){
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td colspan="1">' . $row->id . '</td>';
            echo '<td><a href="'. $row->links .'">' . $row->links . '</a></td>';
            echo '<td>' . $row->date_created . '</td>';
            echo '<td><a href="?page=fanooq&action=delete&link_id=' . esc_attr($row->id) . '&_wpnonce=' . esc_attr($delete_nonce) . '">Delete</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr>';
            echo '<td colspan="5" style="text-align: center;">No records Found</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';


} 




add_action('wp_ajax_save_rss_url', 'save_rss_url');

function save_rss_url(){

    global $table_prefix, $wpdb;
    $url = $_GET['url'];
    $message = 'Success';
    if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
        echo "Invalid Email.";
        die();
    } else {

        $tablename = 'fanooq_rss_links'; 
        $linksTable = $table_prefix . $tablename;
        $sql = "SELECT `id` from $linksTable WHERE `links` = '$url'";
        $post_id = $wpdb->get_results($sql);

        if(empty($post_id)){
            $sql = "INSERT INTO `$linksTable` 
                            (`id`, `links`, `date_created`) 
                            VALUES 
                            ( NULL, '$url', current_timestamp());";
            $post_id = $wpdb->query($sql);
            if(!$post_id){
                $message = "Error in saving.";
                die;
            }
        } else {
            $message = "Link already exists.";
        }
        echo $message;
        die;
    }
    die;
}

add_action('wp_ajax_run_rss_links', 'run_rss_links');

function run_rss_links(){
    global $table_prefix, $wpdb;
    $tablename = 'fanooq_rss_links'; 
    $linksTable = $table_prefix . $tablename;
    $sql = "SELECT `links` from $linksTable";
    $links = $wpdb->get_results($sql);
    $post_count = 20;
    
    $message = 'success';
    if(!empty($links)){
        pr($links);
        foreach($links as $links_arr) {
            $feeds_json = get_feeds_array($links_arr->links);
            $feeds = json_decode($feeds_json);
            $i = 0;
            foreach($feeds as $row){
                if($i < $post_count){
                    $link = $row->link;
                    $exp = explode('?', $link);
                    $link_url =$exp[0];
                    $tablename = 'fanooq_web_links'; 
                    $linksTable = $table_prefix . $tablename;
                    $sql = "SELECT `id` from $linksTable WHERE `links` = '$link_url'";
                    $post_id = $wpdb->get_results($sql);
                    if(empty($post_id)){
                        $sql = "INSERT INTO `$linksTable` 
                            (`id`, `links`, `rss_feed_url`, `status`, `date_created`) 
                            VALUES 
                            ( NULL, '$link_url', '$url', '0', current_timestamp());";
    
                        pr($sql);
                        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
                        dbDelta( $sql );
                    }
                    $i++;
                }
            }
            pr('----------------------------------');
        }
        $message = "Links Found";
    } else {
        $message = 'No Links Found';
    }
    echo $message; die;
}

?>