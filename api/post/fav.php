<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$domain = $param->putValue('domain');
$method = $param->putValue('method');
$id = $param->getValue('id');
// myVarDump($param);

$result=["未実行"=>"0"];

ob_start();
// pawooの自分TL取得
if (contains($domain, 'pawoo')) {
    if ($method == 'do') {
        $api = "api/v1/statuses/$id/favourite";
    } else if ($method == 'undo') {
        $api = "api/v1/statuses/$id/unfavourite";
    }
    
    if(!empty($api)) {
        $connection = getMastodonConnection(PawooDomain);
        $result = $connection->executePostAPI($api);
    }
}

// 自分のイラストリストTL取得
if (contains($domain, 'twitter')) {
    if ($method == 'do') {
        $api = 'favorites/create';
    } else if ($method == 'undo') {
        $api = 'favorites/destroy';
    }
    
    if(!empty($api)) {
        $result = getTwitterConnection()->post($api, $param->parameters);
    }
}

echo json_encode($result);