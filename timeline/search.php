<?php
require_once ("init.php");

$api = AppURL . '/api/template/search.php';

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'twitter');
$param->setInitialValue('count', '100');

$q = mb_ereg_replace("[　]"," ", $param->getValue('q'));
$searchType = $param->getValue('searchType');

if($searchType=='account') {
	header('Location: /account.php?q='.$q);
    exit;
} else if($searchType=='hash') {
	$param->setParam('q', '#'.$q);
}

// $param->setInitialValue('pawoo_oldest_id', '');
$param->setInitialValue('twitter_oldest_id', '');

// $param->setParam('q', $q);

if(true) {
// if(false) {

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
    // $param->setParam('pawoo_oldest_id', $response->pawoo_oldest_id);
    $param->setParam('twitter_oldest_id', $response->twitter_oldest_id);
    /**
    **/

    // 不要になったcountを削除
    $param->unset('count');
}

if($searchType=='hash') {
	$q = '#'.$q;
}

$image_file_name = getPageImages($q);

$f = $param->getValue('f');
if(!empty($f)) {
	$q .= " filter:$f";
}

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "$q の検索結果");

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

$smarty->assign("AppURL", AppURL);
$smarty->assign("og_image", $image_file_name);
$smarty->assign("twitter_card", "summary_large_image");

$smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
$smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

// $smarty->assign("embedded_mutters", build_embededd_mutters(obj_to_array($response->mutters)));
$smarty->assign("embedded_mutters", build_embededd_mutters(array()));

$smarty->assign("mutters", array());

// テンプレートを表示する
$smarty->display("timeline.tpl");