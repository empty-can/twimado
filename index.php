<?php
require_once ("init.php");

$woeid = "1118370"; // Tokyo
$api = 'trends/place';

$target = "_blank";

$params = array(
    "id" => $woeid
);

$account = getSessionParam('account', "");
$twitterLoginAccount = getSessionParam('twitterLoginAccount', "");
$pawooLoginAccount = getSessionParam('pawooLoginAccount', "");

$trends = getTwitterConnection()->get($api, $params);

// $userInfo = getSessionParam("twitter_user_info", "");

$twitterLogin = !empty($twitterLoginAccount);
$twitterList = isset($twitterLoginAccount["twitter_mylists"]) ? $twitterLoginAccount["twitter_mylists"] : "";
if($twitterLogin && empty($twitterList)) {
    $api = 'lists/list';
    $params = ["screen_name" => $twitterLoginAccount["screen_name"]];
    $twitterList = getTwitterConnection()->get($api, $params);
    $twitterLoginAccount["twitter_mylists"] = $twitterList;
}

$pawooLogin = !empty($pawooLoginAccount);

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
$smarty->assign("target", $target);
$smarty->assign("trends", $trends);
$smarty->assign("lists", $twitterList);

// テンプレートを表示する
$smarty->display("index.tpl");