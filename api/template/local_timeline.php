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
$tmp_mutters = array();

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

    do {
        $tmp = getRequest($api, $pawoo_param->parameters);
        $tmp_response = json_decode($tmp, true);

        if (! is_array($response))
            break;

        $pawoo_oldest = $tmp_response['oldest_mutter'];

        $tmp_mutters = array_merge($tmp_mutters, $tmp_response['mutters']);
        
        if (isset($pawoo_oldest['id']))
            $pawoo_oldest_id = $pawoo_oldest['id'];
        else
            $pawoo_oldest_id = - 1;
        
        $pawoo_param->setParam('pawoo_oldest_id', $pawoo_oldest_id);
    } while (count($tmp_mutters) < 1 && $pawoo_oldest_id > 0);
    
    if($pawoo_oldest_id == $param->getValue('pawoo_oldest_id'))
        $pawoo_oldest_id = - 1;
}

$mutters = array_merge($mutters, $tmp_mutters);
// $tmp_mutters = array();

// myVarDump(json_decode($response, true));
// myVarDump(json_last_error());
$mutters = array_unique($mutters, SORT_REGULAR);
usort($mutters, "sort_mutter_by_time");

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