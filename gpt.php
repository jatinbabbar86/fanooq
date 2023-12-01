<?php function get_prompt($body){

return 'You are a professional blog writer. 
    rewrite the article below:
    "' . $body . '"
    Suggest me 5 titles, 
    an introduction, 
    an article body with paragraph text and heading for each paragraph, 
    a conclusion, 
    10 focused keywords, 
    an excerpt between 120 to 160 characters,
    suggested category and label them as "Suggested Categories"
    and 10  highly rated comma separated seo tags without hashes and label them as "Highly Rated SEO Tags"  
    for the blog article. Blog article should contain highly rated set keywords. Article language should be casual.
' ;
//along with a generated image for the article with a resolution of 1300*685 px. 
} 

function gpt_call($data){
    $api_key = gpt_api_key();
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

?>