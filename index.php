<?php
require_once ("init.php");

$mo = getSessionParam('mo', 'true');
$hs = getSessionParam('hs', 'true');
$thumb = getSessionParam('thumb', 'true');
setGetParam('thumb', $thumb);

$smarty->assign("mo", $mo);
$smarty->assign("hs", $hs);
$smarty->assign("thumb", $thumb);

$account = getSessionParam('account', "");
$twitterLoginAccount = getSessionParam('twitterLoginAccount', "");
$pawooLoginAccount = getSessionParam('pawooLoginAccount', "");

$twitterLogin = !empty($twitterLoginAccount);

// Twitter にログインしている場合の処理
if($twitterLogin) {
    //     $tokens = getSessionParam("twitterAccessToken", "");
    
    // フォローの取得
    $twitterMyFriends = getArrayParam($twitterLoginAccount, "twitter_my_friends", array());
    
    if(empty($twitterMyFriends)) {
        $params = [
            "user_id" => getArrayParam($twitterLoginAccount, "id")
            , "count" => "200"
        ];
        $result = getTwitterConnection()->get('friends/list', $params);
        
        if(isset($result->users)) {
            $twitterMyFriends = $result->users;
            usort($twitterMyFriends, "sort_twitter_account_by_followers_count");
            $twitterLoginAccount["twitter_my_friends"] = $twitterMyFriends;
        }
    }
    
    setSessionParam('twitterLoginAccount', $twitterLoginAccount);
    
    // マイリストの取得
    $twitterList = getArrayParam($twitterLoginAccount, "twitter_mylists", array());
    
    if(empty($twitterList)) {
        $params = ["user_id" => getArrayParam($twitterLoginAccount, "id")];
        $twitterList = getTwitterConnection()->get('lists/list', $params);
        $twitterLoginAccount["twitter_mylists"] = $twitterList;
    }
    
    setSessionParam('twitterLoginAccount', $twitterLoginAccount);
}



$pawooLogin = !empty($pawooLoginAccount);
// Pawoo にログインしている場合の処理
if($pawooLogin) {
    $tokens = getSessionParam("pawooAccessToken", "");
    $access_token = getObjectProps($tokens, "access_token");
    
    // フォローの取得
    $pawooMyFriends = getArrayParam($pawooLoginAccount, "pawoo_my_friends", array());
    
    if(empty($pawooMyFriends)) {
        $id = getArrayParam($pawooLoginAccount, "id");
        
        $params = ["limit" => "80"];
        $pawooMyFriends = getMastodonConnection(PawooDomain, $access_token)
                    ->executeGetAPI("/api/v1/accounts/$id/following", $params);
        
        if(!empty($pawooMyFriends)) {
            usort($pawooMyFriends, "sort_pawoo_account_by_followers_count");
            $pawooLoginAccount["pawoo_my_friends"] = $pawooMyFriends;
        }
    }
}

// トレンドを取得
$trends = getTrendByWords('Tokyo');

$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());


$smarty->assign("title", AppName);
$smarty->assign("AppURL", AppURL);
// $smarty->assign("userInfo", $userInfo);
$smarty->assign("account", $account);
$smarty->assign("pawooLoginAccount", $pawooLoginAccount);
$smarty->assign("twitterLoginAccount", $twitterLoginAccount);
$smarty->assign("twitterLogin", $twitterLogin);
$smarty->assign("pawooLogin", $pawooLogin);
$smarty->assign("trends", $trends);
$smarty->assign("lists", $twitterList);
$smarty->assign("twitterMyFriends", $twitterMyFriends);
$smarty->assign("pawooMyFriends", $pawooMyFriends);
$smarty->assign("message", getSessionParam("message", ""));
setSessionParam("message", "");

// テンプレートを表示する
$smarty->display("index.tpl");