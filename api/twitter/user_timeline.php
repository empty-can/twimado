<?php

require_once ("init.php");

$api = 'statuses/user_timeline';

$screen_name = getPostParam('screen_name', 'orenoyome');
$count = getPostParam('count', '200');
$max_id = getPostParam('max_id', '');

$params = array(
    "screen_name" => $screen_name,
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