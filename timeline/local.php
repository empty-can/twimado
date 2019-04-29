<?php
require_once ("init.php");

$domain = getGetParam('domain', 'pawoo');
$api = AppURL . '/api/template/local_timeline.php';
$hs = getGetParam('hs', 'true');
$count = getGetParam('count', '20');
$thumb = getGetParam('thumb', 'true');
$pawoo_id = PawooAccountID;
$mutters = array();

$pawoo_oldest_id = "";

$params = array(
    "domain" => $domain
    ,"pawoo_id" => $pawoo_id
    ,"hs" => $hs
    ,"count" => $count
    ,"thumb" => $thumb
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
$pawoo_oldest_id = $response->pawoo_oldest_id;

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "PawooローカルTL");
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
    ,"hs" => $hs
    ,"thumb" => $thumb
    ,"pawoo_oldest_id" => $pawoo_oldest_id
    ,"pawoo_id" => $pawoo_id
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
$smarty->assign("mylists", getSessionParam("twitter_mylists", array()));

// テンプレートを表示する
$smarty->display("timeline.tpl");