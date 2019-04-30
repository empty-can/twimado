<?php
require_once ("init.php");

header('Location: /');
exit();

$domain = getPostParam('domain', 'twitterpawoo');
$api = AppURL . '/api/template/timeline.php';
$hs = getPostParam('hs', 'true');
$count = getPostParam('count', '20');
$twitter_list = getPostParam('twitter_list', TwitterList);
$thumb = getPostParam('thumb', 'true');
$mutters = array();

$twitter_oldest_id = "";
$pawoo_oldest_id = "";

$params = array(
    "domain" => $domain
    ,"hs" => $hs
    ,"count" => $count
    ,"thumb" => $thumb
    ,"twitter_list" => $twitter_list
);

$response = json_decode(getRequest($api, $params));

// myVarDump($response->mutters);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

$mutters = $response->mutters;

// foreach ($response->mutters as $key => $value) {
//     $mutters[$key] = $value;
// }
// myVarDump($mutters);
$twitter_oldest_id = $response->twitter_oldest_id;
$pawoo_oldest_id = $response->pawoo_oldest_id;

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "公式タイムライン");
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
    ,"hs" => $hs
    ,"thumb" => $thumb
    ,"twitter_oldest_id" => $twitter_oldest_id
    ,"pawoo_oldest_id" => $pawoo_oldest_id
    ,"twitter_list" => $twitter_list
];

$embedded_js_params_int = [
];

$embedded_js_string = [
    "api" => $api
];
$embedded_js_int = [
    "count" => 200
];
// $embededd_js_assocarray = [
//     "mutterQueue" => $mutters
// ];
$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));
// $smarty->assign("embededd_js_assocarray", build_embededd_js_assocarray($embededd_js_assocarray));

// myVarDump($response->mutters);

$smarty->assign("mutters", $mutters);

// テンプレートを表示する
$smarty->display("timeline.tpl");