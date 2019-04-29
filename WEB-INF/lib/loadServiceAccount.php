<?php
require_once ("init.php");
use Abraham\TwitterOAuth\TwitterOAuth;

function loadAccountInfo(array $paringRecord) {
    $serviceName = $paringRecord['service_name'];
    $access_token = $paringRecord['access_token'];
    $access_token_secret = $paringRecord['access_token_secret'];
    
    switch ($serviceName) {
        case "twitter":
            //取得したアクセストークンでユーザ情報を取得
            $user_connection = new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret, $access_token, $access_token_secret);
            $twitterAccount = obj_to_array($user_connection->get('account/verify_credentials'));
            
            //各値をセッションに入れる
            setSessionParam('twitterLoginAccount', $twitterAccount);
            setSessionParam('twitterAccessToken', new AccessToken($access_token, $access_token_secret));
            break;
            
        case "pawoo":
            $connection = getMastodonConnection(PawooDomain, $access_token);
            $pawooAccount = $connection->executeGetAPI('api/v1/accounts/verify_credentials');
            
            //各値をセッションに入れる
            setSessionParam("pawooLoginAccount", $pawooAccount);
            setSessionParam("pawooAccessToken", new AccessToken($access_token, $access_token_secret));
            break;
    }
}