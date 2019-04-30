<?php
require_once ("init.php");

//Callback URL
define('Callback', AppURL.'/auth/twitter_callback.php');

//ライブラリを読み込む
use Abraham\TwitterOAuth\TwitterOAuth;

//TwitterOAuthのインスタンスを生成し、Twitterからリクエストトークンを取得する
$connection = new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret);
$request_token = $connection->oauth("oauth/request_token", array("oauth_callback" => Callback));

//リクエストトークンはcallback.phpでも利用するのでセッションに保存する
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$params = array(
    "oauth_token" => $request_token['oauth_token']
);

// Twitterの認証画面へリダイレクト
$url = $connection->url("oauth/authenticate", $params);

header('Location: ' . $url);
exit();
