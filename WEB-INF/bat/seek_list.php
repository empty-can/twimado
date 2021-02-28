<?php
require_once ("init.php");

error_reporting(- 1);

// exit();

// アクセストークンの取得
$tokens = getTwitterTokens();
$max_id=0;
$before_max_id=0;
while (true) {
    $max_id = seek_list('1347033014086209536', $tokens, $max_id);

    if($before_max_id==$max_id) {
        $max_id=0;
    }
    $before_max_id=$max_id;

    $wait = (int) rand(10, 20);
    sleep($wait);
}

function seek_list($list_id, $tokens, int $before_max_id)
{
    $api = 'lists/statuses';
    $wait = (int) 930;
    $counter = (int) 0;
    $min_id = (int) 0;
    $before_min_id = (int) 0;
    $max_id = $before_max_id;

    while(true) {
        $params = [
            "list_id" => $list_id,
            "count" => 200,
            "include_rts" => false
        ];

        // APIアクセス
        retry:
        try {
            $tweets = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $params);
        } catch (Exception $e) {
            sleep($wait);
            goto retry;
        }

        if(empty($tweets)) {
            echo "----------\r\n";
            echo "empty\r\n";
            echo "----------\r\n";
        }

        if (isset($tweets->errors)) {
            if(isset($tweets->errors)) {
                if($tweets->errors[0]->code==130) {
                    echo "----------\r\n";
                    echo "Over capacity\r\n";
                    echo "----------\r\n";
                    sleep(600);
                    goto retry;
                } else {
                    echo "----------\r\n";
                    var_dump($tweets);
                    echo "----------\r\n";
                    var_dump($tweets->errors);
                    echo "==========\r\n";
                    sleep($wait);
                    goto retry;
                }
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

        foreach ($tweets as $tweet) {
            
            if(isset($tweet->retweeted_status)) {
                continue;
            }
            
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
        }

        echo "--------------------------------------------------------------\r\n";
        echo "count:".count($tweets)."（".$min_id."～".$max_id."）\r\n";
        echo "合計:$counter\r\n";

        if($before_min_id==$min_id) {
            break;
        }

        $params['max_id'] = $min_id;
        $before_min_id=$min_id;

        sleep(rand(5, 10));
    }

    echo "==============================================================\r\n";

    return $max_id;
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
