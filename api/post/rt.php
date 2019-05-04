<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$domain = $param->putValue('domain');
$method = $param->putValue('method');
$id = $param->getValue('id');
// myVarDump($param);

$result=["未実行"=>"0"];

// pawooのリブログ
if (contains($domain, 'pawoo')) {
    if ($method == 'do') {
        $api = "api/v1/statuses/$id/reblog";
    } else if ($method == 'undo') {
        $api = "api/v1/statuses/$id/unreblog";
    }
    
    if(!empty($api)) {
        $connection = getMastodonConnection(PawooDomain);
        $result = $connection->executePostAPI($api);
    }
}

// twitterのリツイート
if (contains($domain, 'twitter')) {
    if ($method == 'do') {
        $api = 'statuses/retweet/'.$id;
    } else if ($method == 'undo') {
        $api = 'statuses/unretweet/'.$id;
    }
    
    if(!empty($api)) {
        $result = getTwitterConnection()->post($api, $param->parameters);
    }
}

echo json_encode($result);