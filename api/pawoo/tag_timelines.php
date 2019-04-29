<?php
require_once ("init.php");

$account = getGetParam('account', '');
$id = getGetParam('id', '');
$limit = getGetParam('limit', MastodonTootsLimit);
$max_id = getGetParam('max_id', '');
$hashtag = getGetParam('tag', '');

if(!empty($account)) {
    $pair = get_access_tokens($account, 'pawoo');
    $access_token = $pair['access_token'];
} else if(!empty($id)){
    $access_token = getPassengerTokens($id, 'pawoo')['access_token'];
} else {
    $access_token = PawooAccessToken;
}

$api = "/api/v1/timelines/tag/$hashtag";

// var_dump($api);

if($limit>MastodonTootsLimit)
    $limit=MastodonTootsLimit;

$params = array(
    "limit" => $limit
    , "only_media" => true
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

$connection = getMastodonConnection(PawooDomain, $access_token);
$toots = $connection->executeGetAPI($api.'?'.http_build_query($params));


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