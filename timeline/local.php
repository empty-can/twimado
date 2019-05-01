<?php
require_once ("init.php");

$api = AppURL . '/api/template/local_timeline.php';

// $domain = getGetParam('domain', 'pawoo');
// $hs = getGetParam('hs', 'true');
// $count = getGetParam('count', '20');
// $thumb = getGetParam('thumb', 'true');
// $pawoo_id = PawooAccountID;

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('domain', 'pawoo');
$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('count', '20');
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);

// $pawoo_oldest_id = "";

// $params = array(
//     "domain" => $domain
//     ,"pawoo_id" => $pawoo_id
//     ,"hs" => $hs
//     ,"count" => $count
//     ,"thumb" => $thumb
// );

$tmp = getRequest($api, $param->parameters);

// myVarDump($tmp);

$response = json_decode($tmp);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

// 不要になったcountを削除
$param->unset('count');

// foreach ($response->mutters as $key => $value) {
//     $mutters[$key] = $value;
// }
// myVarDump($response);
$param->setParam('pawoo_oldest_id', $response->pawoo_oldest_id);

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "PawooローカルTL");

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

$smarty->assign("mutters", $response->mutters);

// テンプレートを表示する
$smarty->display("timeline.tpl");