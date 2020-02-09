<?php
require_once("init.php");
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Twitter のアクセストークンを返却する
 *
 * @param string $account
 * @param string $passenger_id
 * @param bool $enable_app_token
 * @return string[]|AccessToken
 */
function getTwitterTokens(string $account="", string $passenger_id="", bool $enable_app_token=true) {
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
//        $tokens->token = TwitterAccessToken;
//        $tokens->secret = TwitterAccessTokenSecret;
		$tokens->token = "";
		$tokens->secret = "";
    }

    return $tokens;
}

function getTwitterApplicationBearerToken() {

    $bearerToken = base64_encode(
        rawurlencode(TwitterAppToken)
        .':'.
        rawurlencode(TwitterAppTokenSecret)
    );

    $params = array(
        'grant_type' => 'client_credentials'
    );

    $headers = [
        'Authorization: Basic '.$bearerToken,
        'Content-type: application/x-www-form-urlencoded;charset=UTF-8',
    ];

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, 'https://api.twitter.com/oauth2/token');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    $body = substr ($response, $info["header_size"]);

    curl_close($curl);

    return json_decode($body);
}

function getPawooTokens(string $account, string $passenger_id, bool $enable_app_token=true) {
    $tokens = new Tokens();

    if(!empty($account)) {
        $result = get_access_tokens($account, 'pawoo');
        $tokens->token = $result['access_token'];
    } else if(!empty($passenger_id)) {
        $result = getPassengerTokens($passenger_id, 'pawoo');
        $tokens->token = $result['access_token'];
    } else if($enable_app_token){
        $tokens->token = PawooAccessToken;
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
function getResponse(array $mutters, Mutter $oldest=null, Mutter $latest=null) {
    $response = array();
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
    $response['latest_mutter'] = $latest;
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

