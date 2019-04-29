<?php
require_once ("common.php");

$domain = getGetParam('domain', 'twitterpawoo');
$api = AppURL . '/api/template/home_timeline.php';
$hs = getGetParam('hs', 'true');
$count = getGetParam('count', '20');
$id = getGetParam('id', '');
$thumb = getGetParam('thumb', 'true');
$max_id = getGetParam('max_id', '');

if(empty($domain)) {
    echo "ドメインの指定がありません。";
    exit();
}

$params = array(
    "account" => Account
    ,"hs" => $hs
    ,"domain" => $domain
    ,"id" => $id
    ,"count" => $count
    ,"thumb" => $thumb
    , "pawoo_id" => PawooAccountID
    , "twitter_id" => TwitterAccountID
);

if(!empty($max_id)) {
    $params['oldest_id'] = $max_id;
}

$response = json_decode(getRequest($api, $params));


if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

$twitter_oldest_id = $response->twitter_oldest_id;
$pawoo_oldest_id = $response->pawoo_oldest_id;


$jss[] = "timeline";

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "ホームタイムライン");
$smarty->assign("csss", $csss);
$smarty->assign("jss", $jss);

$embedded_js_params_string = array_merge($embedded_js_params_string
    , array(
    "domain" => $domain
    ,"id" => $id
    ,"twitter_oldest_id" => $twitter_oldest_id
    ,"pawoo_oldest_id" => $pawoo_oldest_id
    ,"pawoo_id" => PawooAccountID
    ,"twitter_id" => TwitterAccountID
));

$embedded_js_params_int = array_merge($embedded_js_params_int, array());
$embedded_js_string = array_merge($embedded_js_params_int, array("api" => $api));
$embedded_js_int = array_merge($embedded_js_int, array());

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("mutters", $response->mutters);

// テンプレートを表示する
$smarty->display("timeline.tpl");