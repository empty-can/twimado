<?php
require_once ("init.php");

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Twitter のコネクションを取得するための共通関数
 * 
 * @param string $user_token
 * @param string $user_token_secret
 * @return \Abraham\TwitterOAuth\TwitterOAuth
 */
function getTwitterConnection(string $user_token, string $user_token_secret) {
    if (empty($user_token)) {
        $user_token = TwitterAccessToken;
    }
    if (empty($user_token_secret)) {
        $user_token_secret = TwitterAccessTokenSecret;
    }
    
    return new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret, $user_token, $user_token_secret);
}