<?php 
//http://localhost/tk/wp-admin/admin-ajax.php?action=create_fanooq_posts
add_action('wp_ajax_create_fanooq_posts', 'create_fanooq_posts');
function create_fanooq_posts()
{
    pr('Execution Started...');
    pr('---------------------');
    $post_array = get_links_rss_table();
    if(empty($post_array)){
        pr('No Links Found...');
        die();
    }
    pr($post_array);
    pr('Data Fetched...');
    pr('---------------------');
    $i = 0;
    $arr = [];
    foreach($post_array as $row){
        $url =$row->links;
        pr('Processing '. $i .' - '. $url);
        if(!post_exists($url)){
            pr('post does not exist...');
            pr('scrapping starting...');
            $scrapped_html = scrap($url);
            pr('scrapping finished...');

            if(!empty($scrapped_html)){
                $content = trim(get_article_content_from_html($scrapped_html));
                if(trim($content) == "" ){
                    pr('Article tag does not exist. Execution stopped.');
                    update_fanooq_web_links('2', $row->id, 'Article tag does not exist. Execution stopped.');
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
                        update_fanooq_web_links('1', $row->id, '' , $postId);
                        pr('Record status updated');
                    } else {
                        $error = $decoded_response['error']['message'];
                        update_fanooq_web_links('2', $row->id, $error);
                    }
                }
            } else {
                pr('could not scrap...');
                update_fanooq_web_links('2', $row->id, 'could not scrap...');
                break;
            }
        } else {
            pr('Post already exists. Skipped.');
            update_fanooq_web_links('2', $row->id, 'Post already exists. Skipped.');
        }
        $i++;        
        pr('------------------------------------------------------------------------------------------------------------------------------------------------------------------------');
    }
    die('Finished');
}


function scrap($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)){
        echo 'Request Error:' . curl_error($ch);
        exit;
    }
    curl_close($ch);
    return $response;
}

function get_article_content_from_html($html){
    $dom = new DOMDocument;
    libxml_use_internal_errors(true); 
    $dom->loadHTML($html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    $articles = $xpath->query('//article');
    $content = '';
    foreach ($articles as $article) {
        $content .= $article->nodeValue . "\n";
    }
    return $content;
}


function extract_suggested_category($text) {
    // $pattern = '/Suggested Category:\s*([^\n\r]+)/';
    $pattern = '/Suggested Category: \s*(.*?)\s*(?=Title Suggestions|Introduction|Article Body|Focused Keywords|$)/s';

    if (preg_match($pattern, $text, $matches)) {
        return trim($matches[1]);
    } else {
        return 'No suggested category found';
    }
}

function extract_seo_tags($text) {
    // $pattern = '/Highly Rated SEO Tags:\s*([^\n\r]+)/';
    $pattern = '/Highly Rated SEO Tags: \s*(.*?)\s*(?=Title Suggestions|Introduction|Article Body|Focused Keywords|$)/s';
    if (preg_match($pattern, $text, $matches)) {
        return trim($matches[1]);
    } else {
        return '';
    }
}

function extract_excerpt($text) {
    $pattern = '/Excerpt:\s*(.*?)\s*(?=Title Suggestions|Introduction|Article Body|Focused Keywords|Suggested Category|$)/s';
    if (preg_match($pattern, $text, $matches)) {
        return trim($matches[1]);
    } else {
        return 'No excerpt found';
    }
}

?>