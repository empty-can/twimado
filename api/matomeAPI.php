<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$tweet_id = $param->parameters['tweet_id'];
$domain = $param->parameters['domain'];
$action = $param->parameters['action'];
// myVarDump($param->parameters);

if($action=='reg') {
if(!empty($param->parameters['matomeList']) && !empty($param->parameters['tweet_id'])) {
    foreach ($param->parameters['matomeList'] as $matome) {
        $result = regMatome($tweet_id, $domain, $matome);
    }
}
} else if($action=='del') {
    foreach ($param->parameters['matomeList'] as $matome) {
        $result = delMatome($tweet_id, $domain, $matome);
    }
}
// myVarDump($param->parameters);

$response = array();
$response['result'] = $result;
echo json_encode($response);