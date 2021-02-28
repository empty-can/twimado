<?php
require_once ("init.php");

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 *
 * @param object $tweet
 * @return object|NULL
 */
function getTwitterURLs(object $tweet) {

    if(isset($tweet->entities) && isset($tweet->entities->urls)) {
        return $tweet->entities->urls;
    }

    return array();
}
/**
 *
 * @param object $tweet
 * @return object|NULL
 */
function getTwitterMedia(object $tweet) {

    if(isset($tweet->entities) && isset($tweet->entities->media)) {
        return $tweet->entities->media;
    }

    return array();
}

/**
 *
 * @param object $tweet
 * @return object|NULL
 */
function getTwitterExtendedMedia(object $tweet) {

    if(isset($tweet->extended_entities) && isset($tweet->extended_entities->media)) {
        return $tweet->extended_entities->media;
    }

    return array();
}

/**
 *
 * @param object $tweet
 * @return array
 */
function getTwitterHashTags(object $tweet) {
    $tags = array();

    if(isset($tweet->entities) && isset($tweet->entities->hashtags)) {
        foreach ($tweet->entities->hashtags as $hashtag) {
            $tags[] = $hashtag->text;
        }
    }

    return $tags;
}

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

    $api = "friends/list";
    $tokens = getTwitterTokens();

    // アクセストークンの取得
    $result=array();
    $next_cursor = 0;
    $counter=(int)0;
    $creator_ids=array();

    if(empty($user_id)) {
        $counter=(int)0;
        $mydb = new MyDB();

        $sql = "SELECT id, screen_name, name FROM creator;";
        $creators = $mydb->select($sql);

        foreach ($creators as $creator) {
        	$creator_ids[]=$creator['id'];
            $tmp = array();
            $tmp['user_id']=$creator['id'];
            $tmp['screen_name']=$creator['screen_name'];
            $tmp['name']=$creator['name'];
            $result[] = $tmp;

            if(count($creator_ids)>=100) {

                $params = array();
				$params["user_id"] = implode(',', $creator_ids);
				$friends = getTwitterConnection($tokens->token, $tokens->secret)->get("users/lookup", $params);

				foreach ($friends as $user) {
				    insertOrUpdateCreator($user);
				}

				$creator_ids=array();
				echo "updating users...\r\n";
            }
        }
        $mydb->close();

        return $result;
    }

    // APIアクセス
    do{
        if($counter>=15)
          sleep(1000);

        $counter++;

        $params = [
            "user_id" => $user_id
            , "count" => 200
            , "skip_status" => false
        ];

        if($next_cursor>0)
            $params["cursor"] = $next_cursor;

        $friends = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $params);

        if(isset($friends->users)) {
            foreach ($friends->users as $user) {
            	insertOrUpdateCreator($user);

                $tmp = array();
                $tmp['user_id']=$user->id_str;
                $tmp['screen_name']=$user->screen_name;
                $tmp['name']=$user->name;
                $result[] = $tmp;
            }
        }

        $next_cursor = (isset($friends->next_cursor)) ? $friends->next_cursor : -1;

    }while($next_cursor>0);

    return $result;
}

function insertOrUpdateCreator($user) {
	$mydb = new MyDB();
	$results = $mydb->select("SELECT id FROM creator WHERE id = $user->id_str;");
	$mydb->close();

    if(empty($results)){
        insertCreator($user->id_str, 'twitter', $user->screen_name, mb_convert_encoding($user->name, "HTML-ENTITIES", "UTF-8"), $user->profile_image_url_https, $user->followers_count);
    } else {
        updateCreator($user->id_str, 'twitter', $user->screen_name, mb_convert_encoding($user->name, "HTML-ENTITIES", "UTF-8"), $user->profile_image_url_https, $user->followers_count);
    }
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