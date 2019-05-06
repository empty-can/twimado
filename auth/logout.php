<?php
require_once ("init.php");

logout();

/* セッション関連の設定 */
session_save_path('C:\xampp\session_tmp\sukipic');
session_start();

setSessionParam("message", "正常にログアウトしました。");

header('Location: /');
exit();