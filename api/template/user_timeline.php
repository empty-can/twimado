<?php

require_once ("init.php");

$domain = getGetParam('domain', '');
$id = getGetParam('id', '');
$hs = getGetParam('hs', 'true');
$max_id = getGetParam('oldest_id', '');
$count = getGetParam('count', '');
$thumb = getGetParam('thumb', 'true');

$response = array();
$response['mutters'] = array();

if($max_id==-1){
    $response['oldest_id'] = -1;
    echo json_encode($response);
    exit();
}

if(empty($domain)) {
    $response['oldest_id'] = -1;
    $response['error'] = "ドメインの指定がありません。";
    echo json_encode($response);
    exit();
}

$params = array(
    "hs" => $hs
);

$api =  "";

ob_start();

if($domain=="twitter") {
    $api = AppURL . '/api/twitter/user_timeline.php';
    $params["id"] = $id;
    if(!empty($max_id)) {
        $params["max_id"] = $max_id;
    }
    if(empty($count)) {
        $params['count'] = "200";
    } else {
        $params['count'] = "$count";
    }
    
    $response = getRequest($api, $params);
} else if($domain=="pawoo") {
    $api = AppURL . '/api/pawoo/user_timeline.php';
    $params["id"] = $id;
    if(!empty($max_id)) {
        $params["max_id"] = $max_id;
    }
    if(empty($count)) {
        $params['limit'] = "40";
    } else {
        $params['limit'] = "$count";
    }
    
    $response = getRequest($api, $params);
} else {
    echo "対応するAPIがありません。";
    exit();
}
// var_dump($response);
// var_dump(json_decode($response));
// var_dump(empty($response));

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
} else {
    $response = json_decode($response);
    
    if(isset($response->oldest_mutter) && !empty($response->oldest_mutter))
        $oldest_id = $response->oldest_mutter->id;
    else
        $oldest_id = -1;
}

$mutters = $response->mutters;

$mutters = array_unique(obj_to_array($mutters), SORT_REGULAR);
usort($mutters, "sort_mutter_by_time");

// テンプレートを表示する
$hs = ($hs=='true') ? true : false;
$thumb = ($thumb=='true') ? true : false;
$smarty->assign("hs", $hs);
$smarty->assign("thumb", $thumb);

$response = array();
$response['mutters'] = array();
foreach ($mutters as $mutter) {
    $smarty->assign("mutter", $mutter);
    $response['mutters'][$mutter['time']] = $smarty->fetch("parts/mutter.tpl");
//     $response['mutters'][$mutter['id']] = htmlspecialchars($smarty->fetch("parts/mutter.tpl"));
}
// myVarDump($response['mutters']);

$response['oldest_id'] = $oldest_id;

$response['error'] = ob_get_contents(); 
ob_end_clean();

// myVarDump(json_encode($response));
echo json_encode($response);