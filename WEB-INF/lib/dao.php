<?php


function isSameMedia(string $matome_id, string $user_id, string $hash) {
    $mydb = new MyDB();

    $sql  = "SELECT mutter.id, media.url, mutter.created_at AS created_at, BIT_COUNT(CAST(CONV(media.hash, 16, 10) AS UNSIGNED) ^ CAST(CONV('$hash', 16, 10) AS UNSIGNED)) AS distance";
    $sql .= " FROM media, matome, mvsm, mutter";
    $sql .= " WHERE matome.user_id='$user_id' AND matome.id=$matome_id";
    $sql .= " AND mvsm.mutter_id=mutter.id AND mvsm.mutter_domain=mutter.domain AND mvsm.matome_id=matome.id";
    $sql .= " AND media.mutter_id=mutter.id AND media.mutter_domain=mutter.domain";
    $sql .= " AND BIT_COUNT(CAST(CONV(media.hash, 16, 10) AS UNSIGNED) ^ CAST(CONV('$hash', 16, 10) AS UNSIGNED))<=5;";
    // echo $sql."<br>";

    $results = $mydb->select($sql);

    $flag = false;

    foreach ($results as $result) {
    	if($result['distance']==0)
    		$flag=true;

        echo "ID:".$result['id']."<br>\r\n";
        echo "Created At:".$result['created_at']."<br>\r\n";
        echo 'DIST:<b style="color:read;">'.$result['distance']."</b><br>\r\n";
        echo '<img style="display:block;width:100%;" src="'.$result['url'].'">'."\r\n";
    }
    $count = count($results);
    $mydb->close();


    return $flag;
}

/**
 *
 * @param string $key
 * @return $img_url
 */
function isMediaInfo(string $url, string $mutterId, string $mutterDomain) {
    $mydb = new MyDB();
	$sql = "SELECT count(url) AS count FROM media WHERE url ='$url' AND mutter_id=$mutterId AND mutter_domain='$mutterDomain';";
	// myVarDump($sql);
    $results = $mydb->select($sql);
	// myVarDump($results[0]['count']);

    $mydb->close();

    return $results[0]['count'];
}

/**
 *
 * @param string $key
 * @return $img_url
 */
function insertMediaInfo(string $url, string $mutterId, string $mutterDomain, string $hash) {
    $mydb = new MyDB();
	$sql = "INSERT INTO media VALUES ('$url', $mutterId, '$mutterDomain', '$hash');";

    $results = $mydb->insert($sql);

    $mydb->close();

    return $results;
}

/**
 *
 * @param string $key
 * @return $img_url
 */
function getPageImages(string $key) {
    $mydb = new MyDB();
    $key = $mydb->escape($key);
    $img_url = "";

    $results = $mydb->select("SELECT image_file_name FROM top_images WHERE image_key = '$key';");

    if (!empty($results) && isset($results[0]) && !empty($results[0]['image_file_name'])) {
        $img_url = $results[0]['image_file_name'];
    }

    return $img_url;
}

function isPageImages(string $key) {
    $mydb = new MyDB();
    $key = $mydb->escape($key);

    $results = $mydb->select("SELECT count(image_key) FROM top_images WHERE image_key = '$key';");
    $mydb->close();
    return $results[0]["count(image_key)"];
}

/**
 *
 * @param string $key
 * @param string $image_file_name
 */
function setPageImages(string $key, string $image_url) {
    $mydb = new MyDB();
    $key = $mydb->escape($key);
    $image_url = $mydb->escape($image_url);
    $twimg_url = 'https://pbs.twimg.com/media/';
    $media_base = MediaDir;
    $sql = "";
    $image_file_name = "";

    if (! empty($image_url)) {
        $image = file_get_contents($image_url);
        $image_file_name = explode($twimg_url, $image_url)[1];
    } else {
        return;
    }

    $count = isPageImages($key);

    if ($count == 0) {
        file_put_contents($media_base . '/' . $image_file_name, $image);
        $sql = "INSERT INTO top_images (image_key, image_file_name) VALUES ('$key', '$image_file_name');";
        $mydb->query($sql);
    } else {
        $old_image_file_name = getPageImages($key);

        if ($old_image_file_name !== $image_file_name)
            unlink($media_base . '/' . $old_image_file_name);
        $sql = "UPDATE top_images SET image_file_name = '$image_file_name' WHERE image_key = '$key';";
        $mydb->query($sql);

        file_put_contents($media_base . '/' . $image_file_name, $image);
    }

    $mydb->close();
}

function checkAndCreateCreator(string $user_id) {
    $mydb = new MyDB();

//     $results = $mydb->select("SELECT id FROM creator WHERE id = $user_id;");
    $results = $mydb->select("SELECT id FROM new_creator WHERE id = $user_id;");
    $mydb->close();

    if(empty($results)){
        $tokens = getTwitterTokens();

        $api = "users/show";

        retry_get_user_account:

        // APIアクセス
        $account = getTwitterConnection($tokens->token, $tokens->secret)
        ->get($api, ['user_id' => $user_id]);

        $date = date('Y-m-d H:i:s');

        if (isset($account->errors)) {
            foreach($account->errors as $error) {
                if($error->code == 50) {
                    return;
                }

                echo "===============\r\n";
                echo var_dump($error);
                echo "\r\n===============\r\n";
                sleep(930);
                goto retry_get_user_account;
            }
        }

        echo "new followee!\r\n$account->name@$account->screen_name\r\n";

        insertCreator($user_id, 'twitter', $account->screen_name, $account->name, $account->profile_image_url_https, $account->followers_count);

        $mydb = new MyDB();
        $sql = "INSERT INTO new_creator (id, domain, screen_name, name, follow_date, crawled) VALUES ('$user_id', 'twitter', '$account->screen_name', '$account->name', '$date', 0);";
        $results = $mydb->insert($sql);
        $mydb->close();
    }
}

/**
 *
 * @param string $creator_id
 * @param string $domain
 */
function getLatestMutter(string $user_id, string $domain) {
    $result = "";

    $mydb = new MyDB();
    $user_id = $mydb->escape($user_id);
    $domain = $mydb->escape($domain);

    $sql = "SELECT created_at AS created_at FROM mutter"
        ." WHERE mutter.user_id = '$user_id' AND mutter.domain = '".$domain."'"
        ." ORDER BY mutter.created_at DESC LIMIT 1;";
// echo "$sql\r\n";
$results = $mydb->select($sql);
// echo "$sql\r\n";

    $mydb->close();

    if (isset($results[0]) && $results[0]['created_at'] && ! empty($results[0]['created_at']))
        $result = strtotime($results[0]['created_at']);
    else
        $result = 0;

    return $result;
}

/**
 *
 * @param string $user_id
 * @param string $domain
 * @param string $screen_name
 * @param string $name
 * @param string $profile_image_url_https
 * @param int $followers_count
 */
function insertCreator(string $user_id, string $domain, string $screen_name, string $name, string $profile_image_url_https, int $followers_count) {
    $results = "";

    $mydb = new MyDB();
    $user_id = $mydb->escape($user_id);
    $domain = $mydb->escape($domain);
    $screen_name = $mydb->escape($screen_name);
    $name = $mydb->escape($name);

    $sql = "INSERT INTO creator (`id`, `domain`, `screen_name`, `name`, `profile_image`, `followers_count`) "
        ."VALUES ($user_id, '$domain', '$screen_name', '$name', '$profile_image_url_https', $followers_count);";

        $results = $mydb->insert($sql);

        $mydb->close();

        return $results[0]['count(id)'];
}

/**
 *
 * @param string $user_id
 * @param string $domain
 * @param string $screen_name
 * @param string $name
 * @param string $profile_image_url_https
 * @param int $followers_count
 */
function updateCreator(string $user_id, string $domain, string $screen_name, string $name, string $profile_image_url_https, int $followers_count) {
    $results = "";

    $mydb = new MyDB();
    $user_id = $mydb->escape($user_id);
    $domain = $mydb->escape($domain);
    $screen_name = $mydb->escape($screen_name);
    $name = $mydb->escape($name);

    $sql = "UPDATE creator SET ";
    $sql .= "screen_name='$screen_name', ";
    $sql .= "name='$name', ";
    $sql .= "profile_image='$profile_image_url_https', ";
    $sql .= "followers_count=$followers_count ";
    $sql .= "WHERE id='$user_id' AND domain='$domain';";
    $results = $mydb->insert($sql);

    $mydb->close();

    return $results[0];
}

function existCreator(string $user_id, string $domain) {
    $results = "";

    $mydb = new MyDB();
    $user_id = $mydb->escape($user_id);
    $domain = $mydb->escape($domain);

    $sql = "SELECT count(id) FROM creator WHERE id=$user_id AND domain='$domain';";

    $results = $mydb->select($sql);

    $mydb->close();

    return $results[0]['count(id)'];

}

function updateMutter(string $id, string $domain, int $fav, int $rt) {
    $mydb = new MyDB();
    $sql = "UPDATE mutter SET fav = $fav, rt = $rt WHERE id='$id' AND domain='$domain';";
    $results = $mydb->query($sql);
    $mydb->close();

    return $results;
}


/**
 *
 * @param string $id
 * @param string $domain
 * @param string $user_id
 * @param int $time
 * @param int $fav
 * @param int $rt
 * @param int $is_reply
 * @param int $possibly_sensitive
 * @param array $tags
 * @param object $urls
 * @param object $extendedMedia
 */
function addTweet(string $id, string $domain, string $user_id
    , int $time, int $fav, int $rt, int $is_reply = 0
    , int $possibly_sensitive = 0
    , array $tags = array(), array $urls = array(), array $extendedMedia = array())
{
    $mydb = new MyDB();
    $urls = $mydb->escape(json_encode($urls));
    $extendedMedia = $mydb->escape(json_encode($extendedMedia));

    $created_at = date('Y-m-d H:i:s', $time);

    $results = $mydb->select("SELECT id FROM mutter WHERE id = '$id';");

    if (empty($results)) {
        $sql = "INSERT INTO mutter ";
        $sql .= "(id, domain, user_id, created_at, fav, rt, media, is_reply, possibly_sensitive, urls, extendedMedia, updated) ";
        $sql .= "VALUES ('$id', '$domain', '$user_id', '$created_at', $fav, $rt, 1, $is_reply, $possibly_sensitive, '$urls', '$extendedMedia', 6);";
//         echo "$sql\r\n";
        $results = $mydb->insert($sql);

        foreach ($tags as $tag) {
            $tag = $mydb->escape($tag);
            $sql = "INSERT INTO tags ";
            $sql .= "(mutter_id, domain, tag, created_at, user_id) ";
            $sql .= "VALUES ('$id', '$domain', '$tag', '$created_at', '$user_id');";

            $results = $mydb->insert($sql);
        }
    } else {
        $sql = "UPDATE mutter ";
        $sql .= "SET fav = $fav, rt = $rt , body=NULL, updated=6 ";
        $sql .= "WHERE id='$id' AND domain='$domain';";
//         echo "$sql\r\n";
        $results = $mydb->query($sql);

        foreach ($tags as $tag) {
            $tag = $mydb->escape($tag);

            $sql = "INSERT INTO tags ";
            $sql .= "(mutter_id, domain, tag, created_at, user_id) ";
            $sql .= "VALUES ('$id', '$domain', '$tag', '$created_at', '$user_id');";

            $results = $mydb->insert($sql);
        }
    }

    $mydb->close();
}

/**
 *
 * @param string $id
 * @param string $domain
 * @param string $user_id
 * @param int $time
 * @param int $fav
 * @param int $rt
 */
function addMatomeTimeline(string $id, string $domain, string $user_id, int $time, int $fav, int $rt, int $media, int $is_reply=0, int $possibly_sensitive=0, object $body=NULL) {
    $mydb = new MyDB();

    if($body!=NULL) {
        $body = "'".$mydb->escape(json_encode($body))."'";
    }

	/**
    $results = $mydb->select("SELECT id FROM creator WHERE id = '$user_id';");

    if(empty($results)){
        $tokens = getTwitterTokens();

        $api = "users/show";

        // APIアクセス
        $account = getTwitterConnection($tokens->token, $tokens->secret)
        ->get($api, ['user_id' => $user_id]);

        //         myVarDump($account);

        $user_id = $account->id_str;
        $sql = "INSERT INTO creator (id, domain, screen_name, name) VALUES ('$user_id', 'twitter', '$account->screen_name', '$account->name');";
        $results = $mydb->insert($sql);
    }
    **/

    $results = $mydb->select("SELECT id FROM mutter WHERE id = '$id';");

    if(empty($results)){
        $created_at = date('Y-m-d H:i:s', $time);
        $sql = "INSERT INTO mutter (id, domain, user_id, created_at, fav, rt, media, is_reply, possibly_sensitive, body, updated) VALUES ('$id', '$domain', '$user_id', '$created_at', $fav, $rt, $media, $is_reply, $possibly_sensitive, $body, 2);";
        //myVarDump($sql);
        $results = $mydb->insert($sql);
        // echo $sql."\r\n";
        // var_dump($results);
    } else {
//         if($media==0) {
//             $sql = "DELETE FROM mutter WHERE id='$id';";
//         } else {
            $sql = "UPDATE mutter SET fav = $fav, rt = $rt , media = $media, is_reply=$is_reply, possibly_sensitive=$possibly_sensitive, updated=2, body=$body WHERE id='$id' AND domain='$domain';";
//         }

//        myVarDump($sql);
        $results = $mydb->query($sql);
//         echo $sql."\r\n";
        // var_dump($results);
    }


    $mydb->close();
}

function getNewTweet(int $limit=100) {
    $mydb = new MyDB();
    $sql = "SELECT id FROM mutter ORDER BY created_at DESC LIMIT $limit;";
    $results = $mydb->select($sql);

    $mydb->close();

    return $results;
}

function getFavRanking(string $fromday, string $today="") {
    $mydb = new MyDB();

    if(empty($today)) {
        $sql = "SELECT id FROM mutter WHERE created_at > '$fromday' ORDER BY fav DESC LIMIT 100;";
    } else {
        $sql = "SELECT id FROM mutter WHERE created_at BETWEEN '$fromday' AND '$today' ORDER BY fav DESC LIMIT 100;";
    }
    // myVarDump($sql);
    $results = $mydb->select($sql);

    $mydb->close();

    return $results;
}

function getAllCreators() {

    $mydb = new MyDB();

    $sql = "SELECT * FROM creator ORDER BY id;";

    $results = $mydb->select($sql);

    $mydb->close();

    return $results;

}

function getTwitterCollection(string $user_id="", string $matome_id="") {
    $results = "";

    $mydb = new MyDB();
    $matome_id = $mydb->escape($matome_id);

    $sql = "SELECT collection_id, re_collection_id FROM matome WHERE user_id=$user_id AND id=$matome_id;";

    $results = $mydb->select($sql);
    $mydb->close();

    return $results[0];
}

/**
 *
 * @param string $mutter_id
 * @param string $domain
 * @param string $user_id
 * @param string $matome_id
 * @return mysqli_result|boolean
 */
function regMatome(string $mutter_id="", string $domain="", string $user_id="", string $matome_id="") {
    $results = "";

    $mydb = new MyDB();
    $mutter_id = $mydb->escape($mutter_id);
    $domain = $mydb->escape($domain);
    $matome_id = $mydb->escape($matome_id);
    $user_id = $mydb->escape($user_id);

    $sql = "INSERT INTO mvsm (`mutter_id`, `mutter_domain`, `user_id`, user_domain, `matome_id`) VALUES ($mutter_id, '$domain', '$user_id', '$domain', $matome_id);";

    $results = $mydb->insert($sql);

    if(!empty($results)) {
        $sql = "SELECT mutter_id FROM mvsm WHERE user_id='$user_id' AND matome_id='$matome_id' ORDER BY mutter_id DESC LIMIT 1;";
        $results = $mydb->select($sql);
        $mutter_id = $results[0]['mutter_id'];

        $sql = "UPDATE matome SET `latest_mutter_id`='$mutter_id' WHERE user_id='$user_id' AND id='$matome_id';";
        $results = $mydb->query($sql);
    }

    $mydb->close();

    return $results;
}

/**
 *
 * @param string $mutter_id
 * @param string $domain
 * @param string $user_id
 * @param string $matome_id
 * @return mysqli_result|boolean
 */
function delMatome(string $mutter_id="", string $domain="", string $user_id="", string $matome_id="") {
    $results = "";

    $mydb = new MyDB();
    $mutter_id = $mydb->escape($mutter_id);
    $domain = $mydb->escape($domain);
    $matome_id = $mydb->escape($matome_id);

    $sql = "DELETE FROM mvsm WHERE `mutter_id`=$mutter_id AND `mutter_domain`='$domain' AND `user_id`=$user_id AND `user_domain`='$domain' AND `matome_id`=$matome_id;";
    // error_log("sql:$sql");

    $results = $mydb->query($sql);

    $sql = "SELECT mutter.id AS id FROM mvsm, mutter WHERE mvsm.mutter_id=mutter.id AND mvsm.mutter_domain=mutter.domain AND mvsm.user_id=$user_id AND mvsm.user_domain='$domain' AND mvsm.matome_id=$matome_id ORDER BY mutter.created_at DESC LIMIT 1";
    $results = $mydb->select($sql);

    $id = $results[0]['id'];
    // error_log("sql:$sql");
    // error_log("id:$id");

    $sql = "UPDATE matome SET latest_mutter_id=$id WHERE `user_id`=$user_id AND `user_domain`='$domain' AND `id`=$matome_id;";
    // error_log("sql:$sql");
    $mydb->query($sql);



    $mydb->close();

    return $results;
}

function createMatome(string $creator_id="", string $creator_domain="", string $matome_name="", string $matome_name_short="") {
    $results = "";

    if(!empty($matome_name) && !empty($matome_name_short)) {
        $mydb = new MyDB();

        $creator_id = $mydb->escape($creator_id);
        $creator_domain = $mydb->escape($creator_domain);
        $matome_name = $mydb->escape($matome_name);
        $matome_name_short = $mydb->escape($matome_name_short);

        $sql = "SELECT max(id) AS max FROM matome WHERE user_id='$creator_id'";

        // error_log("sql:$sql");

        $max = ((int)$mydb->select($sql)[0]['max'])+1;

        // error_log("max:$max");

        $matome_name = $mydb->escape($matome_name);

        $sql = "INSERT INTO matome (`id`, `title`, `title_short`, `description`, `user_id`, `user_domain`, `latest_mutter_id`)"
            ." VALUES ($max, '$matome_name', '$matome_name_short', '$matome_name', $creator_id,'$creator_domain', 0)";

        // error_log("sql:$sql");

        $results = $mydb->insert($sql);

        $mydb->close();
    }

    return $results;
}

function getMatomeInfo(string $matome_id="") {
    $results = "";

    $mydb = new MyDB();
    $matome_id = $mydb->escape($matome_id);

    $sql = "SELECT matome.id AS `id`, `title`, `title_short`, `affiliate`, `description`, `user_id`, `user_domain`, `private`, `latest_mutter_id`, count(*) AS total FROM matome WHERE CONCAT(matome.user_id, matome.id) = '$matome_id' GROUP BY id ORDER BY total DESC;";

    $results = $mydb->select($sql)[0];

    $mydb->close();

    return $results;
}

function getMatomeInfoByUserId(string $user_id="", string $user_domain="") {
    $results = "";

    if (!empty($user_id) && !empty($user_domain)) {
        $mydb = new MyDB();
        $user_id = $mydb->escape($user_id);
        $user_domain = $mydb->escape($user_domain);

        $sql = "SELECT temp_table.id AS id, `title`, `title_short`, `affiliate`, `description`, temp_table.user_id AS user_id, temp_table.user_domain AS user_domain, temp_table.matome_id AS matome_id, count(*) AS total"
                ." FROM"
                ." (SELECT CONCAT(matome.user_id, matome.id) AS id, `title`, `title_short`, `affiliate`, `description`, `user_id`, `user_domain`, matome.id AS `matome_id`"
                ."  FROM matome WHERE user_id = $user_id"
                ."  ) temp_table LEFT JOIN mvsm ON temp_table.id = CONCAT(mvsm.user_id, mvsm.matome_id) GROUP BY temp_table.id ORDER BY mvsm.matome_id;";

        $results = $mydb->select($sql);

        $mydb->close();
    }

    return $results;
}

function getMatomeIds(string $matome_id="", string $mutter_id="", int $asc=0, int $limit=100) {
    $results = array();

    if (!empty($matome_id)) {

        $mydb = new MyDB();
        $matome_id = $mydb->escape($matome_id);

        $sql = "SELECT * FROM matome, mvsm, mutter WHERE CONCAT(mvsm.user_id, mvsm.matome_id) = '$matome_id' AND CONCAT(matome.user_id, matome.id) = '$matome_id' AND mutter.id = mvsm.mutter_id AND mutter.domain = mvsm.mutter_domain";

        if(empty($mutter_id)) {
            if($asc==0) {
                $sql .= " ORDER BY created_at DESC";
            } else {
                $sql .= " ORDER BY created_at ASC";
            }
        } else {
            if($asc==0) {
                $sql .= " AND mutter.id < $mutter_id ORDER BY created_at DESC";
            } else {
                $sql .= " AND mutter.id > $mutter_id ORDER BY created_at ASC";
            }
        }

        $sql .= " LIMIT $limit";

        $rows = $mydb->select($sql);

        foreach ($rows as $row) {
            $results[] = $row["mutter_id"];
        }

        $mydb->close();
    }

    return implode(',', $results);
}

function getMatomeList(string $user_id="", string $domain="") {
    $results = "";

    $mydb = new MyDB();
    $user_id = $mydb->escape($user_id);
    $domain = $mydb->escape($domain);

    $sql = "SELECT matome.id AS matome_id, `title`, `description`, matome.user_id AS `user_id`, `user_domain`, total FROM matome, creator"
            .", (SELECT user_id, matome_id, count(matome_id) AS total FROM mvsm GROUP BY user_id, matome_id) matome_count";
    $sql .= "   WHERE matome.user_id = creator.id AND matome.user_id = matome_count.user_id AND matome.id = matome_count.matome_id";

    if(!empty($user_id)) {
        $sql .= "   AND matome.user_id = '$user_id' AND user_domain = '$domain'";
    }

    $sql .= ";";

    // error_log("sql:$sql");

    $results = $mydb->select($sql);

    $mydb->close();

    return (empty($results)) ? array() : $results;
}

/**
 *
 * @param string $account_id
 * @param string $max_id
 * @param int $limit
 * @param int $asc
 * @return string
 */
function getMutterIds(string $account_id="", string $mutter_id=null, int $limit=100, int $asc=1, bool $media_only=true) {
    $results = "";

    if(!empty($account_id)) {
        $mydb = new MyDB();
        $account_id = $mydb->escape($account_id);

        $sql = "SELECT id, created_at FROM mutter AS a WHERE a.user_id = '$account_id'";

        if($media_only) {
            $sql .= " AND media=1";
        }

        if(empty($mutter_id)) {
            if($asc==0) {
                $sql .= " ORDER BY a.created_at DESC";
            } else {
                $sql .= " ORDER BY a.created_at ASC";
            }
        } else {
            if($asc==0) {
                $sql .= " AND a.created_at <= (SELECT created_at FROM mutter AS b WHERE b.id = $mutter_id) ORDER BY created_at DESC";
            } else {
                $sql .= " AND a.created_at >= (SELECT created_at FROM mutter AS b WHERE b.id = $mutter_id) ORDER BY created_at ASC";
            }
        }

        $sql .= " LIMIT $limit;";

        // error_log("sql:$sql");

        $rows = $mydb->select($sql);

        $i=0;
        foreach ($rows as $row) {
        	// error_log("row:".implode($row,','));
            $results .= $row["id"].",";

            if($i++>=$limit)
                break;
        }

        $mydb->close();

        if(!empty($results)) {
            $results = substr($results, 0, -1);
        }
    }

    return $results;
}

/**
 * アプリ連携情報を削除
 *
 * @param string $account_id
 * @param string $service_name
 * @return boolean|mysqli_result
 */
function delete_passenger(string $account_id, string $service_name) {
    $results = false;

    if(!empty($account_id)) {
        $mydb = new MyDB();
        $account_id = $mydb->escape($account_id);
        $service_name = $mydb->escape($service_name);

        $sql = "DELETE FROM passenger WHERE id = '$account_id' AND service_name = '$service_name'";
        $results = $mydb->query($sql);
        $mydb->close();
    }

    return $results;
}

/**
 * アプリのアカウント情報を削除
 *
 * @param string $account_id
 * @return boolean|mysqli_result
 */
function delete_account(string $account_id) {
    $results = false;

    if(!empty($account_id)) {
        $mydb = new MyDB();
        $account_id = $mydb->escape($account_id);

        $sql = "DELETE FROM tamikusa WHERE id = '$account_id'";
        $results = $mydb->query($sql);
        $mydb->close();
    }

    return $results;
}

/**
 * サービスを登録する
 *
 * @param string $account_id
 * @param string $service_name
 * @param array $service_user_info
 * @return mysqli_result|boolean
 */
function register_pairing(string $account_id, string $service_name, array $service_user_info) {
    $results = false;

    if(!exist_pair($account_id, $service_name, $service_user_info['id'])) {
        $mydb = new MyDB();

        $account_id = $mydb->escape($account_id);
        $service_name = $mydb->escape($service_name);
        $service_account_id = $mydb->escape($service_user_info['id']);

        $service_user_name = $mydb->escape($service_user_info['user_name']);
        $service_display_name = $mydb->escape($service_user_info['display_name']);

        $encrypted_access_token = encrypt($service_user_info['token']);
        $access_token = $mydb->escape($encrypted_access_token['data']);
        $enc_key = $encrypted_access_token['key'];

        $access_token_secret =
            (isset($service_user_info['token_secret']))
            ? $mydb->escape(encrypt($service_user_info['token_secret'], $enc_key)['data'])
                : "";

        $sql = "INSERT INTO tamikusa_pairing"
            ." (tamikusa_id, service_name, service_account_id, service_user_name, service_display_name, access_token, access_token_secret, enc_key)"
                ." VALUES ('$account_id', '$service_name', '$service_account_id', '$service_user_name', '$service_display_name', '$access_token', '$access_token_secret', '$enc_key')";

        $results = $mydb->query($sql);
    }

    return $results;
}

/**
 * アカウント情報からサービス連携情報を取得する
 *
 * @param string $account_id
 * @return boolean
 */
function select_all_pairs(string $account_id) {
    return select_pairs($account_id);
}

/**
 * アカウント名とサービス名からサービス連携情報を取得する
 *
 * @param string $account_id
 * @param string $service_name
 */
function select_pairs(string $account_id, string $service_name = "") {
    $mydb = new MyDB();

    $account_id = $mydb->escape($account_id);
    $sql = "SELECT * FROM tamikusa_pairing WHERE tamikusa_id = '$account_id'";
    if(!empty($service_name)) {
        $sql .= " AND service_name = '$service_name'";
    }
    $tmp = $mydb->select($sql);

    $results = array();
    foreach ($tmp as $result) {
        $enc_key = $result['enc_key'];

        $result['access_token'] = decrypt($result['access_token'], $enc_key);
        $result['access_token_secret'] =
        (empty($result['access_token_secret'])) ? ""
            : decrypt($result['access_token_secret'], $enc_key);

            $results[] = $result;
    }

    return $results;
}
//

/**
 * アカウント名とサービス名からアクセストークン情報を取得
 *
 * @param string $account_id
 * @param string $service_name
 * @return AccessToken
 */
function get_access_tokens(string $account_id, string $service_name = "") {
    $pairs = select_pairs($account_id, $service_name)[0];

    return obj_to_array(new AccessToken($pairs['access_token'], $pairs['access_token_secret']));
}

/**
 * サービスとのペアが存在するかどうか
 *
 * @param string $account_id
 * @param string $service_name
 * @param string $service_account_id
 * @return boolean
 */
function exist_pair(string $account_id, string $service_name, string $service_account_id) {
    $mydb = new MyDB();

    $account_id = $mydb->escape($account_id);
    $service_name = $mydb->escape($service_name);
    $service_account_id = $mydb->escape($service_account_id);
    $sql = "SELECT COUNT(tamikusa_id) AS count FROM tamikusa_pairing"
            ." WHERE tamikusa_id = '$account_id' AND service_name = '$service_name' AND service_account_id = '$service_account_id'";
    $results = $mydb->select($sql);

    return ($results[0]['count']>=1);
}

/**
 * ログイン処理を行う
 *
 * @param string $account_id
 * @param string $password
 * @return boolean
 */
function login(string $account_id, string $password) {
    $result = "";

    $mydb = new MyDB();

    $id = $mydb->escape($account_id);

    $results = $mydb->select("SELECT * FROM tamikusa WHERE id = '$id'");

    $decrypted_pass = "";

    if(isset($results[0])) {
        $result = $results[0];

        $decrypted_pass = decrypt($result['password'], $result['enc_key']);

        $result = ($decrypted_pass==$password) ? $result['rand'] : "";
    }

    if(!empty($result)) {
        $results = $mydb->query("UPDATE tamikusa SET last_login_date='".date('Y-m-d H:i:s')."'"
                ." WHERE id = '$id'");
    }

    $mydb->close();

    return $result;
}

/**
 * アカウントを登録する
 *
 * @param string $account_id
 * @param string $password
 * @return boolean
 */
function register_account(string $account_id, string $password, string $enc_key) {
    $mydb = new MyDB();

    $account_id = $mydb->escape($account_id);
    $rand = md5(time());
    $password = $mydb->escape($password);
    $enc_key = $mydb->escape($enc_key);

    $sql = "INSERT INTO tamikusa (id, rand, password, enc_key, create_date)"
        ." VALUES ('$account_id', '$rand', '$password', '$enc_key', '".date('Y-m-d H:i:s')."')";

    $results = $mydb->query($sql);

    if ($results)
        return $rand;
    else
        return "";
}

/**
 * 指定されたアカウントが登録済みかどうか
 *
 * @param string $account_id
 * @return boolean
 */
function exist_account(string $account_id) {
    $mydb = new MyDB();

    $account_id = $mydb->escape($account_id);
    $sql = "SELECT COUNT(*) AS count FROM tamikusa WHERE id = '$account_id'";
    $results = $mydb->select($sql);

    return ($results[0]['count']>=1);
}

/**
 * アクセス情報を取得
 */
function getPassengerTokens($account_id, $service_name)
{
    $mydb = new MyDB();

    $id = $mydb->escape($account_id);

    $tmp = $mydb->select("SELECT access_token, access_token_secret, enc_key FROM passenger WHERE id = '$id' AND service_name = '$service_name'")[0];

    $enc_key = $tmp['enc_key'];

    $result = array();
    $result['access_token'] = decrypt($tmp['access_token'], $enc_key);
    $result['access_token_secret'] =
    (empty($tmp['access_token_secret'])) ? ""
        : decrypt($tmp['access_token_secret'], $enc_key);

    $mydb->close();

    return $result;
}

/**
 * アクセス情報を設定
 */
function setPassengerTokens($account_id, $service_name, $account_name, $display_name, $access_token,  $access_token_secret)
{
    $mydb = new MyDB();

    $id = $mydb->escape($account_id);
    $service_name = $mydb->escape($service_name);
    $name = $mydb->escape($account_name);
    $display_name = $mydb->escape($display_name);

    $encrypted_access_token = encrypt($access_token);
    $at = $mydb->escape($encrypted_access_token['data']);
    $enc_key = $encrypted_access_token['key'];

    $ats = (!empty($access_token_secret))
        ? $mydb->escape(encrypt($access_token_secret, $enc_key)['data'])
        : "";

    $results = $mydb->select("SELECT COUNT(id) AS count FROM passenger WHERE id = '".$id."'");

    if($results[0]['count']==0) {
        $query = "INSERT INTO passenger (id, service_name, name, display_name, access_token, access_token_secret, enc_key, create_date, last_login_date)"
            ." VALUES ('$id', '$service_name', '$name', '$display_name', '$at', '$ats', '$enc_key', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."');";

        $results = $mydb->insert($query);
    } else {
        $query = "UPDATE passenger"
            ." SET access_token='$at', access_token_secret='$ats', name='$name', display_name='$display_name', enc_key='$enc_key', last_login_date='".date('Y-m-d H:i:s')."'"
                ." WHERE id = '$id' AND service_name = '$service_name'";

            $results = $mydb->select($query);
    }

    $mydb->close();

    return $results[0];
}

/**
 * ユーザ情報の存在有無を確認
 */
function isUsers($account_name)
{
    $mydb = new MyDB();

    $account_name = $mydb->escape($account_name);

    $results = $mydb->select("SELECT COUNT(name) FROM users WHERE name = '$account_name'");

    $mydb->close();

    return ($results[0][0]==1);
}
/**
 * ユーザ情報を設定
 */
function addUsers($account_name, $password)
{
    $mydb = new MyDB();

    $account_name = $mydb->escape($account_name);
    $password = hash('sha512', $password);


    $query = "INSERT INTO users (name, password, hs_mode, rt_off)"
        ." VALUES ('$account_name', '$password', TRUE, FALSE);";

    $results = $mydb->insert($query);

    $mydb->close();

    return $results;
}

/**
 * 引数をキーワードに地域を特定しトレンドを取得する
 *
 * @param string $place_keyword
 * @return array|object
 */
function getTrendByWords(string $place_keyword) {
    $trend_words = $trends = array();

    $twitterLoginAccount = getSessionParam('twitterLoginAccount', "");

    $tokens = getTwitterTokens("", $twitterLoginAccount['id'], false);

    $connection = getTwitterConnection($tokens->token, $tokens->secret);

    $trends = $connection->get('geo/search', ['query' => $place_keyword]);

    if(isset($trends->errors)) {
        return $trends->errors;
    }

    $idokeido = $trends->result->places[0]->centroid;

    $params = array(
        "lat" => $idokeido[1]
        , "long" => $idokeido[0]
    );

    $closest = $connection->get('trends/closest', $params);
    if(!isset($closest->errors) && isset($closest[0]) && isset($closest[0]->woeid)) {
        $woeid = $connection->get('trends/closest', $params)[0]->woeid;
        $trend_words = $connection->get('trends/place', ['id'=>$woeid]);
    }

    return $trend_words;
}