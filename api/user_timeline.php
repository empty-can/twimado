<?php

require_once ("init.php");

$domain = getGetParam('domain', '');
$id = getGetParam('id', '');
$hidden_sensitive = getGetParam('hidden_sensitive', 'true');
$max_id = getGetParam('oldest_id', '');
$count = getGetParam('count', '');

if(empty($domain)) {
    echo "ドメインの指定がありません。";
    exit();
}

$params = array(
    "hidden_sensitive" => $hidden_sensitive
);

$api =  "";
$response = null;

if($domain=="twitter") {
    $api = AppURL . 'api/twitter/user_timeline.php';
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
    $api = AppURL . 'api/pawoo/user_timeline.php';
    $params["id"] = $id;
    if(!empty($max_id)) {
        $params["max_id"] = $max_id;
    }
    if(empty($count)) {
        $params['count'] = "40";
    } else {
        $params['count'] = "$count";
    }
    
    $response = getRequest($api, $params);
} else {
    echo "対応するAPIがありません。";
    exit();
}

if(empty($response)) {
    echo "APIからのデータ取得に失敗しました。";
    exit();
} else {
    $response = json_decode($response);
    $oldest_id = $response->oldest_mutter->id;
}

$mutters = $response->mutters;

$response = array();
$response['mutters'] = array();

// テンプレートを表示する
$hidden_sensitive = ($hidden_sensitive=='true') ? true : false;
$smarty->assign("hidden_sensitive", $hidden_sensitive);
$smarty->assign("app_url", AppURL);

foreach ($mutters as $mutter) {
    $arrayed_mutter = obj_to_array($mutter);
    $smarty->assign("mutter", $arrayed_mutter);
    $response['mutters'][$arrayed_mutter['id']] = $smarty->fetch("parts/mutter.tpl");
//     $response['mutters'][$mutter['id']] = htmlspecialchars($smarty->fetch("parts/mutter.tpl"));
}

$response['oldest_id'] = $oldest_id;

echo json_encode($response);