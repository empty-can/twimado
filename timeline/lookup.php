<?php
require_once ("init.php");

$api = AppURL . '/api/template/lookup.php';

$user_id = getGetParam('target_id', "");

$twitter_oldest_id = getPostParam('target_id', "");

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'twitterpawoo');
$param->setInitialValue('count', '10');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);
$param->setParam('twitter_id', TwitterAccountID);

$ids = getMutterIds($user_id, $twitter_oldest_id);

$param->setParam('ids', $ids);

$tmp = getRequest($api, $param->parameters);

// myVarDump($tmp);
$response = my_json_decode($tmp);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

// 不要になったcountを削除
$param->unset('count');

// myVarDump($response);
// レスポンスから取得したデータをセット
if(isset($response->pawoo_oldest_id)) {
    $param->setParam('pawoo_oldest_id', $response->pawoo_oldest_id);
}
if(isset($response->twitter_oldest_id)) {
    $param->setParam('twitter_oldest_id', $response->twitter_oldest_id);
}

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "lookup");

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
];

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("embedded_mutters", build_embededd_mutters(obj_to_array($response->mutters)));

$smarty->assign("mutters", array());

// テンプレートを表示する
$smarty->display("timeline.tpl");