<?php
require_once ("init.php");

$api = AppURL . '/api/template/search.php';

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'twitterpawoo');
$param->setInitialValue('count', '20');

$q = mb_ereg_replace("[　]"," ", $param->getValue('q'));
$f = $param->getValue('f');
$searchType = $param->getValue('searchType');

if($searchType=='hash') {
	$q = '%23'.$q;
} else if($searchType=='account') {
	header('Location: /account.php?q='.$q);
    exit;
}

if(!empty($f)) {
	$q .= "%20filter:$f";
} else if($searchType=='account') {
	header('Location: /account.php?q='.$q);
    exit;
}

$param->setParam('q', $q);

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);
$param->setParam('twitter_id', TwitterAccountID);


$tmp = getRequest($api, $param->parameters);
$response = json_decode($tmp);

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
}

// レスポンスから取得したデータをセット
$param->setParam('pawoo_oldest_id', $response->pawoo_oldest_id);
$param->setParam('twitter_oldest_id', $response->twitter_oldest_id);
/**
**/

// 不要になったcountを削除
$param->unset('count');

$param->setInitialValue('pawoo_oldest_id', '');
$param->setInitialValue('twitter_oldest_id', '');

$query = urldecode(explode('%20filter', $q)[0]);

$image_file_name = getPageImages($query);

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "$query の検索結果");

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

$smarty->assign("og_image", $image_file_name);
$smarty->assign("twitter_card", "summary_large_image");

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

// $smarty->assign("embedded_mutters", build_embededd_mutters(obj_to_array($response->mutters)));
$smarty->assign("embedded_mutters", build_embededd_mutters(array()));

$smarty->assign("mutters", array());

// テンプレートを表示する
$smarty->display("timeline.tpl");