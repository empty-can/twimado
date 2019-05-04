<?php

require_once ("init.php");

$account = getPostParam('account', '');
$id = getPostParam('id', '');
$target_id = getPostParam('target_id', '');
$limit = getPostParam('limit', MastodonTootsLimit);
$max_id = getPostParam('max_id', '');
$mo = getPostParam('mo', 'true');

if(!empty($account)) {
    $pair = get_access_tokens($account, 'pawoo');
    $access_token = $pair['access_token'];
} else if(!empty($id)){
    $access_token = getPassengerTokens($id, 'pawoo')['access_token'];
} else {
    $access_token = PawooAccessToken;
}

$api = "api/v1/accounts/$target_id/statuses";

$params = array(
    "limit" => ($limit>MastodonTootsLimit) ? MastodonTootsLimit : $limit
    , "only_media" => ($mo=='true') ? true : false
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

// myVarDump(http_build_query($params));

$connection = getMastodonConnection(PawooDomain, $access_token);
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
        
        if($mo=='false') {
            $mutters[$originalId] = $tmp;
        } else if ($tmp->hasMedia() && !isset($mutters[$originalId])) {
            $mutters[$originalId] = $tmp;
        }
    }
    
    $response = array();
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
    
//     myVarDump($mutters);
    
    echo json_encode($response);
}