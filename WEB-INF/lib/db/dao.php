<?php
/**
 * アクセス情報を取得
 */
function getTokens($account_id)
{
    $mydb = new MyDB();
    
    $id = $mydb->escape($account_id);
    
    $results = $mydb->select("SELECT * FROM total_accounts WHERE id = '$id'");
    
    $mydb->close();
    
    return $results[0];
}

/**
 * アクセス情報を設定
 */
function setTokens($account_id, $account_name, $access_token,  $access_token_secret)
{
    $mydb = new MyDB();
    
    $id = $mydb->escape($account_id);
    $name = $mydb->escape($account_name);
    $at = $mydb->escape($access_token);
    $ats = $mydb->escape($access_token_secret);
    
    $results = $mydb->select("SELECT COUNT(id) FROM total_accounts WHERE id = '".$id."'");
    
    if($results[0][0]==0) {
        $query = "INSERT INTO total_accounts (id, name, access_token, access_token_secret)"
            ." VALUES ('$id', '$name', '$at', '$ats');";
            
            $results = $mydb->insert($query);
    } else {
        $query = "UPDATE total_accounts"
            ." SET access_token = '".$at."'"
            .", access_token_secret = '".$ats."'"
            .", name = '".$name."'"
            ." WHERE id = '$id'";
        
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