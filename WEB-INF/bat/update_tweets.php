<?php
require_once ("init.php");

error_reporting(-1);

// exit();

$tokens = getTwitterTokens();

$api = 'statuses/lookup';
$max_id = '0';
$wait = (int)60*60*6;

while(true){
    $mydb = new MyDB();
    $sql = "SELECT count(id) AS count, count(id)*100/(SELECT count(id) FROM creator) AS percent FROM `new_creator` WHERE crawled=1;";
    $result = $mydb->select($sql);
    echo "======================================================\r\n";
    echo $result[0]['count'].", ".$result[0]['percent']."%\r\n";
    echo "======================================================\r\n";
    
    $sql = "SELECT DISTINCT id, updated FROM (";
    $sql .= "(SELECT id, updated ,created_at FROM mutter WHERE created_at='0000-00-00 00:00:00' ORDER BY `created_at`)";
    $sql .= " UNION";
    if($max_id==0)
        $sql .= " (SELECT id, updated, created_at FROM mutter WHERE created_at>'".date('Y-m-d H:i:s',strtotime('-14 day'))."' AND media<>0)";
    else
        $sql .= " (SELECT id, updated, created_at FROM mutter WHERE id<'$max_id' AND created_at>'".date('Y-m-d H:i:s',strtotime('-14 day'))."' AND media<>0)";
    $sql .= " ) AS a";
    
    $last = $mydb->select("SELECT count(id) AS count FROM ($sql) AS b;");
    
    $sql .= " ORDER BY created_at DESC LIMIT 100;";
    
    $results = $mydb->select("$sql;");
    echo $sql."\r\nlast: ".$last[0]['count']."\r\n";

    $mydb->close();

    $tweets = array();

    if(empty($results)) {
        $max_id = '0';
        continue;
    }

    $ids = array();
    $counter=(int)0;
    $delete_counter=(int)0;
    foreach ($results as $result) {
        $id = $result['id'];
        
        if($max_id>$id)
            $max_id=$id;
        else if($max_id==0)
            $max_id=$id;
        
        $ids[] = $id;
        $tweets[$id] = array();
        $tweets[$id]['id'] = $id;
        $tweets[$id]['tweet'] = '';
        $counter++;

        if($counter>=100)
          break;
    }

    $params = [
        "id" => implode(',', $ids)
    ];

    retry:
    try {
        echo count($ids) . "\r\n";
        $results = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $params);
        echo count($results) . "\r\n";

        foreach ($results as $tweet) {
            $id = (string) $tweet->id;
            $tweets[$id]['tweet'] = $tweet;
        }

        foreach ($tweets as $tmp) {
            $tweet = $tmp['tweet'];
            if (empty($tweet) || isset($tweet->retweeted_status)) {
                $mydb = new MyDB();
                $sql = "SELECT extendedMedia FROM mutter WHERE id='".$tmp['id']."' AND domain='twitter';\r\n";
                $delete_tweets = $mydb->select($sql);
                fputs(STDERR, $delete_tweets[0]["extendedMedia"]);
                if(isset($delete_tweets[1])) {
                    fputs(STDERR, $delete_tweets[1]["extendedMedia"]);
                }
                if(isset($delete_tweets[2])) {
                    fputs(STDERR, $delete_tweets[2]["extendedMedia"]);
                }
                if(isset($delete_tweets[3])) {
                    fputs(STDERR, $delete_tweets[3]["extendedMedia"]);
                }
                $sql = "DELETE FROM mutter WHERE id='".$tmp['id']."' AND domain='twitter';\r\n";
                $mydb->query($sql);
                $sql = "DELETE FROM tags WHERE mutter_id='".$tmp['id']."' AND domain='twitter';\r\n";
                $mydb->query($sql);
                $mydb->close();
                echo $tmp['id']."\r\n";
                $delete_counter++;
            } else if (is_array($tweet)) {
                echo "is array ==============================\r\n";
                var_dump($tweet);
                echo "==============================\r\n";
                sleep(3600);
                goto retry;
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
                    $sql .= "SET created_at='$created_at', fav = $fav, rt = $rt, possibly_sensitive=$possibly_sensitive, is_reply=$isreply, ";
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
                    $counter ++;
                } else {
                    $mydb = new MyDB();
                    $sql = "DELETE FROM mutter WHERE id='$tweet->id_str' AND domain='twitter';\r\n";
                    $mydb->query($sql);
                    $sql = "DELETE FROM tags WHERE mutter_id='$tweet->id_str' AND domain='twitter';\r\n";
                    $mydb->query($sql);
                    $mydb->close();
                }
            }
        }
        echo "deleted: $delete_counter\r\n";
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();

        if (strpos($exceptionMessage, "Connection timed out after")===false) {
            echo "==============================\r\n";
            var_dump($e);
            // echo "==============================\r\n";
            // var_dump($tweets);
            echo "\r\n==============================\r\n";
            sleep(600);
        } else if (strpos($exceptionMessage, "Connection timed out after")===false) {
            echo "==============================\r\n";
            var_dump($e);
            // echo "==============================\r\n";
            // var_dump($tweets);
            echo "\r\n==============================\r\n";
            sleep($wait);
        }
        sleep(600);
        goto retry;
    } finally {
        $sleepTime = rand(5,10);
        sleep($sleepTime);
    }
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