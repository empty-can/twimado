<?php

require_once ("init.php");

$domain = getGetParam('domain', 'twitterpawoo');
$hs = getGetParam('hs', 'true');
$pawoo_oldest_id = getGetParam('pawoo_oldest_id', '');
$twitter_oldest_id = getGetParam('twitter_oldest_id', '');
$count = getGetParam('count', '');
$thumb = getGetParam('thumb', 'true');

$mutters = array();

$response = array();
$response['mutters'] = array();

// pawooの自分TL取得
if(contains($domain, 'pawoo') && ($pawoo_oldest_id!=-1)) {
    
    do {
        $params = array(
            "domain" => "pawoo.net",
            "api" => "api/v1/timelines/home",
            "local" => "true"
        );
        
        if(empty($count)) {
            $params['limit'] = "40";
        } else {
            $params['limit'] = "$count";
        }
        
        if(!empty($pawoo_oldest_id)) {
            $params['max_id'] = $pawoo_oldest_id;
        }
        
        $response = json_decode(getRequest(AppURL . '/api/toots.php', $params), true);
        
        
        if(!is_array($response))
            break;
        
        $pawoo_oldest = $response['oldest_mutter'];
        //         usort($pawoos, "sort_mutter_by_time");
        //         myVarDump(array_last($pawoos));
        
        $mutters = array_merge($mutters, $response['mutters']);
            // myVarDump($oldest_id);
            // myVarDump(count($mutters));
            
        if (isset($pawoo_oldest['id']))
            $pawoo_oldest_id = $pawoo_oldest['id'];
        else
            $pawoo_oldest_id = - 1;
    }while(count($mutters)<1 && $pawoo_oldest_id>0);
}

// // myVarDump($oldest_id);
// // myVarDump(count($mutters));

// 横島ボットTL取得
// if(contains($domain, 'twitter')) {
//     do {
//         $params = array(
//             "user_id" => "766219679631183872",
//             "count" => "20"
//         );
        
//         if (! empty($twitter_oldest_id)) {
//             $params['max_id'] = $twitter_oldest_id;
//         }
        
//         $response = json_decode(postRequest(AppURL . '/api/twitter/user_timeline.php', $params), true);
        
//         if(!is_array($response))
//             break;
        

// if (isset($twitter_oldest['id']))
//     $twitter_oldest_id = $twitter_oldest['id'];
// else
//     $twitter_oldest_id = - 1;
//     } while (count($mutters) < 30);
// }

// 自分のイラストリストTL取得
if(contains($domain, 'twitter') && ($twitter_oldest_id!=-1)) {
    
    do {
        $params = array(
            "list_id" => TwitterList
        );
        
        if(empty($count)) {
            $params['count'] = "200";
        } else {
            $params['count'] = "$count";
        }
        
        if (! empty($twitter_oldest_id)) {
            $params['max_id'] = $twitter_oldest_id;
        }
        
        $tmp = getRequest(AppURL . '/api/twitter/list.php', $params);
//         myVarDump($tmp);
        $response = json_decode($tmp, true);
        
        if(!is_array($response))
            break;
            
            //         myVarDump($response);
        $twitter_oldest = $response['oldest_mutter'];
        
        $mutters = array_merge($mutters, $response['mutters']);
        
        if (isset($twitter_oldest['id']))
            $twitter_oldest_id = $twitter_oldest['id'];
        else
            $twitter_oldest_id = - 1;
        
    } while (count($mutters) < 1 && $twitter_oldest_id>0); 
}

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

$response['pawoo_oldest_id'] = $pawoo_oldest_id;
$response['twitter_oldest_id'] = $twitter_oldest_id;

echo json_encode($response);