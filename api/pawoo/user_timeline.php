<?php

require_once ("init.php");

$id = getGetParam('id', '');
$count = getGetParam('count', '40');
$max_id = getGetParam('max_id', '');

$api = "/api/v1/accounts/$id/statuses";

$params = array(
    "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

$connection = getMastodonConnection('pawoo.net');
$toots = $connection->executeGetAPI($api.'?'.http_build_query($params));

$mutters = array();

$oldest = "";

if(empty($toots)) {
    $response = array();
    $response['mutters'] = array();
    $response['oldest_mutter'] = array();
    echo json_encode($response);
} else {
    foreach ($toots as $toot) {
        $tmp = new Pawoo($toot);
        
        $oldest = $tmp;
        $originalId = $tmp->originalId();
        
        if ($tmp->hasMedia() && !isset($mutters[$originalId]))
            $mutters[$originalId] = $tmp;
    }
    
    $response = array();
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
    
    echo json_encode($response);
}