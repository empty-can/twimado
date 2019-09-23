<?php
require_once ("init.php");

$api = AppURL . '/api/template/matome.php';

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('asc', '1');
$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'twitterpawoo');
$param->setInitialValue('count', '5');
$param->setInitialValue('edit', 'false');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);
$param->setParam('twitter_id', TwitterAccountID);

$matome_id = $param->getValue('matome_id');
$asc = $param->getValue('asc');
$edit = $param->getValue('edit');

$matomeInfo = getMatomeInfo($matome_id);

$tmp = getRequest($api, $param->parameters);
$response = my_json_decode($tmp);

// myVarDump($response);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

$param->setParam('count', '100');

// レスポンスから取得したデータをセット
if(isset($response->pawoo_oldest_id)) {
    $param->setParam('pawoo_oldest_id', $response->pawoo_oldest_id);
}
if(isset($response->twitter_oldest_id)) {
    $param->setParam('twitter_oldest_id', $response->twitter_oldest_id);
}
if(isset($response->twitter_latest_id)) {
    $param->setParam('twitter_latest_id', $response->twitter_latest_id);
}

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "まとめ：".$matomeInfo['title']);
$smarty->assign("matomeInfo", $matomeInfo);

$csss=array();
$csss[] = "timeline";
$smarty->assign("csss", $csss);

$jss=array();
$jss[] = "timeline";
$smarty->assign("jss", $jss);

$embedded_js_params_string = $param->parameters;

$embedded_js_params_int = [
];

$embedded_js_string = [
    "api" => $api
];
$embedded_js_int = [
    "count" => AsyncCount
    , "asc" => $asc
    , "edit" => $edit
];

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("embedded_mutters", build_embededd_mutters(obj_to_array($response->mutters)));

$matomeList = array();
$matomeList[0] = getMatomeInfo($matome_id);
$smarty->assign("matomeList", $matomeList);

$smarty->assign("mutters", array());

// テンプレートを表示する
$smarty->display("matome_timeline.tpl");