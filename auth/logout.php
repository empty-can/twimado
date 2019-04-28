<?php
require_once ("init.php");
//セッション変数を全て解除
$_SESSION = array();

//セッションクッキーの削除
if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", '', time() - 1800, '/');
}
if (isset($_COOKIE["login_cookie_id"])) {
    setcookie("login_cookie_id", '', time() - 1800, '/twimado/', 'www.suki.pics', false, false);
}

//セッションを破棄する
session_destroy();
header('Location: /');
exit();