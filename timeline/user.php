<?php
require_once ("init.php");

$domain = getGetParam('domain', '');
$api = AppURL . '/api/template/user_timeline.php';
$hs = getGetParam('hs', 'true');
$count = getGetParam('count', '20');
$id = getGetParam('id', '');
$thumb = getGetParam('thumb', 'true');
$max_id = getGetParam('max_id', '');
$access_token = "";

if(empty($domain)) {
    echo "ドメインの指定がありません。";
    exit();
} else if(contains($domain, "twitter")) {
    $params = array(
        "user_id" => $id
    );
    $account = getTwitterConnection("", "")->get("users/show", $params);
    $title = $account->name;
} else if(contains($domain, "pawoo")) {
    $access_token = getSessionParam("pawoo_access_token", "");
    $connection = getMastodonConnection(PawooDomain, $access_token);
    $account = $connection->executeGetAPI("api/v1/accounts/$id");
    $title = $account["display_name"]."@".$account["username"];
}

$params = array(
    "hs" => $hs
    ,"domain" => $domain
    ,"id" => $id
    ,"count" => $count
    ,"thumb" => $thumb
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

$oldest_id = $response->oldest_id;

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", $title);
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
    ,"id" => $id
    ,"hs" => $hs
    ,"thumb" => $thumb
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