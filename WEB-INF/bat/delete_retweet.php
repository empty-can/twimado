<?php
require_once ("init.php");

error_reporting(-1);

// exit();

$tokens = getTwitterTokens();

$api = 'statuses/lookup';
$max_id = '0';
$wait = (int)60*60*6;

$target_tweets = array();

while(true){
    $mydb = new MyDB();
    $sql = "SELECT count(id) AS count FROM mutter WHERE updated<6;";
    $results = $mydb->select($sql);
    echo "==============================\r\n";
    echo "last: ".$results[0]['count']."\r\n";
    echo "==============================\r\n";
    $mydb->close();

    search:

    $mydb = new MyDB();
    $sql = "SELECT id FROM mutter WHERE updated<6 ORDER BY `created_at` DESC LIMIT 1000;";
    $results = $mydb->select($sql);
    $mydb->close();

    if(empty($results)) {
        $max_id = '0';
        continue;
    }

    foreach ($results as $result) {
        $target_tweets[] = $result['id'];
    }

    $target_tweets_num = count($target_tweets);

    echo $sql."\r\ntarget_tweets: $target_tweets_num\r\n";


    if($target_tweets_num<100) {
        goto search;
    }

    $tweets = array();

    access_api:

    $ids = array();
    $counter=(int)0;
    for($counter = (int)0; $counter<100; $counter++) {
        if(count($target_tweets)<=0)
            break;

        $max_id = array_shift($target_tweets);
        $ids[] = $max_id;
        $tweets[$max_id] = array();
        $tweets[$max_id]['id'] = $max_id;
        $tweets[$max_id]['tweet'] = '';
    }

    $params = [
        "id" => implode(',', $ids)
    ];

    echo "count: ids: ".count($ids)."\r\ntarget_tweets: ".count($target_tweets)."\r\n";

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

        $delete_counter = (int)0;
        $update_counter = (int)0;

        foreach ($results as $tweet) {
            $id = (string) $tweet->id;
            $tweets[$id]['tweet'] = $tweet;
        }

        foreach ($tweets as $tmp) {
            $tweet = $tmp['tweet'];
            if (empty($tweet) || isset($tweet->retweeted_status)) {
                $mydb = new MyDB();
                $sql = "DELETE FROM mutter WHERE id='".$tmp['id']."' AND domain='twitter';\r\n";
                $mydb->query($sql);;
                $sql = "DELETE FROM tags WHERE mutter_id='".$tmp['id']."' AND domain='twitter';\r\n";
                $mydb->close();
                $delete_counter++;
            } else {
                $id = $tweet->id_str;
                $mydb = new MyDB();
                $sql = "UPDATE mutter SET updated=6 WHERE id='$id' AND domain='twitter';";
                $results = $mydb->query($sql);
                $mydb->close();
                $update_counter++;
            }
        }
        
        echo "updated: $update_counter deleted: $delete_counter (".($delete_counter+$update_counter).")\r\n";

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

    $target_tweets_num = count($target_tweets);
    if($target_tweets_num>=100)
        goto access_api;
}