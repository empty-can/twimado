<?php

require_once ("init.php");

$api = 'lists/statuses';

$list_id = getGetParam('list_id', TwitterList);
$count = getGetParam('count', '200');
$max_id = getGetParam('max_id', '');

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

ob_start();

$tweets = getTwitterConnection("", "")->get($api, $params);

$mutters = array();
$oldest = "";

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);
    
    $oldest = $tmp;
    
    if ($tmp->hasMedia()) {
        if(isset($tmp->originalId)) {
            if(!isset($mutters[$tmp->originalId])) {
                $mutters[$tmp->originalId] = $tmp;
                //                 $mutters[$tmp->id] = $tmp;
            }
        } else {
            $mutters[$tmp->id] = $tmp;
        }
    }
}

// var_dump($mutters);

$response = array();
$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

$response['error'] = ob_get_contents();
ob_end_clean();

// myVarDump($response['error']);

echo json_encode($response);