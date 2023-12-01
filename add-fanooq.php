<?php 
function add_new_fanooq()
{
    wp_enqueue_script('jquery');
    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>RSS feeds to Post</h1>
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
                <tr valign="top">
                    <th scope="row">Count</th>
                    <td>
                        <input type="text" id="wp_content_count" name="wp_content_count"
                            value="1" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"></th>
                    <td>
                        <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Save">
                        <div id="upload-btn-saving" style="display: none;">Saving...</div>
                    </td>
                </tr>

            </table>
            <?php //submit_button(); ?>
            <a href="<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=create_fanooq_posts" >Run cron</a> | 
            <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=add_new_fanooq_rss" >Add RSS Links</a>
            
        </form>
        <script type="text/javascript">
            function isUrlValid(url) {
                return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
            }
            jQuery(document).ready(function ($) {
                $('#upload-btn').click(function (e) {
                    $('#upload-btn').hide();
                    $('#upload-btn-saving').show();
                    val = $('#wp_content_url').val();
                    count = $('#wp_content_count').val();

                    if(count > 0 && count <=20) {
                        // if (val != '' ) {
                        if (isUrlValid(val)) {
                            //Valid URL 
                            var data = {
                                action: 'save_fanooq_request',
                                url: val,
                                count: count
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
                    } else if(count > 20){
                        alert('count should be between 1 to 20');
                        $('#upload-btn').show();
                        $('#upload-btn-saving').hide();
                    } else {
                        alert('count should be between 1 to 20');
                        $('#upload-btn').show();
                        $('#upload-btn-saving').hide();
                    }
                });
            });
        </script>
    </div>
    <?php
}



add_action('wp_ajax_save_fanooq_request', 'save_fanooq_request');

function save_fanooq_request(){

    

    global $table_prefix, $wpdb;
    $url = $_GET['url'];
    $post_count = $_GET['count'];
    $feeds_json = get_feeds_array($url);
    $feeds = json_decode($feeds_json);

    $array = [];
    $i = 0;
    $messages = [];

    if(!empty($feeds)){
        // $feeds_array = $feeds['channel']['item'];
        // $array_link_element = 'link';
        // if(!$feeds['channel']['item']){
        //     $feeds_array = $feeds['entry'];
        //     $array_link_element = 'id';
        // }
        // pr($feeds_array);

        foreach($feeds as $row){
            // if(empty($row[$array_link_element])){
            //     pr('Could not access link from feed...');
            //     break;
            // }
            if($i < $post_count){
                // $link = $row[$array_link_element];
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
                    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
                    dbDelta( $sql );
                }
            } else {
                break;
            }
            $i++;
        }
    }
    echo "Finished";
    die;
} 

function get_feeds_array($rssFeedUrl){
    $array = [];
    $i = 0;
    $rss_data = file_get_contents($rssFeedUrl);
    if ($rss_data !== false) {
        $rss = simplexml_load_string($rss_data);
        if ($rss !== false) {
            foreach ($rss->channel->item as $item) {
                $array[$i]['title'] = (string)$item->title;
                $array[$i]['link'] = (string) $item->link;
                $i++;
            }
        } 
    } 
    $arr = json_encode($array);
    return $arr;    
}


?>