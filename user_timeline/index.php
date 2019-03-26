<?php

require_once ("init.php");

$domain = getGetParam('domain', '');
$id = getGetParam('id', '');
$hidden_sensitive = getGetParam('hidden_sensitive', 'true');
$count = getGetParam('count', '10');
$api = AppURL . 'api/user_timeline.php';

if(empty($domain)) {
    echo "ドメインの指定がありません。";
    exit();
}

$params = array(
    "hidden_sensitive" => $hidden_sensitive
    ,"domain" => $domain
    ,"id" => $id
    ,"count" => 10
);

$response = json_decode(getRequest($api, $params));

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

$oldest_id = $response->oldest_id;

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "テストタイムライン");
$smarty->assign("AppContext", AppContext);
$smarty->assign("hidden_sensitive", $hidden_sensitive);

$csss=array();
$csss[] = "timeline";
$smarty->assign("csss", $csss);

$jss=array();
$jss[] = "jquery-3.3.1.min";
$jss[] = "common";
$jss[] = "timeline";
$smarty->assign("jss", $jss);

$embedded_js_params_string = [
    "domain" => $domain
    ,"id" => $id
    ,"hidden_sensitive" => $hidden_sensitive
    ,"oldest_id" => $oldest_id
];

$embedded_js_params_int = [
];

$embedded_js_string = [
    "api" => $api
];
$embedded_js_int = [
    "count" => $count
];

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("mutters", $response->mutters);

// テンプレートを表示する
$smarty->display("timeline.tpl");