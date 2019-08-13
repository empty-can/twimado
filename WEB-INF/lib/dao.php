<?php

function insertCreator(string $user_id, string $domain, string $screen_name, string $name) {
    $results = "";

    $mydb = new MyDB();
    $user_id = $mydb->escape($user_id);
    $domain = $mydb->escape($domain);
    $screen_name = $mydb->escape($screen_name);
    $name = $mydb->escape($name);

    $sql = "INSERT INTO creator (`id`, `domain`, `screen_name`, `name`) VALUES ($user_id, '$domain', '$screen_name', '$name');";

    $results = $mydb->insert($sql);

    $mydb->close();

    return $results[0]['count(id)'];

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

function getAllCreators() {

    $mydb = new MyDB();

    $sql = "SELECT * FROM creator;";

    $results = $mydb->select($sql);

    $mydb->close();

    return $results;

}

function regMatome(string $mutter_id="", string $domain="", string $matome_id="") {
    $results = "";

    $mydb = new MyDB();
    $mutter_id = $mydb->escape($mutter_id);
    $domain = $mydb->escape($domain);
    $matome_id = $mydb->escape($matome_id);

    $sql = "INSERT INTO mvsm (`mutter_id`, `mutter_domain`, `matome_id`) VALUES ($mutter_id, '$domain', $matome_id);";
    //     myVarDump($sql);
    $results = $mydb->insert($sql);

    $mydb->close();

    return $results;
}

function delMatome(string $mutter_id="", string $domain="", string $matome_id="") {
    $results = "";

    $mydb = new MyDB();
    $mutter_id = $mydb->escape($mutter_id);
    $domain = $mydb->escape($domain);
    $matome_id = $mydb->escape($matome_id);

    $sql = "DELETE FROM mvsm WHERE `mutter_id`=$mutter_id AND `mutter_domain`='$domain' AND `matome_id`=$matome_id;";
    //     myVarDump($sql);

    $results = $mydb->query($sql);

    $mydb->close();

    return $results;
}

function getMatomeInfo(string $matome_id="") {
    $results = "";

    $mydb = new MyDB();
    $matome_id = $mydb->escape($matome_id);

    $sql = "SELECT * FROM matome WHERE id = '$matome_id';";

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

        $sql = "SELECT id, title FROM matome WHERE user_id = $user_id AND user_domain = '$user_domain';";

        $results = $mydb->select($sql);

        $mydb->close();
    }

    return $results;
}

function getMatomeIds(string $matome_id="", string $mutter_id="", int $asc=0, int $limit=100) {
    $results = "";

    if (!empty($matome_id)) {

        $mydb = new MyDB();
        $matome_id = $mydb->escape($matome_id);

        $sql = "SELECT mutter.id FROM matome, mvsm, mutter WHERE matome.id = $matome_id AND matome.id = mvsm.matome_id" . " AND mutter.id = mvsm.mutter_id AND mutter.domain = mvsm.mutter_domain";

        if(empty($mutter_id)) {
            if($asc==0) {
                $sql .= " ORDER BY id DESC";
            } else {
                $sql .= " ORDER BY id ASC";
            }
        } else {
            if($asc==0) {
                $sql .= " AND mutter.id < $mutter_id ORDER BY id DESC";
            } else {
                $sql .= " AND mutter.id > $mutter_id ORDER BY id ASC";
            }
        }

        $sql .= " LIMIT $limit";

//         echo $sql;

        $rows = $mydb->select($sql);

        foreach ($rows as $row) {
            $results .= $row["id"].",";
        }

        $mydb->close();

        if(!empty($results)) {
            $results = substr($results, 0, -1);
        }
    }

    return $results;
}

function getMatomeList(string $user_id="", string $domain="") {
    $results = "";

    $mydb = new MyDB();
    $user_id = $mydb->escape($user_id);
    $domain = $mydb->escape($domain);

    $sql = "SELECT matome.id, `title`, `description`, `user_id`, `user_domain`, count(matome.id) AS total FROM matome, creator";
    $sql .= " WHERE creator.id = user_id";
    if(!empty($user_id)) {
        $sql .= " AND user_id = '$user_id' AND user_domain = '$domain' GROUP BY matome.id ORDER BY matome.id;";
    } else {
        $sql .= " GROUP BY matome.id ORDER BY matome.id;";
    }

    $results = $mydb->select($sql);

//     var_dump($sql);
    $mydb->close();

    return (empty($results)) ? array() : $results;
}

function getMutterIds(string $account_id="", string $max_id=null, int $limit=100) {
    $results = "";

    if(!empty($account_id)) {
        $mydb = new MyDB();
        $account_id = $mydb->escape($account_id);

        $sql = "SELECT id FROM mutter WHERE user_id = '$account_id'";

        if(!empty($max_id)) {
            $sql .= " AND id <= $max_id";
        } else {
        }

        $sql .= " ORDER BY id DESC LIMIT $limit";

        $rows = $mydb->select($sql);

        $i=0;
        foreach ($rows as $row) {
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

    $connection = getTwitterConnection();

    $trends = $connection->get('geo/search', ['query' => $place_keyword]);

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