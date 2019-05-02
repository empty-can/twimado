<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromPostParameters();

$param->setInitialValue('hs', 'true');
$param->setInitialValue('thumb', 'true');

$domain = $param->putValue('domain');
$thumb = $param->putValue('thumb');
$hs = $param->putValue('hs');

$q = urlencode($param->getValue('q'));

$mutters = array();
$tmp_mutters = array();
ob_start();

// pawooの検索結果取得（pawooはツイートのキーワード検索未対応）
if (contains($domain, 'pawoo')) {
    $api = AppURL . '/api/pawoo/tag_timelines.php';
    $pawoo_oldest_id = $param->getValue('pawoo_oldest_id');
    
    $pawoo_param = clone $param;
    $pawoo_param->moveValue('pawoo_id', 'id');
    $pawoo_param->setInitialValue('count', '80');
    $pawoo_param->moveValue('count', 'limit');
    $pawoo_param->moveValue('pawoo_oldest_id', 'max_id');
    
    // Pawooはタグ検索APIを使うので先頭の#は削除する
    $pawoo_param->setParam('q', mb_ereg_replace('%23', '', $q));
    $pawoo_param->moveValue('q', 'tag');
    
    do {
        $tmp = getRequest($api, $pawoo_param->parameters);
        
        $tmp_response = json_decode($tmp, true);
        if (! is_array($tmp_response))
            break;

        // myVarDump($response);
        $pawoo_oldest = $tmp_response['oldest_mutter'];

        $tmp_mutters = array_merge($tmp_mutters, $tmp_response['mutters']);

        if (isset($pawoo_oldest['id']))
            $pawoo_oldest_id = $pawoo_oldest['id'];
        else
            $pawoo_oldest_id = - 1;

        $pawoo_param->setParam('pawoo_oldest_id', $pawoo_oldest_id);
    } while (count($tmp_mutters) < 1 && $pawoo_oldest_id > 0);

    if ($pawoo_oldest_id == $param->getValue('pawoo_oldest_id'))
        $pawoo_oldest_id = - 1;
}

$mutters = array_merge($mutters, $tmp_mutters);
$tmp_mutters = array();

// Twitterの検索結果取得
if (contains($domain, 'twitter')) {
    $api = AppURL . '/api/twitter/search.php';
    $twitter_oldest_id = $param->getValue('twitter_oldest_id');
    
    $twitter_param = clone $param;
    $twitter_param->moveValue('twitter_id', 'id');
    $twitter_param->setInitialValue('count', '200');
    $twitter_param->moveValue('twitter_oldest_id', 'max_id');
    
    do {
        $tmp = getRequest($api, $twitter_param->parameters);
        $tmp_response = json_decode($tmp, true);

        if (! is_array($tmp_response))
            break;

        // myVarDump($response);
        $twitter_oldest = $tmp_response['oldest_mutter'];

        $tmp_mutters = array_merge($tmp_mutters, $tmp_response['mutters']);

        if (isset($twitter_oldest['id']))
            $twitter_oldest_id = $twitter_oldest['id'];
        else
            $twitter_oldest_id = - 1;

        $twitter_param->setParam('twitter_oldest_id', $twitter_oldest_id);
    } while (count($tmp_mutters) < 1 && $twitter_oldest_id > 0);
    
    if($twitter_oldest_id == $param->getValue('twitter_oldest_id'))
        $twitter_oldest_id = - 1;
}

$mutters = array_merge($mutters, $tmp_mutters);
$mutters = array_unique($mutters, SORT_REGULAR);
usort($mutters, "sort_mutter_by_time");

// テンプレートを表示する
$hs = ($hs=='true') ? true : false;
$thumb = ($thumb=='true') ? true : false;
$smarty->assign("hs", $hs);
$smarty->assign("q", $q);
$smarty->assign("thumb", $thumb);

$response = array();
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

// echo $response['error'];
echo json_encode($response);