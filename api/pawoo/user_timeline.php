<?php

require_once ("init.php");

$id = getGetParam('id', '');
$limit = getGetParam('limit', MastodonTootsLimit);
$max_id = getGetParam('max_id', '');

$api = "api/v1/accounts/$id/statuses";

$params = array(
    "limit" => ($limit>MastodonTootsLimit) ? MastodonTootsLimit : $limit
    , "only_media" => true
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

// myVarDump(http_build_query($params));

$connection = getMastodonConnection(PawooDomain);
$toots = $connection->executeGetAPI($api.'?'.http_build_query($params));

// myVarDump($toots);

$mutters = array();

$oldest = "";

if(empty($toots)) {
    $response = array();
    $response['mutters'] = array();
    $response['oldest_mutter'] = null;
    echo json_encode($response);
} else {
    foreach ($toots as $toot) {
        $tmp = new Pawoo($toot);
        
//         myVarDump($tmp);
        
        $oldest = $tmp;
        $originalId = $tmp->originalId();
        
        if ($tmp->hasMedia() && !isset($mutters[$originalId]))
            $mutters[$originalId] = $tmp;
    }
    
    $response = array();
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
    
//     myVarDump($mutters);
    
    echo json_encode($response);
}