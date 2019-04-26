<?php
require_once ("init.php");

$domain = getGetParam('domain', 'twitterpawoo');
$api = AppURL . '/api/template/search.php';
$q = getGetParam('q', '');
$hs = getGetParam('hs', 'true');
$count = getGetParam('count', '20');
$thumb = getGetParam('thumb', 'true');
$max_id = getGetParam('max_id', '');
$pawoo_access_token = getSessionParam("pawoo_access_token", "");

if(empty($domain)) {
    echo "ドメインの指定がありません。";
    exit();
}

if(empty($q)) {
    echo "検索キーワードがありません。";
    exit();
}

$params = array(
    "hs" => $hs
    ,"domain" => $domain
    ,"q" => $q
    ,"count" => $count
    ,"thumb" => $thumb
    , "pawoo_access_token" => $pawoo_access_token
);
// myVarDump($params);
if(!empty($max_id)) {
    $params['oldest_id'] = $max_id;
}

$tmp = getRequest($api, $params);

$response = json_decode($tmp);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

$twitter_oldest_id = $response->twitter_oldest_id;
$pawoo_oldest_id = $response->pawoo_oldest_id;

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "検索：".urldecode($q));
$smarty->assign("AppContext", AppContext);
$smarty->assign("hs", $hs);
$smarty->assign("mylists", getSessionParam("twitter_mylists", array()));

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
    ,"q" => urlencode($q)
    ,"hs" => $hs
    ,"thumb" => $thumb
    ,"twitter_oldest_id" => $twitter_oldest_id
    ,"pawoo_oldest_id" => $pawoo_oldest_id
    , "pawoo_access_token" => $pawoo_access_token
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