<?php
$ini_array = parse_ini_file('twimado.ini');

date_default_timezone_set('Asia/Tokyo');

// var_dump($ini_array);

define("DbHost", $ini_array["db_host"]);
define("DbName", $ini_array["db_name"]);
define("DbAccount", $ini_array["db_account"]);
define("DbPassword", $ini_array["db_password"]);

/* ライブラリのロード */
$lib_path = $ini_array["lib_path"];
require_once($ini_array["smarty_class_path"]."/Smarty.class.php"); // Smartyライブラリをロード
require_once($lib_path."/vendor/twitteroauth/load.php"); // twitteroauthのライブラリをロード
require_once($lib_path."/vendor/thecodingcompany/php-mastodon/autoload.php"); // mastodon認証のライブラリをロード
require_once($lib_path."/load.php"); // 自作ライブラリをロード

/* セッション関連の設定 */
session_save_path('C:\xampp\htdocs\tmp');
$sessionID = getGetParam("mySessionID",'');
// if(!empty($sessionID)) {
//     session_id($sessionID);
// }
session_start();

/* ミニブログサーバの情報 */
define("PawooDomain", $ini_array["pawoo_domain"]);


/* Twitterトークン情報の設定 */
define("TwitterAppToken", $ini_array["twitter_consumer_key"]);
define("TwitterAppTokenSecret", $ini_array["twitter_consumer_secret"]);
define("TwitterAccessToken", $ini_array["twitter_access_token"]);
define("TwitterAccessTokenSecret", $ini_array["twitter_access_token_secret"]);

/* Pawooトークン情報の設定 */
define("PawooID", $ini_array["pawoo_id"]);
define("PawooClientID", $ini_array["pawoo_client_id"]);
define("PawooClientSecret", $ini_array["pawoo_client_secret"]);
define("PawooAccessToken", $ini_array["pawoo_access_token"]);

/* サイト情報の設定 */
define("AppName", $ini_array["app_name"]);
define("AppURL", $ini_array["protocol"].':'.$ini_array["app_url"]);
define("AppContext", $ini_array["app_context"]);
define("ErrorMessage", $ini_array["error_message"]);

define("TwitterList", $ini_array["twitter_list"]);

/* Smartyのロード */
$smarty = new Smarty();

$smarty->assign("app_url", AppURL);
$smarty->assign("app_context", AppContext);

// テンプレートディレクトリとコンパイルディレクトリを読み込む
$smarty->template_dir = $ini_array["template_dir"];
$smarty->compile_dir = $ini_array["compile_dir"];
