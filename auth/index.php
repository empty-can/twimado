<?php

exit();

$_SESSION = array();

require_once ("init.php");

$account = getPostParam("account");
$password = getPostParam("password");
$button = getPostParam("button");
$logout = getPostParam("logout", "");
$message = "";

$logined = false;

// ログアウト処理
if($logout=="logout") {
    setSessionParam("account", "");
    $message = "ログアウトしました。";

// 送信されたアカウントデータチェック
} else if(!empty($button) && (empty($account) || empty($password))) {
        $message = "アカウントとパスワードは両方入力してください。";
       
// ログイン処理
} else if($button=="login") {
    if (!exist_account($account)) {
        $message = "アカウント $account は登録されていません。";
    } else {
        $rand = login($account, $password);
        if (!empty($rand)) {
            $all_pairs = select_all_pairs($account);
            $message = "アカウント $account でログインに成功しました。";
            $logined = true;
        } else {
            $message = "アカウント $account でログインに失敗しました。";
        }
    }

// アカウント登録処理
} else if ($button == "register") {
    if (exist_account($account)) {
        $message = "アカウント $account は既に登録されています。";
    } else {
        $encrypted = encrypt($password);
        $result = register_account($account, $encrypted['data'], $encrypted['key']);
        if(!empty($result)) {
            $message = "アカウント $account は正常に登録されました。";
            $rand = login($account, $password);
            if (!empty($rand)) {
                $message = "アカウント $account でログインに成功しました。";
                $logined = true;
            }
        } else {
            $message = "アカウント $account の登録に失敗しました。";
        }
    }

// その他
} else if(!empty($button)){
    $message = "不正なリクエストです。";
    
    header('Location: /');
    exit();
} else {
    $message = "不正なリクエストです。";
    $message = "";
    
    header('Location: /');
    exit();
}

setSessionParam("message", $message);

if($logined) {
    setSessionParam("account", $account);
    setSessionParam("account_rand", $rand);
    $all_pairs = select_all_pairs($account);
    
    // ペアリング情報から各サービスのアカウント情報を取得
    foreach ($all_pairs as $pair) {
        loadAccountInfo($pair);
    }
}

header('Location: /');
exit();

// myVarDump($all_pairs);

$login = !empty(getSessionParam("account", ""));
$account = getSessionParam("account", "");
$twitterLoginAccount = getSessionParam("twitterLoginAccount", array());
$pawooLoginAccount = getSessionParam("pawooLoginAccount", array());

$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());
$smarty->assign("title", "ログイン画面 - ".AppName);
$smarty->assign("AppURL", AppURL);
$smarty->assign("twitterLoginAccount", $twitterLoginAccount);
$smarty->assign("pawooLoginAccount", $pawooLoginAccount);
$smarty->assign("message", $message);
$smarty->assign("account", $account);
$smarty->assign("login", $login);

// テンプレートを表示する
$smarty->display("auth.tpl");