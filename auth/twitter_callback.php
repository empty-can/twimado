<?php
require_once ("init.php");

//Callback URL
define('Callback', AppURL.'/auth/twitter_callback.php');

//ライブラリを読み込む
use Abraham\TwitterOAuth\TwitterOAuth;

$oauth_token_session = getSessionParam('oauth_token');
$oauth_token_secret_session = getSessionParam('oauth_token_secret');
$oauth_verifier = getGetParam('oauth_verifier');
$oauth_token = getGetParam('oauth_token');

//oauth_tokenとoauth_verifierを取得
if($oauth_token_session == $oauth_token and $oauth_verifier){

    //Twitterからアクセストークンを取得する
    $connection = new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret, $oauth_token_session, $oauth_token_secret_session);
    $access_token = $connection->oauth('oauth/access_token',
        array('oauth_verifier' => $oauth_verifier
            , 'oauth_token'=> $oauth_token)
        );

    //取得したアクセストークンでユーザ情報を取得
    $user_connection = new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $twitterAccount = obj_to_array($user_connection->get('account/verify_credentials'));

    //各値をセッションに入れる
    setSessionParam('twitterLoginAccount', $twitterAccount);
    setSessionParam('twitterAccessToken', new AccessToken($access_token['oauth_token'], $access_token['oauth_token_secret']));


    // アプリにログインしていればDBへ連携情報を登録する
    $account_id = getSessionParam("account", "");
    if(!empty($account_id)) {
        $service_user_info = [
            'id' => $twitterAccount["id"]
            ,'user_name' => $twitterAccount["screen_name"]
            ,'display_name' => $twitterAccount["name"]
            ,'token' => $access_token['oauth_token']
            ,'token_secret' => $access_token['oauth_token_secret']
        ];

        register_pairing($account_id, "twitter", $service_user_info);
    } else {
        setPassengerTokens($twitterAccount['id'], 'twitter', $twitterAccount['name'], $twitterAccount['screen_name'], $access_token['oauth_token'],  $access_token['oauth_token_secret']);
    }    
    
    define("TwitterAccountID", $twitterAccount['id']);
    define("TwitterOauthToken", $access_token['oauth_token']);
    define("TwitterOauthTokenSecret", $access_token['oauth_token_secret']);

    $_SESSION['oauth_token'] = "";
    $_SESSION['oauth_token_secret'] = "";

    header('Location: '.AppURL);
    exit();
}else{
    header('Location: '.AppURL);
    exit();
}