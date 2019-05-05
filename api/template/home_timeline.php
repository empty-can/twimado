<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromPostParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));

$domain = $param->putValue('domain');
$thumb = $param->putValue('thumb');
$hs = $param->putValue('hs');

$mutters = array();

$response = array();
$response['mutters'] = array();
$errors = array();

ob_start();

// pawooの自分TL取得
if (contains($domain, 'pawoo')) {
    $api = AppURL . '/api/pawoo/home_timeline.php';
    $pawoo_oldest_id = $param->getValue('pawoo_oldest_id');
    
    $pawoo_param = clone $param;
    $pawoo_param->moveValue('pawoo_id', 'id');
    $pawoo_param->setInitialValue('count', '40');
    $pawoo_param->moveValue('pawoo_oldest_id', 'max_id');
    
    $pawoo_result = getMutters($api, $pawoo_param->parameters, $pawoo_oldest_id);
    
	$response['mutters']  = array_merge($response['mutters'] , $pawoo_result['mutters']);
	$pawoo_oldest_id = $pawoo_result['oldest_id'];
}

// 自分のイラストリストTL取得
if (contains($domain, 'twitter')) {
    $api = AppURL . '/api/twitter/home_timeline.php';
    $twitter_oldest_id = $param->getValue('twitter_oldest_id');
    
    $twitter_param = clone $param;
    $twitter_param->moveValue('twitter_id', 'id');
    $twitter_param->setInitialValue('count', '200');
    $twitter_param->moveValue('twitter_oldest_id', 'max_id');
    
    $twitter_result = getMutters($api, $twitter_param->parameters, $twitter_oldest_id);
	$response['mutters']  = array_merge($response['mutters'] , $twitter_result['mutters']);
	$twitter_oldest_id = $twitter_result['oldest_id'];
}
// myVarDump($response);
// myVarDump(json_encode($response, true));
// myVarDump(json_last_error());
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
// myVarDump($response['mutters']);

$response['pawoo_oldest_id'] = isset($pawoo_oldest_id) ? $pawoo_oldest_id : "";
$response['twitter_oldest_id'] = isset($twitter_oldest_id) ? $twitter_oldest_id : "";
// myVarDump($response['mutters']);

$errors[] = array("template"=>ob_get_contents());
ob_end_clean();
// myVarDump($response);
// if(!empty($errors))
//     $response['error'] = json_encode(["template"=>json_encode($errors)]);

echo json_encode($response);