<?php
if(empty($_SERVER["HTTPS"])) {
    header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
    exit();
}

$ini_array = parse_ini_file('/xampp/app/conf/sukipic.ini');

if ($_SERVER['REMOTE_ADDR'] === $ini_array["admin_host"])
    error_reporting(E_ALL);
else
    error_reporting(0);

ini_set( 'session.gc_maxlifetime', 604800 );
ini_set( 'session.cookie_lifetime', 604800 );
ini_set( 'session.cache_expire', 604800 );

date_default_timezone_set('Asia/Tokyo');

define("DbHost", $ini_array["db_host"]);
define("DbName", $ini_array["db_name"]);
define("DbAccount", $ini_array["db_account"]);
define("DbPassword", $ini_array["db_password"]);

define("Passphrase", $ini_array["passphrase"]);
define("EncMethod", $ini_array["enc_method"]);
define("AsyncCount", $ini_array["async_count"]);

/* ライブラリのロード */
$lib_path = $ini_array["lib_path"];
require_once($ini_array["smarty_class_path"]."/Smarty.class.php"); // Smartyライブラリをロード
require_once($lib_path."/vendor/twitteroauth/load.php"); // twitteroauthのライブラリをロード
require_once($lib_path."/vendor/thecodingcompany/php-mastodon/autoload.php"); // mastodon認証のライブラリをロード
require_once($lib_path."/load.php"); // 自作ライブラリをロード

/* セッション関連の設定 */
session_save_path('C:\xampp\session_tmp\sukipic');
$sessionID = getPostParam("mySessionID",'');
// if(!empty($sessionID)) {
//     session_id($sessionID);
// }
session_start();

/* ミニブログサーバの情報 */
define("PawooDomain", $ini_array["pawoo_domain"]);
define("MastodonTootsLimit", $ini_array["mastodon_toots_limit"]);


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

// myVarDump($_SESSION);

define("TwitterList", $ini_array["twitter_list"]);

define("Account", getSessionParam('account', ''));

$twitterLoginAccount = getSessionParam('twitterLoginAccount', '');
if (isset($twitterLoginAccount['id']))
    define("TwitterAccountID", $twitterLoginAccount['id']);
else
    define("TwitterAccountID", "");

$pawooLoginAccount = getSessionParam('pawooLoginAccount', '');
if (isset($pawooLoginAccount['id']))
    define("PawooAccountID", $pawooLoginAccount['id']);
else
    define("PawooAccountID", "");

/* Smartyのロード */
$smarty = new Smarty();

$smarty->assign("app_url", AppURL);
$smarty->assign("app_context", AppContext);

// テンプレートディレクトリとコンパイルディレクトリを読み込む
$smarty->template_dir = $ini_array["template_dir"];
$smarty->compile_dir = $ini_array["compile_dir"];

$smarty->assign("mylists", getSessionParam("twitter_mylists", array()));