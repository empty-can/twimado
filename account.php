<?php
require_once ("init.php");

$mo = getSessionParam('mo', 'true');
$hs = getSessionParam('hs', 'true');
$thumb = getSessionParam('thumb', 'true');
setGetParam('thumb', $thumb);

$smarty->assign("mo", $mo);
$smarty->assign("hs", $hs);
$smarty->assign("thumb", $thumb);


$api = 'users/search';  // アクセスするAPI

/*------------ パラメータの取得設定 ------------*/
$param = new Parameters();
$param->constructFromGetParameters();
$param->required = ["q"];

$param->setInitialValue('count', '20');
/*-----------------------------------------*/

// アクセストークンの取得
if(!isset($account))
	$account="";

if(!isset($passenger_id)) {
	if(isset($twitterLoginAccount['id'])) {
		$passenger_id=$twitterLoginAccount['id'];
	} else {
		$passenger_id="";
	}
}

$tokens = getTwitterTokens($account, $passenger_id, true);

// APIアクセス
$api_result = getTwitterConnection($tokens->token, $tokens->secret)
                ->get($api, $param->parameters);

// APIアクセスのエラー確認
if (isset($api_result->errors)) {
    echo "APIの実行に失敗しました。";
    foreach ($api_result->errors as $error) {
        echo "<br>\r\nエラーコード：".$error->code;
        echo "<br>\r\nメッセージ：".$error->message;
    }
    exit();
}

$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());


$smarty->assign("title", AppName);
$smarty->assign("AppURL", AppURL);
$smarty->assign("q", $param->parameters['q']);
$smarty->assign("accounts", $api_result);
$smarty->assign("message", getSessionParam("message", ""));
setSessionParam("message", "");

// テンプレートを表示する
$smarty->display("account.tpl");