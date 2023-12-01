<?php 
function fanooq_main_page() {
    
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['page']) && $_GET['page'] == 'fanooq') {
        fanooq_delete_link();
    }

    
    global $wpdb;

    $delete_nonce = wp_create_nonce('fanooq_delete_link_' . $row->id);

    // Fetch data from the database
    $table_name = $wpdb->prefix . 'fanooq_web_links';
    $results = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");

    // Start HTML for the admin page
    echo '<div class="wrap">';
    echo '<h1 style="width:200px; float: left;">Fanooq Links</h1><br>';
    echo '<h1 style="float: right;"><a target="_blank" class="button" href="'.site_url('/wp-admin/admin.php?page=add_new_fanooq_rss').'">RSS Links</a></h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th colspan="1">ID</th>
                <th>Links</th>
                <th>RSS Feed URL</th>
                <th>Status</th>
                <th>Error Message</th>
                <th>Generated Link</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>';
    echo '<tbody>';

    if(count($results) > 0){
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td colspan="1">' . $row->id . '</td>';
            echo '<td><a target="_blank" href="'. $row->links .'">' . $row->links . '</a></td>';
            echo '<td><a href="'. $row->rss_feed_url .'">' . $row->rss_feed_url . '</a></td>';
            echo '<td>' . $row->status . '</td>';
            echo '<td>' . $row->error_message . '</td>';
            echo '<td><a href="'. get_permalink( $row->generated_post_id ) .'">' . get_permalink( $row->generated_post_id )  . '</a></td>';
            echo '<td>' . $row->date_created . '</td>';
            echo '<td>';
                echo '<a class="button" href="?page=fanooq&action=delete&link_id=' . esc_attr($row->id) . '&_wpnonce=' . esc_attr($delete_nonce) . '">Delete</a> ';
                if($row->status == 0){
                   echo '<a target="_blank" class="button" href="'.site_url('/wp-admin/admin-ajax.php?action=gpt_create_post&link='.urlencode($row->links)).'">Run</a>';
                }
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr>';
            echo '<td colspan="8" style="text-align: center;">No records Found</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
} 

function fanooq_delete_link() {
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'fanooq_web_links';

    $link_id = isset($_GET['link_id']) ? intval($_GET['link_id']) : 0;
    $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';

    // if (!wp_verify_nonce($nonce, 'fanooq_delete_link_' . $link_id)) {
    //     die('Invalid nonce');
    // }
    if (current_user_can('manage_options')) {
        $wpdb->delete($table_name, ['id' => $link_id], ['%d']);
    }
    // wp_redirect('http://www.google.com');
    wp_redirect(admin_url('admin.php?page=fanooq'));

    // exit;
}

add_action('wp_ajax_gpt_create_post', 'gpt_create_post');

function gpt_create_post(){

    pr('Execution Started...');
    pr($_GET['link']);
    $url = $_GET['link'];
    
    if(filter_var($url, FILTER_VALIDATE_URL)){
        pr('valid');



        cron_run($url);



    } 
    
    pr('---------------------');

    
    die;
}



function cron_run($url){

    if(!post_exists($url)){
        pr('post does not exist...');
        pr('scrapping starting...');
        $scrapped_html = scrap($url);
        pr('scrapping finished...');

        if(!empty($scrapped_html)){
            $content = trim(get_article_content_from_html($scrapped_html));
            if(trim($content) == "" ){
                pr('Article tag does not exist. Execution stopped.');
                update_fanooq_web_links('2', '', 'Article tag does not exist. Execution stopped.',$url);
            } else {
                
                $content = !empty($content) ? $content : $scrapped_html;
                $content = trim(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content));
                pr($content);
                $data = [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [['role' => 'user', 'content' => get_prompt($content)]],
                    'max_tokens' => 1000, // Adjust as needed
                    'temperature' => 0.7 // You can adjust this parameter as needed
                ];
                pr('data array created...');
                pr($data); 
                $decoded_response = gpt_call($data);
                $article = $decoded_response['choices'][0]['message']['content'];
                pr($decoded_response);
                if($article != ""){
                    $excerpt = extract_excerpt($article);
                    $suggested_category = extract_suggested_category($articles);
                    $tags = extract_seo_tags($content);
                    $post_data = [
                        'post_title'   => $url,
                        'post_content' => $article,
                        'post_excerpt' => $excerpt,
                        // 'post_category' => $suggested_category,
                        'tags_input' => $tags,
                        'post_status'  => 'draft',
                        'post_type'    => 'post',
                    ];
                    pr($post_data);
                    $postId = wp_insert_post( $post_data, $wp_error );
                    
                    if($postId > 0){
                        $status = '1';
                        pr('Post Created.');
                    } else {
                        $status = '2';
                        pr('Save Error');
                    }
                    //UPDATE the row status
                    update_fanooq_web_links('1', '', '' , $postId, $url);
                    pr('Record status updated');
                } else {
                    $error = $decoded_response['error']['message'];
                    update_fanooq_web_links('2', '', $error, $url);
                }
            }
        } else {
            pr('could not scrap...');
            update_fanooq_web_links('2', '', 'could not scrap', $url);
        }
    } else {
        pr('Post already exists. Skipped.');
        update_fanooq_web_links('2', '', 'Post already exists. Skipped',$url);
    }

    pr('Finished...');


}




?>