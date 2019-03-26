<?php

require_once ("init.php");

$api = getPostParam('api', 'api/v1/timelines/home');

if(empty($api)) {
    echo "No api specified.";
    exit();
}

$domain = getPostParam('domain', 'pawoo.net');
$local = getPostParam('local', 'true');
$limit = getPostParam('limit', '40');
$max_id = getPostParam('max_id', '');

$params = array(
    "screen_name" => $domain,
    "local" => $local,
    "count" => $limit
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

$connection = getMastodonConnection($domain);
$toots = $connection->executeGetAPI($api.'?'.http_build_query($params));

$mutters = array();

$oldest = "";

if($domain=='pawoo.net') {
    foreach ($toots as $toot) {
        $tmp = new Pawoo($toot);
        
        $oldest = $tmp;
        $originalId = $tmp->originalId();
        
        if ($tmp->hasMedia() && !isset($mutters[$originalId]))
            $mutters[$originalId] = $tmp;
    }
}

$response = array();
$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

echo json_encode($response);
