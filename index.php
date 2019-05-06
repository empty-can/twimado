<?php
require_once ("init.php");

$mo = getSessionParam('mo', 'true');
$smarty->assign("mo", $mo);

$hs = getSessionParam('hs', 'true');
$smarty->assign("hs", $hs);

$thumb = getSessionParam('thumb', 'true');
setGetParam('thumb', $thumb);
$smarty->assign("thumb", $thumb);

$account = getSessionParam('account', "");
$twitterLoginAccount = getSessionParam('twitterLoginAccount', "");
$pawooLoginAccount = getSessionParam('pawooLoginAccount', "");


$connection = getTwitterConnection();

$keyword = 'Tokyo';
$trends = $connection->get('geo/search', ['query' => $keyword]);

$idokeido = $trends->result->places[0]->centroid;

$params = array(
    "lat" => $idokeido[1]
    , "long" => $idokeido[0]
);

$woeid = $connection->get('trends/closest', $params)[0]->woeid;
$trends = $connection->get('trends/place', ['id'=>$woeid]);

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
$smarty->assign("message", getSessionParam("message", ""));
setSessionParam("message", "");

// テンプレートを表示する
$smarty->display("index.tpl");