<?php
require_once ("init.php");

$domain = getGetParam('domain', '');
$api = AppURL . '/api/template/user_timeline.php';
$hs = getGetParam('hs', 'true');
$count = getGetParam('count', '20');
$target_id = getGetParam('target_id', '');
$thumb = getGetParam('thumb', 'true');
$max_id = getGetParam('max_id', '');
$access_token = "";

if(empty($domain)) {
    echo "ドメインの指定がありません。";
    exit();
} else if(contains($domain, "twitter")) {
    $params = array(
        "user_id" => $target_id
    );
    $account = getTwitterConnection()->get("users/show", $params);
    $title = $account->name;
} else if(contains($domain, "pawoo")) {
    $connection = getMastodonConnection(PawooDomain, $access_token);
    $account = $connection->executeGetAPI("api/v1/accounts/$target_id");
    $title = $account["display_name"]."@".$account["username"];
}

$params = array(
    "hs" => $hs
    ,"domain" => $domain
    ,"target_id" => $target_id
    ,"count" => $count
    ,"thumb" => $thumb
    ,"pawoo_id" => PawooAccountID
    ,"twitter_id" => TwitterAccountID
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
    "account" => Account
    ,"domain" => $domain
    ,"target_id" => $target_id
    ,"hs" => $hs
    ,"thumb" => $thumb
    ,"oldest_id" => $oldest_id
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

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

$smarty->assign("mutters", $response->mutters);

// テンプレートを表示する
$smarty->display("timeline.tpl");