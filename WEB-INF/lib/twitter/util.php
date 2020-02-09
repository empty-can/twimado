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
function getTwitterConnection(string $user_token="", string $user_token_secret="") {

    if (!empty($user_token) && !empty($user_token_secret)) {
        return new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret, $user_token, $user_token_secret);
    } else {
    
		$applicationBearer = getSessionParam('applicationBearer', '');

		if (empty($applicationBearer)) {
			$tw = new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret);
			$applicationBearer = $tw->oauth2("oauth2/token",array("grant_type"=>"client_credentials"));
			setSessionParam('applicationBearer', $applicationBearer);
		}
		
        return new TwitterOAuth(TwitterAppToken, TwitterAppTokenSecret, null, $applicationBearer->access_token);
    }
}

/**
 * ツイートをコレクションに登録する関数
 *
 * @param string $mutter_id
 * @param array $collection_ids
 */
function addTwitterCollection(string $mutter_id="", array $collection_ids=array()) {
    $collection_id = $collection_ids['collection_id'];
    $re_collection_id = $collection_ids['re_collection_id'];

    $api = 'collections/entries/add'; // アクセスするAPI

    // アクセストークンの取得
    $tokens = getTwitterTokens();

    $params = [
        "id" => "custom-$collection_id",
        "tweet_id" => $mutter_id
    ];

    // APIアクセス
    getTwitterConnection($tokens->token, $tokens->secret)
    ->post($api, $params);


    $params = [
        "id" => "custom-$re_collection_id",
        "tweet_id" => $mutter_id
    ];

    // APIアクセス
    getTwitterConnection($tokens->token, $tokens->secret)
    ->post($api, $params);
}

/**
 * ツイートをコレクションから削除する関数
 *
 * @param string $mutter_id
 * @param array $collection_ids
 */
function removeTwitterCollection(string $mutter_id="", array $collection_ids=array()) {
    $collection_id = $collection_ids['collection_id'];
    $re_collection_id = $collection_ids['re_collection_id'];

    $api = 'collections/entries/remove'; // アクセスするAPI

    // アクセストークンの取得
    $tokens = getTwitterTokens();

    $params = [
        "id" => "custom-$collection_id",
        "tweet_id" => $mutter_id
    ];

    // APIアクセス
    getTwitterConnection($tokens->token, $tokens->secret)
    ->post($api, $params);


    $params = [
        "id" => "custom-$re_collection_id",
        "tweet_id" => $mutter_id
    ];

    // APIアクセス
    getTwitterConnection($tokens->token, $tokens->secret)
    ->post($api, $params);
}

/**
 * 指定したユーザのフォローしているアカウントIDを取得
 *
 * @param string $user_id
 * @return array
 */
function getFollowee(string $user_id) {
    $api = "friends/ids";

    // アクセストークンの取得
    $tokens = getTwitterTokens();

    $params = [
        "user_id" => $user_id
        , "count" => 5000
    ];

    // APIアクセス
    return getTwitterConnection($tokens->token, $tokens->secret)
    ->get($api, $params)->ids;
}

/**
 * 指定したユーザのフォローしているアカウントIDを取得
 *
 * @param string $user_id
 * @return array
 */
function getFolloweeAll(string $user_id) {
    $api = "friends/list";

    // アクセストークンの取得
    $tokens = getTwitterTokens();

    $params = [
        "user_id" => $user_id
    ];

    // APIアクセス
    return getTwitterConnection($tokens->token, $tokens->secret)
    ->get($api, $params);
}