<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromPostParameters();

$param->setInitialValue('hs', 'true');
$param->setInitialValue('thumb', 'true');

$domain = $param->putValue('domain');
$thumb = $param->putValue('thumb');
$hs = $param->putValue('hs');

$mutters = array();

$response = array();
$response['mutters'] = array();

ob_start();

// pawooの自分TL取得
if (contains($domain, 'pawoo')) {
}

// 自分のイラストリストTL取得
if (contains($domain, 'twitter')) {
    $api = AppURL . '/api/twitter/lookup.php';

    $twitter_param = clone $param;

    $twitter_param->setInitialValue('count', '100');
    $twitter_param->moveValue('twitter_oldest_id', 'max_id');

    $twitter_oldest_id = $twitter_param->getValue('twitter_oldest_id');
    $target_id = $twitter_param->getValue('target_id', "");
    $count = $twitter_param->getValue('count', '100');

    $ids = getMutterIds($target_id, $twitter_oldest_id, $count);

    $twitter_result = array();

    if(!empty($ids)) {
        $twitter_param->setParam('ids', $ids);

        $twitter_result = getMutters($api, $twitter_param->parameters, $twitter_oldest_id);
        $twitter_oldest_id = $twitter_result['oldest_id'];
    }

	$response['mutters']  = array_merge($response['mutters'] , $twitter_result['mutters']);
}
$mutters = array_unique($response['mutters'] , SORT_REGULAR);
usort($mutters, "sort_mutter");

// テンプレートを表示する
$hs = ($hs=='true') ? true : false;
$thumb = ($thumb=='true') ? true : false;
$smarty->assign("hs", $hs);
$smarty->assign("thumb", $thumb);

$response['mutters'] = array();
foreach ($mutters as $mutter) {
    $smarty->assign("mutter", $mutter);
    $html = $smarty->fetch("parts/mutter.tpl");
    $response['mutters'][$mutter['originalId']] = $html;
}
ob_end_clean();

$response['twitter_oldest_id'] = isset($twitter_oldest_id) ? $twitter_oldest_id : "";

// myVarDump($response['mutters']);
// echo json_encode($response);

echo json_encode($response);