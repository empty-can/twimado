<?php

require_once ("init.php");

$api = 'statuses/home_timeline';

$count = getGetParam('count', '20');
$max_id = getGetParam('max_id', '');
$id = getGetParam('id', '');
$tokens = getTokens($id);
$access_token = $tokens[2];
$access_token_secret = $tokens[3];

$params = array(
    "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

$tweets = getTwitterConnection($access_token, $access_token_secret)->get($api, $params);

$mutters = array();
$oldest = "";

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);
    
    $oldest = $tmp;
    $originalId = $tmp->originalId();
    
    if ($tmp->hasMedia() && !isset($mutters[$originalId]))
        $mutters[$originalId] = $tmp;
}

$response = array();
$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

$response['error'] = ob_get_contents();
ob_end_clean();

echo json_encode($response);