<?php

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
    $mydb = new MyDB();
    
    $id = $mydb->escape($account_id);
    
    $results = $mydb->select("SELECT * FROM tamikusa WHERE id = '$id'");
    
    $mydb->close();
    
    $decrypted_pass = "";
    
    if(isset($results[0])) {
        $result = $results[0];
        
        $decrypted_pass = decrypt($result['password'], $result['enc_key']);
        
        return ($decrypted_pass==$password);
    }
    
    return false;
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
    $password = $mydb->escape($password);
    $enc_key = $mydb->escape($enc_key);
    
    $sql = "INSERT INTO tamikusa (id, password, enc_key)"
        ." VALUES ('$account_id', '$password', '$enc_key')";
    
    $results = $mydb->query($sql);
    
    return $results;
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
        $query = "INSERT INTO passenger (id, service_name, name, display_name, access_token, access_token_secret, enc_key)"
            ." VALUES ('$id', '$service_name', '$name', '$display_name', '$at', '$ats', '$enc_key');";
            
        $results = $mydb->insert($query);
    } else {
        $query = "UPDATE passenger"
            ." SET access_token='$at', access_token_secret='$ats', name='$name', display_name='$display_name', enc_key='$enc_key'"
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