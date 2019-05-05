<?php
require_once("init.php");

/**
 * Twitter のアクセストークンを返却する
 *
 * @param string $account
 * @param string $passenger_id
 * @param bool $enable_app_token
 * @return string[]|AccessToken
 */
function getTwitterTokens(string $account, string $passenger_id, bool $enable_app_token=true) {
    $tokens = new Tokens();
    
    if(!empty($account)) {
        $result = get_access_tokens($account, 'twitter');
        $tokens->token = $result['access_token'];
        $tokens->secret = $result['access_token_secret'];
    } else if(!empty($passenger_id)) {
        $result = getPassengerTokens($passenger_id, 'twitter');
        $tokens->token = $result['access_token'];
        $tokens->secret = $result['access_token_secret'];
    } else if($enable_app_token){
        $tokens->token = TwitterAccessToken;
        $tokens->secret = TwitterAccessTokenSecret;
    }
    
    return $tokens;
}

function getPawooTokens(string $account, string $passenger_id, bool $enable_app_token=true) {
    $tokens = new Tokens();
    
    if(!empty($account)) {
        $result = get_access_tokens($account, 'pawoo');
        $tokens->token = $result['access_token'];
    } else if(!empty($passenger_id)) {
        $result = getPassengerTokens($passenger_id, 'pawoo');
        $tokens->token = $result['access_token'];
    }
    
    return $tokens;
}

/**
 * エラーレスポンスを生成
 *
 * @param array $mutters
 * @param Mutter $oldest
 * @return array
 */
function gerErrorResponse(string $errorDomain, string $errorMessage) {
    $errorMutter = new ErrorMutter("$errorDomain");
    $errorMutter->addMessage($errorMessage);
    
    $response = array();
    $response['oldest_mutter'] = new EmptyMutter("twitter");
    
    $response['mutters'] = array();
    $response['mutters']['-1'] = obj_to_array($errorMutter);
    
    return $response;
}

/**
 * 正常レスポンスを生成
 * 
 * @param array $mutters
 * @param Mutter $oldest
 * @return array
 */
function getResponse(array $mutters, Mutter $oldest) {
    $response = array();
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
    return $response;
}

/**
 * アクセストークンを格納するクラス
 * 
 * @author Administrator
 *
 */
class Tokens {
    public $token = "";
    public $secret = "";
    
    public function toString() {
        return "{token:".$this->token.",secret:".$this->secret."}";
    }
    
    public function isEmpty() {
        return (empty($this->token) && empty($this->secret));
    }
}

