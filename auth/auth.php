<?php
require_once ("init.php");
require_once ("init.php");

$name = getPostParam("name");
$password = getPostParam("password");

setSessionParam("name", $name);

if(!isUsers($name)) {
    addUsers($name, $password);
} else {
    
}

$userInfo = getSessionParam("twitter_user_info", "");
// myVarDump($userInfo);

$twitterLogin = (!empty(getSessionParam("twitter_access_token", ""))
    && !empty(getSessionParam("twitter_access_token_secret", "")));

$lists = getSessionParam("twitter_mylists", "");
if($twitterLogin) {
    $api = 'lists/list';
    $params = array(
        "screen_name" => $userInfo->screen_name
    );
    setSessionParam("twitter_id", $userInfo->id);
    
    $twitter_access_token = getSessionParam("twitter_access_token", "");
    $twitter_access_token_secret = getSessionParam("twitter_access_token_secret", "");
    //  echo "twitter_access_token:".$twitter_access_token."<br>\r\n";
    //  echo "twitter_access_token_secret:".$twitter_access_token_secret."<br>\r\n";
    
    setTokens($userInfo->id, $userInfo->name."@".$userInfo->screen_name, $twitter_access_token, $twitter_access_token_secret);
    
    if(empty($lists)) {
        $lists = getTwitterConnection($twitter_access_token, $twitter_access_token_secret)->get($api, $params);
        setSessionParam("twitter_mylists", $lists);
    }
}

// Pawoo認証チェック
$pawooLogin = false;
$pawooAccessToken = getSessionParam("pawoo_access_token", "");
$pawooAccount = getSessionParam("pawoo_account", "");

if(!empty($pawooAccessToken)) {
    if(empty($pawooAccount)) {
        $connection = getMastodonConnection(PawooDomain, $pawooAccessToken);
        $pawooAccount = $connection->executeGetAPI('api/v1/accounts/verify_credentials');
        setSessionParam("pawoo_account", $pawooAccount);
        setSessionParam("pawoo_id", $pawooAccount["id"]);
    }
    
    $pawooLogin = true;
}
if($pawooLogin) {
    setTokens($pawooAccount["id"], $pawooAccount["display_name"]."@".$pawooAccount["username"], $pawooAccessToken, "");
}

$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());
$smarty->assign("title", "ツイ窓");
$smarty->assign("AppURL", AppURL);
$smarty->assign("userInfo", $userInfo);
$smarty->assign("pawooAccount", $pawooAccount);
$smarty->assign("pawooAccessToken", $pawooAccessToken);
$smarty->assign("twitterLogin", $twitterLogin);
$smarty->assign("pawooLogin", $pawooLogin);
$smarty->assign("lists", $lists);

// テンプレートを表示する
$smarty->display("auth.tpl");