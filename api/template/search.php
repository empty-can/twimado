<?php

require_once ("init.php");

$domain = getGetParam('domain', 'twitter');
$hs = getGetParam('hs', 'true');
$q = getGetParam('q', '');
$pawoo_oldest_id = getGetParam('pawoo_oldest_id', '');
$twitter_oldest_id = getGetParam('twitter_oldest_id', '');
$count = getGetParam('count', '100');
$thumb = getGetParam('thumb', 'true');

$mutters = array();

$response = array();
$response['mutters'] = array();

ob_start();

// pawooの検索結果取得（未実装）
if(contains($domain, 'pawoo') && ($pawoo_oldest_id>-1)) {
}
// Twitterの検索結果取得
if(contains($domain, 'twitter') && ($twitter_oldest_id>-1)) {
    
    do {
        $params = array(
            "q" => $q
            , "count" => $count
        );
        
        if (! empty($twitter_oldest_id)) {
            $params['max_id'] = $twitter_oldest_id;
        }
        
        $tmpResponse = getRequest(AppURL . '/api/twitter/search.php', $params);
        
//                 var_dump($tmpResponse);
        
        $tmpResponse = json_decode($tmpResponse);
        
//         var_dump($tmpResponse);
        
        if(!is_array($tmpResponse))
            $tmpResponse = obj_to_array($tmpResponse);

//         var_dump($tmpResponse);
            
//             var_dump($tmpResponse['error']);
        $twitter_oldest = $tmpResponse['oldest_mutter'];
        
        $mutters = array_merge($mutters, $tmpResponse['mutters']);
        
        if (isset($twitter_oldest['id']))
            $twitter_oldest_id = $twitter_oldest['id'];
        else
            $twitter_oldest_id = - 1;
            
    } while (count($mutters) < 1 && $twitter_oldest_id>0);
}

// var_dump($mutters);

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
    $response['mutters'][$mutter['time']] = $html;
//     $response['mutters'][$mutter['id']] = htmlspecialchars($smarty->fetch("parts/mutter.tpl"));
}

$response['pawoo_oldest_id'] = $pawoo_oldest_id;
$response['twitter_oldest_id'] = $twitter_oldest_id;

$response['error'] = ob_get_contents();
ob_end_clean();

// echo $response['error'];
echo json_encode($response);