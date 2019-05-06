<?php
require_once ("init.php");

$account = getSessionParam("account","");

if(!empty($account)) {
    delete_account($account);
}

logout();

/* セッション関連の設定 */
session_save_path('C:\xampp\session_tmp\sukipic');
session_start();

setSessionParam("message", "アカウント情報を削除しました。");

header('Location: /');
exit();