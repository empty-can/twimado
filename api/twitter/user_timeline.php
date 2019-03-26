<?php

require_once ("init.php");

$api = 'statuses/user_timeline';

$user_id = getGetParam('id', '766219679631183872');
$count = getGetParam('count', '200');
$max_id = getGetParam('max_id', '');

$params = array(
    "user_id" => $user_id,
    "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}
$tweets = getTwitterConnection("", "")->get($api, $params);

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

echo json_encode($response);