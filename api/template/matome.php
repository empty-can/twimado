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
$response['debug'] = array();

ob_start();

// pawooの自分TL取得
if (contains($domain, 'pawoo')) {
}

// 自分のイラストリストTL取得
if (contains($domain, 'twitter')) {
    $api = AppURL . '/api/twitter/lookup.php';

    $twitter_param = clone $param;

    $twitter_param->setInitialValue('count', '5');

    $twitter_oldest_id = $twitter_param->getValue('twitter_oldest_id');
    $twitter_latest_id = $twitter_param->getValue('twitter_latest_id');
    $matome_id = $twitter_param->getValue('matome_id', "");
    $asc = $twitter_param->getValue('asc', 1);
    $count = $twitter_param->getValue('count', '5');

    if($asc==1) {
        $ids = getMatomeIds($matome_id, $twitter_latest_id, $asc, $count);
    } else {
        $ids = getMatomeIds($matome_id, $twitter_oldest_id, $asc, $count);
    }
//     echo $ids;
    if(!empty($ids)) {
        $twitter_param->setParam('ids', $ids);
        $twitter_param->moveValue('twitter_oldest_id', 'max_id');

        $twitter_result = getMutters($api, $twitter_param->parameters, $twitter_oldest_id);

        $twitter_oldest_id = $twitter_result['oldest_id'];
        $twitter_latest_id = $twitter_result['latest_id'];
        $response['mutters']  = array_merge($response['mutters'], $twitter_result['mutters']);

        $ids = explode(',', $ids);
        if($asc==1) {
            $response['twitter_oldest_id'] = $ids[0];
            $response['twitter_latest_id'] = $ids[count($ids)-1];
        } else {
            $response['twitter_oldest_id'] = $ids[count($ids)-1];
            $response['twitter_latest_id'] = $ids[0];
        }
    } else {
        $response['twitter_oldest_id'] = -1;
        $response['twitter_latest_id'] = -1;
    }
//     echo "<br>\r\n";
//     echo $response['twitter_oldest_id'];
//     echo "<br>\r\n";
//     echo $response['twitter_latest_id'];
}
$mutters = array_unique($response['mutters'] , SORT_REGULAR);

if($asc==1) {
    usort($mutters, "sort_mutter_asc");
} else {
    usort($mutters, "sort_mutter");
}

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
end:

$stdout = ob_get_contents();
ob_end_clean();

$response['error'] = $stdout;

// echo json_encode(gerErrorResponse("twitter", $response));
echo json_encode($response);