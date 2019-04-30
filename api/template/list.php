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
    $api = AppURL . '/api/pawoo/list_timeline.php';
    $pawoo_oldest_id = $param->getValue('pawoo_oldest_id');
    
    $pawoo_param = clone $param;
    $pawoo_param->moveValue('pawoo_id', 'id');
    $pawoo_param->setInitialValue('count', MastodonTootsLimit);
    $pawoo_param->moveValue('count', 'limit');
    $pawoo_param->moveValue('pawoo_oldest_id', 'max_id');
    
    do {
        $tmp = getRequest($api, $pawoo_param->parameters);
        $tmp_response = json_decode($tmp, true);
        
        if(!is_array($tmp_response))
            break;

            // myVarDump($response);
        $pawoo_oldest = $tmp_response['oldest_mutter'];

        $tmp_mutters = array_merge($tmp_mutters, $tmp_response['mutters']);

        if (isset($pawoo_oldest['id']))
            $pawoo_oldest_id = $pawoo_oldest['id'];
        else
            $pawoo_oldest_id = - 1;

        $pawoo_param->setParam('pawoo_oldest_id', $pawoo_oldest_id);
    } while (count($tmp_mutters) < 1 && $pawoo_oldest_id>0); 
    
    if($pawoo_oldest_id == $param->getValue('pawoo_oldest_id'))
        $pawoo_oldest_id = - 1;
}

$mutters = array_merge($mutters, $tmp_mutters);
$tmp_mutters = array();

// 自分のイラストリストTL取得
if (contains($domain, 'twitter')) {
    $api = AppURL . '/api/twitter/list.php';
    $twitter_oldest_id = $param->getValue('twitter_oldest_id');
    
    $twitter_param = clone $param;
    $twitter_param->moveValue('twitter_id', 'id');
    $twitter_param->setInitialValue('count', '200');
    $twitter_param->moveValue('twitter_oldest_id', 'max_id');
    
    do {
        $tmp = getRequest($api, $twitter_param->parameters);
        $tmp_response = json_decode($tmp, true);
        
        if(!is_array($tmp_response))
            break;

            // myVarDump($response);
        $twitter_oldest = $tmp_response['oldest_mutter'];

        $tmp_mutters = array_merge($tmp_mutters, $tmp_response['mutters']);
        
        if (isset($twitter_oldest['id']))
            $twitter_oldest_id = $twitter_oldest['id'];
        else
            $twitter_oldest_id = - 1;
        $twitter_param->setParam('twitter_oldest_id', $twitter_oldest_id);
    } while (count($tmp_mutters) < 1 && $twitter_oldest_id>0);
    
    if($twitter_oldest_id == $param->getValue('twitter_oldest_id'))
        $twitter_oldest_id = - 1;
}

$mutters = array_merge($mutters, $tmp_mutters);

// myVarDump(json_decode($response, true));
// myVarDump(json_last_error());
$mutters = array_unique($mutters, SORT_REGULAR);
usort($mutters, "sort_mutter_by_time");

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
//     $response['mutters'][$mutter['id']] = htmlspecialchars($smarty->fetch("parts/mutter.tpl"));
}

$response['pawoo_oldest_id'] = isset($pawoo_oldest_id) ? $pawoo_oldest_id : "";
$response['twitter_oldest_id'] = isset($twitter_oldest_id) ? $twitter_oldest_id : "";

$response['error'] = ob_get_contents();
ob_end_clean();

echo json_encode($response);