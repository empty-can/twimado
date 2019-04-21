<?php

require_once ("init.php");

$domain = getGetParam('domain', 'twitterpawoo');
$hs = getGetParam('hs', 'true');
$q = urlencode(getGetParam('q', ''));
$pawoo_oldest_id = getGetParam('pawoo_oldest_id', '');
$twitter_oldest_id = getGetParam('twitter_oldest_id', '');
$count = getGetParam('count', '100');
$thumb = getGetParam('thumb', 'true');

$mutters = array();
$tmp_mutters = array();

$response = array();
$response['mutters'] = array();

ob_start();

// pawooの検索結果取得（pawooはツイートのキーワード検索未対応）
if(contains($domain, 'pawoo') && ($pawoo_oldest_id>-1)) {
    
    do {
        $params = array(
            "tag" => mb_ereg_replace('%23', '', $q)
            , "limit" => $count
        );
        
        if (! empty($pawoo_oldest_id)) {
            $params['max_id'] = $pawoo_oldest_id;
        }
        
        if (! empty($pawoo_access_token)) {
            $params['access_token'] = $pawoo_access_token;
        }
        
        $tmpResponse = getRequest(AppURL . '/api/pawoo/tag_timelines.php', $params);
        
//                          var_dump($tmpResponse);
        
        $tmpResponse = json_decode($tmpResponse);
        
        //         var_dump($tmpResponse);
        
        if(!is_array($tmpResponse))
            $tmpResponse = obj_to_array($tmpResponse);
            
            //         var_dump($tmpResponse);
            
            //             var_dump($tmpResponse['error']);
            $pawoo_oldest = $tmpResponse['oldest_mutter'];
            
            $tmp_mutters = array_merge($tmp_mutters, $tmpResponse['mutters']);
            
            if (isset($pawoo_oldest['id']))
                $pawoo_oldest_id = $pawoo_oldest['id'];
            else
                $pawoo_oldest_id = - 1;
                    
    }while(count($tmp_mutters)<1 && $pawoo_oldest_id>0);
}

$mutters = array_merge($mutters, $tmp_mutters);
$tmp_mutters = array();

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

//             myVarDump($tmpResponse);
            
//             var_dump($tmpResponse['error']);
        $twitter_oldest = $tmpResponse['oldest_mutter'];
        
        $tmp_mutters = array_merge($tmp_mutters, $tmpResponse['mutters']);
        
        if (isset($twitter_oldest['id']))
            $twitter_oldest_id = $twitter_oldest['id'];
        else
            $twitter_oldest_id = - 1;
            
    } while (count($tmp_mutters) < 1 && $twitter_oldest_id>0);
}

// var_dump($mutters);

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

$response['pawoo_oldest_id'] = $pawoo_oldest_id;
$response['twitter_oldest_id'] = $twitter_oldest_id;

$response['error'] = ob_get_contents();
ob_end_clean();

// echo $response['error'];
echo json_encode($response);