<?php
require_once ("common.php");

$api = AppURL . '/api/template/home_timeline.php';

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'twitterpawoo');
$param->setInitialValue('count', '20');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);
$param->setParam('twitter_id', TwitterAccountID);

// TLを取得
$tmp = getRequest($api, $param->parameters);
$response = my_json_decode($tmp);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

// 不要になったcountを削除
$param->unset('count');


// レスポンスから取得したデータをセット
$param->setParam('pawoo_oldest_id', $response->pawoo_oldest_id);
$param->setParam('twitter_oldest_id', $response->twitter_oldest_id);


// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "ホームタイムライン");

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
$embedded_js_params_int = array_merge($embedded_js_params_int, array());
$embedded_js_string = array_merge($embedded_js_params_int, array("api" => $api));
$embedded_js_int = array_merge($embedded_js_int, array());

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("embedded_mutters", build_embededd_mutters(obj_to_array($response->mutters)));

$smarty->assign("mutters", array());

// テンプレートを表示する
$smarty->display("timeline.tpl");