<?php
require_once ("init.php");

$api = AppURL . '/api/template/list.php';

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'twitterpawoo');
$param->setInitialValue('count', '20');
// $param->setInitialValue('test2', '20');

$name = $param->putValue('name');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);
$param->setParam('twitter_id', TwitterAccountID);

$tmp = getRequest($api, $param->parameters);

// myVarDump($tmp);
$response = my_json_decode($tmp);

// myVarDump($response);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

// 不要になったcountを削除
$param->unset('count');

// レスポンスから取得したデータをセット
if(isset($response->pawoo_oldest_id)) {
    $param->setParam('pawoo_oldest_id', $response->pawoo_oldest_id);
}
if(isset($response->twitter_oldest_id)) {
    $param->setParam('twitter_oldest_id', $response->twitter_oldest_id);
}

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "リスト：$name");

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
// if(!empty($ids)) {
//     $embedded_js_int["ids"] = "[".implode(",", $ids)."]";
// }

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("embedded_mutters", build_embededd_mutters(obj_to_array($response->mutters)));

$smarty->assign("mutters", array());

// テンプレートを表示する
$smarty->display("timeline.tpl");