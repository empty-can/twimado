<?php
require_once ("init.php");

$domain = getGetParam('domain', '');
$api = AppURL . '/api/template/list.php';
$hs = getGetParam('hs', 'true');
$count = getGetParam('count', '20');
$thumb = getGetParam('thumb', 'true');
$max_id = getGetParam('max_id', '');

$name = getGetParam('name', '');

switch ($domain) {
    case "twitter" :
        $list_id = getGetParam('list_id', TwitterList);
        break;
    case "pawoo" :
        $list_id = getGetParam('list_id', "");
        break;
    default :
        echo "ドメインの指定がありません。";
        exit();
}

$params = array(
    "account" => Account
    , "hs" => $hs
    ,"domain" => $domain
    ,"list_id" => $list_id
    ,"count" => $count
    ,"thumb" => $thumb
    ,"pawoo_id" => PawooAccountID
    ,"twitter_id" => TwitterAccountID
);

// myVarDump($params);
if(!empty($max_id)) {
    $params['oldest_id'] = $max_id;
}

$tmp = getRequest($api, $params);

// myVarDump($tmp);

$response = json_decode($tmp);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}
// myVarDump($response);
$twitter_oldest_id = $response->twitter_oldest_id;
$pawoo_oldest_id = $response->pawoo_oldest_id;

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "マイリスト：$name");
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
    "account" => Account
    ,"domain" => $domain
    ,"hs" => $hs
    ,"list_id" => $list_id
    ,"thumb" => $thumb
    ,"twitter_oldest_id" => $twitter_oldest_id
    ,"pawoo_oldest_id" => $pawoo_oldest_id
    ,"pawoo_id" => PawooAccountID
    ,"twitter_id" => TwitterAccountID
];

$embedded_js_params_int = [
];

$embedded_js_string = [
    "api" => $api
];
$embedded_js_int = [
    "count" => $count
];
if(!empty($ids)) {
    $embedded_js_int["ids"] = "[".implode(",", $ids)."]";
}

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("mutters", $response->mutters);

// テンプレートを表示する
$smarty->display("timeline.tpl");