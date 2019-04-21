<?php
require_once ("init.php");

$domain = getGetParam('domain', 'pawoo');
$hs = getGetParam('hs', 'true');
$pawoo_oldest_id = getGetParam('pawoo_oldest_id', '');
$pawoo_id = getGetParam('pawoo_id', '');
$twitter_oldest_id = getGetParam('twitter_oldest_id', '');
$twitter_id = getGetParam('twitter_id', '');
$count = getGetParam('count', '');
$thumb = getGetParam('thumb', 'true');

$mutters = array();
$tmp_mutters = array();

$response = array();
$response['mutters'] = array();
$errors = [];

ob_start();

// pawooの自分TL取得
if (contains($domain, 'pawoo') && ($pawoo_oldest_id != - 1)) {

    do {
        $api = AppURL . '/api/pawoo/home_timeline.php';
        
        $params = array(
            "id" => $pawoo_id
            , "hs" => $hs
        );
        
        if(!empty($pawoo_oldest_id)) {
            $params["max_id"] = $pawoo_oldest_id;
        }
        if(empty($count)) {
            $params['limit'] = "40";
        } else {
            $params['limit'] = "$count";
        }
        $params['limit'] = "40";
//         myVarDump($params);
        
        $tmp = getRequest($api, $params);
        
        $response = json_decode($tmp, true);
//         myVarDump($response);
        $errors[] = array("pawoo"=>$response['error']);

        if (! is_array($response))
            break;

        $pawoo_oldest = $response['oldest_mutter'];
        // usort($pawoos, "sort_mutter_by_time");
        // myVarDump(array_last($pawoos));

        $tmp_mutters = array_merge($tmp_mutters, $response['mutters']);
        // myVarDump($oldest_id);
        // myVarDump(count($mutters));

        if (isset($pawoo_oldest['id']))
            $pawoo_oldest_id = $pawoo_oldest['id'];
        else
            $pawoo_oldest_id = - 1;
    } while (count($tmp_mutters) < 1 && $pawoo_oldest_id > 0);
}

$mutters = array_merge($mutters, $tmp_mutters);

$tmp_mutters = array();

// 自分のイラストリストTL取得
if (contains($domain, 'twitter') && ($twitter_oldest_id != - 1)) {

    do {
        $api = AppURL . '/api/twitter/home_timeline.php';
        
        $params = array(
            "id" => $twitter_id
            , "hs" => $hs
        );

        if (empty($count)) {
            $params['count'] = "200";
        } else {
            $params['count'] = "$count";
        }

        if (! empty($twitter_oldest_id)) {
            $params['max_id'] = $twitter_oldest_id;
        }

        $tmp = getRequest($api, $params);
        // myVarDump($tmp);
        $response = json_decode($tmp, true);
        $errors[] = array("twitter"=>$response['error']);

        if (! is_array($response))
            break;

        // myVarDump($response);
        $twitter_oldest = $response['oldest_mutter'];

        $tmp_mutters = array_merge($tmp_mutters, $response['mutters']);

        if (isset($twitter_oldest['id']))
            $twitter_oldest_id = $twitter_oldest['id'];
        else
            $twitter_oldest_id = - 1;
    } while (count($tmp_mutters) < 1 && $twitter_oldest_id > 0);
}

$mutters = array_merge($mutters, $tmp_mutters);

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

$response['pawoo_oldest_id'] = $pawoo_oldest_id;
$response['twitter_oldest_id'] = $twitter_oldest_id;

$errors[] = array("template"=>ob_get_contents());
ob_end_clean();

// $response['error'] = $errors;

echo json_encode($response);