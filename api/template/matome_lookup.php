<?php
require_once ("init.php");


$param = new Parameters();

if(isPost()) {
    $param->constructFromPostParameters();
} else {
    $param->constructFromGetParameters();
}

$param->setInitialValue('hs', 'true');
$param->setInitialValue('thumb', 'true');
$param->setInitialValue('target_id', '');
$param->setInitialValue('asc', 1);

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

    $twitter_param->moveValue('twitter_id', 'id');
    $twitter_param->setInitialValue('count', '25');

    $twitter_oldest_id = $twitter_param->getValue('twitter_oldest_id');
    $twitter_latest_id = $twitter_param->getValue('twitter_latest_id');
    $target_id = $twitter_param->getValue('target_id', "");
    $asc  = $twitter_param->getValue('asc', 1);
    $count = $twitter_param->getValue('count', '100');

    if($asc==1) {
        $ids = getMutterIds($target_id, $twitter_latest_id, $count, $asc);
    } else {
        $ids = getMutterIds($target_id, $twitter_oldest_id, $count, $asc);
    }

    if(!empty($ids)) {
        $twitter_param->setParam('ids', $ids);
        $twitter_param->moveValue('twitter_oldest_id', 'max_id');

        $twitter_result = getMutters($api, $twitter_param->parameters, $twitter_oldest_id);

        $twitter_oldest_id = $twitter_result['oldest_id'];
        $twitter_latest_id = $twitter_result['latest_id'];
        $response['mutters']  = array_merge($response['mutters'], $twitter_result['mutters']);

        // error_log("twitter_oldest_id:$twitter_oldest_id");
        // error_log("twitter_latest_id:$twitter_latest_id");
        // error_log("tweet_count:".count($response['mutters']));

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
    $html = $smarty->fetch("parts/matome_mutter.tpl");
    $response['mutters'][$mutter['originalId']] = $html;
}
end:

$stdout = ob_get_contents();
ob_end_clean();

$response['error'] = $stdout;

// echo json_encode(gerErrorResponse("twitter", $response));
echo json_encode($response);