<?php
require_once ("init.php");

$account = getPostParam('account', '');
$id = getPostParam('id', '');
$limit = getPostParam('limit', MastodonTootsLimit);
$max_id = getPostParam('max_id', '');
$hashtag = getPostParam('tag', '');

ob_start();

if(!empty($account)) {
    $pair = get_access_tokens($account, 'pawoo');
    $access_token = $pair['access_token'];
} else if(!empty($id)){
    $access_token = getPassengerTokens($id, 'pawoo')['access_token'];
} else {
    $access_token = PawooAccessToken;
}

$api = "/api/v1/timelines/tag/$hashtag";

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

$response = array();
$response['error'] = ob_get_contents();
ob_end_clean();

if(empty($toots)) {
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
    
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
    
//     myVarDump($mutters);
    
    echo json_encode($response);
}