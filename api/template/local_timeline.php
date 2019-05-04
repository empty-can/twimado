<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromPostParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));

$domain = $param->putValue('domain');
$thumb = $param->putValue('thumb');
$hs = $param->putValue('hs');

$response = array();
$response['mutters'] = array();

ob_start();

// pawooの自分TL取得
if (contains($domain, 'pawoo')) {
    $api = AppURL . '/api/pawoo/local_timeline.php';
    $pawoo_oldest_id = $param->getValue('pawoo_oldest_id');
    
    $pawoo_param = clone $param;
    $pawoo_param->moveValue('pawoo_id', 'id');
    $pawoo_param->setInitialValue('count', MastodonTootsLimit);
    $pawoo_param->moveValue('count', 'limit');
    $pawoo_param->moveValue('pawoo_oldest_id', 'max_id');

    $pawoo_result = getMutters($api, $pawoo_param->parameters, $pawoo_oldest_id);
    
	$response['mutters']  = array_merge($response['mutters'] , $pawoo_result['mutters']);
	$pawoo_oldest_id = $pawoo_result['oldest_id'];
}

$mutters = array_unique($response['mutters'] , SORT_REGULAR);
usort($mutters, "sort_mutter");

// テンプレートを表示する
$hs = ($hs == 'true') ? true : false;
$thumb = ($thumb == 'true') ? true : false;
$smarty->assign("hs", $hs);
$smarty->assign("thumb", $thumb);

$response['mutters'] = array();
foreach ($mutters as $mutter) {
    $smarty->assign("mutter", $mutter);
    $html = $smarty->fetch("parts/mutter.tpl");
    $response['mutters'][$mutter['originalId']] = $html;
    // $response['mutters'][$mutter['id']] = htmlspecialchars($smarty->fetch("parts/mutter.tpl"));
}

$response['pawoo_oldest_id'] = isset($pawoo_oldest_id) ? $pawoo_oldest_id : "";

$errors[] = array("template"=>ob_get_contents());
ob_end_clean();

// $response['error'] = $errors;

echo json_encode($response);