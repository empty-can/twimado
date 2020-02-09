<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$tweet_id = $param->parameters['tweet_id'];
$user_id = $param->parameters['user_id'];
$domain = $param->parameters['domain'];
$action = $param->parameters['action'];
$matome = $param->parameters['matome'];
error_reporting(-1);
if ($action == 'reg') {
    if (!empty($matome) && !empty($tweet_id)) {
        $result = regMatome($tweet_id, $domain, $user_id, $matome);
    }
    if(contains($domain, 'twitter')) {
        $collection_ids = getTwitterCollection($user_id, $matome);
        addTwitterCollection($tweet_id, $collection_ids);
    }
} else if ($action == 'del') {
    if (!empty($matome) && !empty($tweet_id)) {
        $result = delMatome($tweet_id, $domain, $user_id, $matome);
    }
    if(contains($domain, 'twitter')) {
        $collection_ids = getTwitterCollection($user_id, $matome);
        removeTwitterCollection($tweet_id, $collection_ids);
    }
}
// myVarDump($param->parameters);

$response = array();
$response['result'] = $result;
echo json_encode($response);