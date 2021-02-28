<?php
require_once ("init.php");

error_reporting(-1);

// exit();

$tokens = getTwitterTokens();

$api = 'statuses/lookup';
$max_id = '0';
$wait = (int)60*60*6;

$empty_tweets = array();

while(true){
    $mydb = new MyDB();
    $sql = "SELECT count(id) AS count FROM mutter WHERE updated<5;";
    $results = $mydb->select($sql);
    echo "==============================\r\n";
    echo "last: ".$results[0]['count']."\r\n";
    echo "==============================\r\n";
    $mydb->close();

    search:

    $updated_tweets = array();

    $mydb = new MyDB();
    $sql = "SELECT id, LENGTH(body) AS length FROM mutter WHERE updated<5 LIMIT 1000;";
    $results = $mydb->select($sql);
    $mydb->close();

    foreach ($results as $result) {
        if(is_numeric($result['length']) && $result['length']>0) {
            $updated_tweets[] = $result['id'];
            echo "updated_tweets: ".$result['id']."\r\n";
        } else {
            $empty_tweets[] = $result['id'];
            echo "empty_tweets: ".$result['id']."\r\n";
        }
    }

    $empty_tweets = array_unique($empty_tweets);

    $updated_tweets_num = count($updated_tweets);
    $empty_tweets_num = count($empty_tweets);
    
    if($empty_tweets_num==0 && $updated_tweets_num==0) {
        sleep(60);
        continue;
    }

    echo $sql."\r\nupdated_tweets: $updated_tweets_num\r\nempty_tweets: $empty_tweets_num\r\n";

    $mydb = new MyDB();
    foreach ($updated_tweets as $tweet_id) {
        $sql = "SELECT body FROM mutter WHERE id='$tweet_id' AND domain='twitter';";

        $body = $mydb->select($sql)[0]['body'];
        $tweet = json_decode($body);
        
        if($tweet==NULL) {
            $empty_tweets[] = $tweet_id;
            continue;
        }

        $id = $tweet->id_str;
        $user_id = $tweet->user->id_str;
        $created_at = date('Y-m-d H:i:s', strtotime($tweet->created_at));

        $tags = getTwitterHashTags($tweet);
        $urls = getTwitterURLs($tweet);
        $extendedMedia = getTwitterExtendedMedia($tweet);

        $urls = $mydb->escape(json_encode($urls));
        $extendedMedia = $mydb->escape(json_encode($extendedMedia));

        $sql = "UPDATE mutter ";
        $sql .= "SET urls='$urls', extendedMedia='$extendedMedia', body=NULL, updated=6 ";
        $sql .= "WHERE id='$id' AND domain='twitter';";

        $results = $mydb->query($sql);

        foreach ($tags as $tag) {
            $tag = $mydb->escape($tag);

            $sql = "INSERT INTO tags ";
            $sql .= "(mutter_id, domain, tag, created_at, user_id) ";
            $sql .= "VALUES ('$id', 'twitter', '$tag', '$created_at', '$user_id');";

            $results = $mydb->insert($sql);
        }
    }
    $mydb->close();

    if($empty_tweets_num<100 && $updated_tweets_num>0) {
        goto search;
    }

    $tweets = array();

    if(empty($results)) {
        $max_id = '0';
        continue;
    }

    access_api:

    $ids = array();
    $counter=(int)0;
    for($counter = (int)0; $counter<100; $counter++) {
        if(count($empty_tweets)<=0)
            break;

        $max_id = array_shift($empty_tweets);
        $ids[] = $max_id;
        $tweets[$max_id] = array();
        $tweets[$max_id]['id'] = $max_id;
        $tweets[$max_id]['tweet'] = '';
    }

    $params = [
        "id" => implode(',', $ids)
    ];

    echo "count: ids: ".count($ids)."\r\nempty_tweets: ".count($empty_tweets)."\r\n";

    retry:
    try {
        echo "ids: ".count($ids)."\r\n";
        $sleepTime = rand(5,10);
        sleep($sleepTime);
        $results = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $params);

        if(!is_array($results)) {
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
            echo "==============================\r\n";
            var_dump($results);
            echo "\r\n==============================\r\n";
            sleep($wait);
            goto retry;
        }

        echo "results: ".count($results) . "\r\n";

        foreach ($results as $tweet) {
            $id = (string) $tweet->id;
            $tweets[$id]['tweet'] = $tweet;
        }

        $delete_counter = (int)0;

        foreach ($tweets as $tmp) {
            $tweet = $tmp['tweet'];
            if (empty($tweet) || isset($tweet->retweeted_status)) {
                $mydb = new MyDB();
                $sql = "DELETE FROM mutter WHERE id='".$tmp['id']."' AND domain='twitter';\r\n";
                $mydb->query($sql);
                $sql = "DELETE FROM tags WHERE mutter_id='".$tmp['id']."' AND domain='twitter';\r\n";
                $mydb->query($sql);
                $mydb->close();
                $delete_counter++;
            } else {
                $id = $tweet->id_str;
                $user_id = $tweet->user->id_str;
                $created_at = date('Y-m-d H:i:s', strtotime($tweet->created_at));
                $fav = $tweet->favorite_count;
                $rt = $tweet->retweet_count;
                $isreply = ($tweet->in_reply_to_status_id != NULL && $tweet->in_reply_to_status_id) ? 1 : 0;
                $possibly_sensitive = (isset($tweet->possibly_sensitive) && ($tweet->possibly_sensitive != NULL) && ($tweet->possibly_sensitive)) ? 1 : 0;

                if (hasMedia($tweet)) {
                    $tags = getTwitterHashTags($tweet);
                    $urls = getTwitterURLs($tweet);
                    $extendedMedia = getTwitterExtendedMedia($tweet);

                    $mydb = new MyDB();
                    $urls = $mydb->escape(json_encode($urls));
                    $extendedMedia = $mydb->escape(json_encode($extendedMedia));

                    $sql = "UPDATE mutter ";
                    $sql .= "SET fav = $fav, rt = $rt, possibly_sensitive=$possibly_sensitive, is_reply=$isreply, ";
                    $sql .= "urls='$urls', extendedMedia='$extendedMedia', body=NULL, updated=6 ";
                    $sql .= "WHERE id='$id' AND domain='twitter';";
                    $results = $mydb->query($sql);

                    foreach ($tags as $tag) {
                        $tag = $mydb->escape($tag);

                        $sql = "INSERT INTO tags ";
                        $sql .= "(mutter_id, domain, tag, created_at, user_id) ";
                        $sql .= "VALUES ('$id', 'twitter', '$tag', '$created_at', '$user_id');";

                        $results = $mydb->insert($sql);
                    }

                    $mydb->close();
                } else {
                    $mydb = new MyDB();
                    $sql = "DELETE FROM mutter WHERE id='$tweet->id_str' AND domain='twitter';\r\n";
                    $mydb->query($sql);
                    $sql = "DELETE FROM tags WHERE mutter_id='$tweet->id_str' AND domain='twitter';\r\n";
                    $mydb->query($sql);
                    $mydb->close();
                    $delete_counter++;
                }
            }
        }

    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();

        if (strpos($exceptionMessage, "Connection timed out after")===false) {
            echo "==============================\r\n";
            var_dump($e);
            // echo "==============================\r\n";
            // var_dump($tweets);
            echo "\r\n==============================\r\n";
            sleep($wait);
        }
        sleep(60);
        goto retry;
    } finally {
    }
    echo "delete: $delete_counter\r\n";

    $empty_tweets_num = count($empty_tweets);
    if($empty_tweets_num>=100)
        goto access_api;
};


function hasMedia(object $tweet) {
    if (isset($tweet->entities)) {
        $entities = $tweet->entities;
        if (isset($entities->media) && isset($entities->media[0]) && isset($entities->media[0]->media_url_https)) {
            return true;
        } else if (isset($entities->urls) && !empty($entities->urls)) {
            //             echo $tweet->id_str."\r\n";
            return true;
        }
    }

    return false;
}