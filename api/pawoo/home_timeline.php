<?php

require_once ("init.php");

$limit = getGetParam('limit', '40');
$max_id = getGetParam('max_id', '');
$id = getGetParam('id', PawooID);

$access_token = getTokens($id)[1];

$api = "api/v1/timelines/home";

$params = array(
    "limit" => $limit
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

$connection = getMastodonConnection(PawooDomain, $access_token);
// myVarDump($connection);
$toots = $connection->executeGetAPI($api.'?'.http_build_query($params));
// myVarDump($toots);
// var_dump($toots);

$mutters = array();

$oldest = "";

$response = array();

if(empty($toots)) {
    $response['mutters'] = array();
    $response['oldest_mutter'] = null;
} else {
    foreach ($toots as $toot) {
        $tmp = new Pawoo($toot);
        
//         myVarDump($tmp);
        
        $oldest = $tmp;
        $originalId = $tmp->originalId();
        
        if ($tmp->hasMedia() && !isset($mutters[$originalId]))
            $mutters[$originalId] = $tmp;
    }
    
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
}

$response['error'] = ob_get_contents();
ob_end_clean();

echo json_encode($response);