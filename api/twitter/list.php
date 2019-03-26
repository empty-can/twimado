<?php

require_once ("init.php");

$api = 'lists/statuses';

$list_id = getPostParam('list_id', '');
$list_id = '1076465411418255360';
$count = getPostParam('count', '200');
$max_id = getPostParam('max_id', '');

if(empty($list_id)) {
    echo "No list id specified.";
    exit();
}
$params = array(
    "list_id" => $list_id,
    "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

$tweets = getTwitterConnection("", "")->get($api, $params);

// myVarDump($tweets);

$mutters = array();
$oldest = "";

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);
    
    $oldest = $tmp;
    
    if ($tmp->hasMedia())
        $mutters[$tmp->originalId()] = $tmp;
}

// var_dump($mutters);

$response = array();
$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

echo json_encode($response);