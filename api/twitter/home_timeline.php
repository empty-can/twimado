<?php
require_once ("init.php");

$api = 'statuses/home_timeline';

$account = getGetParam('account', '');
$id = getGetParam('id', '');
$count = getGetParam('count', '20');
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
    echo "error";
    exit();
}

$params = array(
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

$response = array();
$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

$response['error'] = ob_get_contents();
ob_end_clean();

echo json_encode($response);