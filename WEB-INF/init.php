<?php
$ini_array = parse_ini_file('/xampp/app/conf/sukipic.ini');

$black_list = array(
);
// mb_internal_encoding('SJIS');
// mb_http_output('SJIS');
// mb_http_input('SJIS');
// mb_regex_encoding('SJIS');
// var_dump($_SERVER);
if(!isset($_SERVER["HTTPS"])) {
    // ローカル実行
} else if(isset($_SERVER["REMOTE_ADDR"]) && in_array($_SERVER["REMOTE_ADDR"], $black_list)) {
    foreach($black_list as $list) {
        if(strpos($_SERVER["REMOTE_ADDR"], $list)!==false) {
            exit();
        }
    }
    if (empty($_SERVER["HTTPS"]) && ! (isset($_SERVER["CLIENTNAME"]) && ($_SERVER["CLIENTNAME"] === "DESKTOP-8S9QC5O"))) {
        header('Location: ' . $ini_array["protocol"] . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }

    if ($_SERVER['REMOTE_ADDR'] === $ini_array["admin_host"]) {
        error_reporting(E_ALL);
    } else {
        error_reporting(0);
    }
}

require_once "lib/vendor/autoload.php";

ini_set( 'session.gc_maxlifetime', 604800 );
ini_set( 'session.cookie_lifetime', 604800 );
ini_set( 'session.cache_expire', 604800 );

date_default_timezone_set('Asia/Tokyo');

define("DbHost", $ini_array["db_host"]);
define("DbName", $ini_array["db_name"]);
define("DbAccount", $ini_array["db_account"]);
define("DbPassword", $ini_array["db_password"]);

define("MediaDir", 'C:\xampp\htdocs\sukipic\media');

define("Passphrase", $ini_array["passphrase"]);
define("EncMethod", $ini_array["enc_method"]);
define("AsyncCount", $ini_array["async_count"]);
define("Twitter", "twitter.com");
define("Pawoo", "pawoo.net");

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

$arr_cookie_options = array (
    'expires' => time() + 60*60*24*30,
    'path' => '/matome/',
    'domain' => 'www.suki.pics', // leading dot for compatibility or use subdomain
    'secure' => true,     // or false
    'httponly' => false,    // or false
    'samesite' => 'None' // None || Lax  || Strict
);
setcookie("SESSIONID", session_id(), $arr_cookie_options);

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

define("TwitterList", $ini_array["twitter_list"]);

define("Account", getSessionParam('account', ''));

$twitterLoginAccount = getSessionParam('twitterLoginAccount', '');
if (isset($twitterLoginAccount['id']))
    define("TwitterAccountID", $twitterLoginAccount['id']);
else
    define("TwitterAccountID", "");

$pawooLoginAccount = getSessionParam('pawooLoginAccount', '');
if (isset($pawooLoginAccount['id'])) {
    define("PawooAccountID", $pawooLoginAccount['id']);
} else {
    define("PawooAccountID", "");
}

$pawoo_skip_list = explode(',', $ini_array["pawoo_skip_list"]);
setSessionParam("pawoo_skip_list", $pawoo_skip_list);

/* Smartyのロード */
$smarty = new Smarty();
$smarty->assign("AppURL", AppURL);
$smarty->assign("app_context", AppContext);
$target = "_blank";
$smarty->assign("target", $target);

// テンプレートディレクトリとコンパイルディレクトリを読み込む
$smarty->template_dir = $ini_array["template_dir"];
$smarty->compile_dir = $ini_array["compile_dir"];

$smarty->assign("mylists", getSessionParam("twitter_mylists", array()));

include('searchList.php');
$smarty->assign("searchList", $searchList);

$twitterMyList = array();
$pawooMyLists = array();
$twitterMyFriends = array();
$pawooMyFriends = array();