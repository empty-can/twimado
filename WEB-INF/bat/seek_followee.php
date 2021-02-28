<?php
require_once ("init.php");

error_reporting(-1);

// exit();

// アクセストークンの取得
$tokens = getTwitterTokens();

while (true) {
    seek_followee('', $tokens);
    seek_followee('1118913857651499008', $tokens);
    seek_followee('2656042465', $tokens);
    seek_followee('1118913857651499008', $tokens);
}

function seek_followee($target_id, $tokens) {
    $api = 'statuses/user_timeline';  // アクセスするAPI

    $followees = getFollowee($target_id);
    $idNum = count($followees);
    $idCount = (int)0;
    echo "user count:$idNum\r\n";

    $wait = (int)60*60*6;

    foreach ($followees as $user) {
    	$user_id = $user['user_id'];
		$idCount++;

		if($user_id=="58166411")
			continue;

        checkAndCreateCreator($user_id);

        $latest_tweet_time = getLatestMutter($user_id, 'twitter');

        $new_creator = ($latest_tweet_time==0) ? true : false;

        $max_id = (int) 0;
        $before_max_id = "";
        $params = [
            "user_id" => $user_id,
            "count" => 200,
            "include_rts" => false
        ];

        $created_at = 0;
        // $limit = time() - (24 * 60 * 60);
        while(true) {
            $sleepTime = rand(5,10);
            sleep($sleepTime);

            echo "start seek user: $user_id\r\n";

            // APIアクセス
            retry:
            try {
                $tweets = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $params);
            } catch (Exception $e) {
                echo "==============================\r\n";
                var_dump($e);
                echo "\r\n==============================\r\n";
                sleep(10);
                goto retry;
            }

            if(empty($tweets)) {
                echo "----------\r\n";
                echo "empty\r\n";
                echo "----------\r\n";
                sleep(10);
                break;
            }

            if (isset($tweets->errors)) {
                echo "----------\r\n";
                var_dump($tweets);
                echo "----------\r\n";
                var_dump($tweets->errors);
                echo "==========\r\n";
                
                if (isset($tweets->errors[0]->code) && ($tweets->errors[0]->code==34)) {
                    break;
                } else {
                    sleep($wait);
                    goto retry;
                }
            }

            if (isset($tweets->error)) {
                if ($tweets->error=="Not authorized.") {
                    echo "----------\r\n";
                    echo "user: $user_id is private account.\r\n";
                    echo "----------\r\n";
                    break;
                } else {
                    echo "----------\r\n";
                    var_dump($tweets);
                    echo "----------\r\n";
                    var_dump($tweets->error);
                    echo "==========\r\n";
                    sleep($wait);
                    goto retry;
                }
            }

            try {
                usort($tweets, "sort_tweets");
            } catch (Exception $e) {
                echo "==============================\r\n";
                var_dump($e);
                echo "==============================\r\n";
                var_dump($tweets);
                echo "\r\n==============================\r\n";
                sleep($wait);
                goto retry;
            }

            $before_max_id = $max_id;

            $counter = (int)0;
            $oldest_tweet_time=strtotime($tweets[0]->created_at);
            $newest_tweet_time=strtotime($tweets[count($tweets)-1]->created_at);

            foreach ($tweets as $tweet) {
                $min_id = $tweet->id_str;

                if ($max_id < $min_id) {
                    $max_id = $min_id;
                }

                $user_id = $tweet->user->id_str;
                $created_at = strtotime($tweet->created_at);
                $fav = $tweet->favorite_count;
                $rt = $tweet->retweet_count;
                $isreply = ($tweet->in_reply_to_status_id != NULL && $tweet->in_reply_to_status_id) ? 1 : 0;
                $possibly_sensitive = (isset($tweet->possibly_sensitive) && ($tweet->possibly_sensitive != NULL) && ($tweet->possibly_sensitive)) ? 1 : 0;

                if (hasMedia($tweet)) {
                    $tags = getTwitterHashTags($tweet);
                    $urls = getTwitterURLs($tweet);
                    $extendedMedia = getTwitterExtendedMedia($tweet);
                    // var_dump($tags);
                    // var_dump($urls);
                    // var_dump($extendedMedia);
                    addTweet($min_id, 'twitter', $user_id, $created_at, $fav, $rt, $isreply, $possibly_sensitive, $tags, $urls, $extendedMedia);
                    $counter ++;
                }

                $oldest_tweet = $created_at;
            }

            // echo "followee:$user->name@$user->screen_name ($idCount/$idNum) (".$counter."/".count($tweets_array).")\r\n";

            echo $user['screen_name'].':('.$idCount.'/'.$idNum.')('.$counter.'/'.count($tweets).")\tnewest_tweet_time:".date('Y/m/d（D）H:i:s', $newest_tweet_time)."\toldest_tweet_time:".date('Y/m/d（D）H:i:s', $oldest_tweet_time)."\r\n";
            $datetimeNow = new DateTime(date('Y-m-d H:i:s', time()));
            $datetimeLastTweet = new DateTime(date('Y-m-d H:i:s', $created_at));
            echo '最後のツイートからの経過日：'.$datetimeNow->diff($datetimeLastTweet)->format("%mヵ月%d日 %H時%I分%S秒")."\r\n";

            $params['max_id'] = $max_id;

            if (!($created_at>$latest_tweet_time && $before_max_id < $max_id)) {
                echo "追いつきました。\r\n";
                echo "====================================\r\n";
                break;
            } else if ($before_max_id <= $max_id) {
                echo "これ以上遡れません。\r\n";
                echo "====================================\r\n";
                break;
            }
        }

        // echo "\r\n";
        // exit();
    }

    echo "--------------------------------------------------------------\r\n";

}

/**
 *
 * @param string $target_user_id
 * @param array $tweet
 * @return boolean
 */
function hasMedia(object $tweet) {
    if (isset($tweet->entities)) {
        $entities = $tweet->entities;
        if (isset($entities->media) && isset($entities->media[0]) && isset($entities->media[0]->media_url_https)) {
            return true;
        } else if (isset($entities->urls) && ! empty($entities->urls)) {
            return true;
        }
    }

    return false;
}

/**
 *
 * @param array $a
 * @param array $b
 * @return number
 */
function sort_tweets(object $a, object $b) {
    if ($a->id == $b->id) {
        return 0;
    }
    return ($a->id < $b->id) ? + 1 : - 1;
}

