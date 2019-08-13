<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$tweet_id = $param->parameters['tweet_id'];
$domain = $param->parameters['domain'];
$action = $param->parameters['action'];
$matome = $param->parameters['matome'];

if ($action == 'reg') {
    if (!empty($matome) && !empty($tweet_id)) {
        $result = regMatome($tweet_id, $domain, $matome);
    }
} else if ($action == 'del') {
    if (!empty($matome) && !empty($tweet_id)) {
        $result = delMatome($tweet_id, $domain, $matome);
    }
}
// myVarDump($param->parameters);

$response = array();
$response['result'] = $result;
echo json_encode($response);