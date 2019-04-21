<?php
require_once ("init.php");

$domain = getGetParam('domain', 'pawoo');
$api = AppURL . '/api/template/home_timeline.php';
$hs = getGetParam('hs', 'true');
$count = getGetParam('count', '20');
$id = getGetParam('id', '');
$thumb = getGetParam('thumb', 'true');
$max_id = getGetParam('max_id', '');
$pawoo_id = getSessionParam("pawoo_id", "");
$twitter_id = getSessionParam("twitter_id", "");

if(empty($domain)) {
    echo "ドメインの指定がありません。";
    exit();
}

$params = array(
    "hs" => $hs
    ,"domain" => $domain
    ,"id" => $id
    ,"count" => $count
    ,"thumb" => $thumb
    , "pawoo_id" => $pawoo_id
    , "twitter_id" => $twitter_id
);

if(!empty($max_id)) {
    $params['oldest_id'] = $max_id;
}

$response = json_decode(getRequest($api, $params));

// myVarDump($response);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

$twitter_oldest_id = $response->twitter_oldest_id;
$pawoo_oldest_id = $response->pawoo_oldest_id;

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "ホームタイムライン");
$smarty->assign("AppContext", AppContext);
$smarty->assign("hs", $hs);

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
    ,"hs" => $hs
    ,"thumb" => $thumb
    ,"twitter_oldest_id" => $twitter_oldest_id
    ,"pawoo_oldest_id" => $pawoo_oldest_id
    ,"pawoo_id" => $pawoo_id
    ,"twitter_id" => $twitter_id
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