<?php
require_once ("init.php");

$fushianasan = getGetParam('fushianasan', 'false');

$twitterLoginAccount = getSessionParam('twitterLoginAccount', "");
$twitterLogin = !empty($twitterLoginAccount);

// $mo = getSessionParam('mo', 'true');
$mo = true;
setSessionParam('mo', $mo);
$hs = getSessionParam('hs', 'true');
$thumb = getSessionParam('thumb', 'true');
setGetParam('thumb', $thumb);

$smarty->assign("mo", $mo);
$smarty->assign("hs", $hs);
$smarty->assign("thumb", $thumb);

if($twitterLogin) {

	// トレンドを取得
	$lastGetTrendTime = getSessionParam('lastGetTrendTime', '0');

	$trends = getSessionParam('trends', array());

	if(
		($lastGetTrendTime + 60) < time()
		|| empty($trends)
		) {
		$trends = getTrendByWords('Tokyo');
		
		if(isset($trends[0]) && !empty($trends[0]->message)) {
			$message = $trends[0]->message;
		} else {
			setSessionParam('lastGetTrendTime', time());
			setSessionParam('trends', $trends);
		}
	}
}



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
$smarty->assign("twitterMyLists", $twitterMyList);
$smarty->assign("pawooMyLists", $pawooMyLists);
$smarty->assign("twitterMyFriends", $twitterMyFriends);
$smarty->assign("pawooMyFriends", $pawooMyFriends);
$smarty->assign("message", $message);
setSessionParam("message", "");

// テンプレートを表示する
$smarty->display("index.tpl");