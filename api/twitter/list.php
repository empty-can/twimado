<?php
require_once ("init.php");

$api = 'lists/statuses';

$account = getGetParam('account', '');
$id = getGetParam('id', '');
$list_id = getGetParam('list_id', TwitterList);
$count = getGetParam('count', '200');
$max_id = getGetParam('max_id', '');

if(!empty($account)) {
    $pair = get_access_tokens($account, 'twitter');
    $access_token = $pair['access_token'];
    $access_token_secret = $pair['access_token_secret'];
} else if(!empty($id)){
    $tokens = getPassengerTokens($id, 'twitter');
    $access_token = $tokens['access_token'];
    $access_token_secret = $tokens['access_token_secret'];
} else {
    $access_token = TwitterAccessToken;
    $access_token_secret = TwitterAccessTokenSecret;
}

if(empty($list_id)) {
    echo "No list id specified.";
    exit();
}
$params = array(
    "list_id" => $list_id,
    "count" => 200
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

$tweets = getTwitterConnection($access_token, $access_token_secret)->get($api, $params);

$mutters = array();
$oldest = "";
$i = (int)0;

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);
    
    $oldest = $tmp;
    $originalId = $tmp->originalId();
    
    if ($tmp->hasMedia() && !isset($mutters[$originalId]))
        $mutters[$originalId] = $tmp;
        
    $i++;
    
    if($i>$count) break;
}

// var_dump($mutters);

$response = array();
$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

$response['error'] = ob_get_contents();
ob_end_clean();

// myVarDump($response['error']);

echo json_encode($response);