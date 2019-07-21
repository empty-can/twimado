<?php
require_once ("init.php");

function ceilNum($target) {
    if(((int)$target)>10000)
        return round(((int)$target)/10000,1)."万";
    else
        return $target;
}

/**
 * 暗号化されたキーを復元する
 *
 * @param string $base64_encrypted
 * @param string $base64_enc_key
 * @return string
 */
function decrypt(string $base64_encrypted, string $base64_enc_key) {
    $encrypted = base64_decode($base64_encrypted);
    $iv = base64_decode($base64_enc_key);
    $options = OPENSSL_RAW_DATA;

    $decrypted = openssl_decrypt($encrypted, EncMethod, Passphrase, $options, $iv);

    return $decrypted;
}

/**
 * 対象文字列を暗号化する
 *
 * @param string $target
 * @param string $base64_enc_key    他で作った暗号化キーを使いまわしたい場合指定する
 * @return string[]
 */
function encrypt(string $target, string $base64_enc_key="") {

    $result = array();

    if(empty($base64_enc_key)) {
        $iv_size = openssl_cipher_iv_length(EncMethod);
        $iv = openssl_random_pseudo_bytes($iv_size);
        $result['key'] = base64_encode($iv);
    } else {
        $iv = base64_decode($base64_enc_key);
        $result['key'] = $base64_enc_key;
    }

    $options = OPENSSL_RAW_DATA;

    $result['data'] = base64_encode(openssl_encrypt($target, EncMethod, Passphrase, $options, $iv));

    return $result;
}

function searchTag(string $text, string $target="_blank") {
    $pattern = '([#＃][^』」】 \r\n]+)';
    $replacement = '<a href="'.AppURL.'/timeline/search.php?q=\1" target="$target">\1</a>';
    return mb_ereg_replace('=[#]+', '=%23', mb_ereg_replace($pattern, $replacement, $text));
}

/**
 * 文字列内のhttp文字列を<a>タグに。
 *
 * @param string $target
 * @return string
 */
function decorateLinkTag(string $text, string $target="_blank") {
    $pattern = '(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)';
    $replacement = '<a href="\1" target="$target">\1</a>';
    return mb_ereg_replace($pattern, $replacement, $text);
}

/**
 *
 * @param string $filename
 * @param string $new_prefix
 * @return mixed
 */
function replace_suffix(string $filename, string $new_prefix) {
    return substr($filename, 0, strrpos($filename, '.')).$new_prefix;
}
/**
 *
 * @param string $filename
 * @param string $new_prefix
 * @return mixed
 */
function get_suffix(string $filename) {
    return substr($filename, strrpos($filename, '.') + 1);
}

/**
 * オブジェクトを配列に変換する関数
 *
 * @param $object
 * @return mixed
 */
function obj_to_array($object) {
    return my_json_decode(json_encode($object), true);
}

/**
 * 配列の最後の要素を取得する関数
 *
 * @param array $array
 * @return mixed
 */
function array_last(array $array) {
    return end($array);
}

/**
 *
 * @param string $url
 * @param array $params
 * @return string
 */
function getRequest(string $url, array $params = array()) {
//     $params["mySessionID"] = session_id();
    $data = http_build_query($params, '', '&');

    // header
    $header = array(
        "Content-Type: application/x-www-form-urlencoded",
        "Content-Length: ".strlen($data)
    );

    $context = array(
        "http" => array(
            "method"  => "POST",
            "header"  => implode("\r\n", $header),
            "content" => $data
        )
    );

//     myVarDump($url."?".$context["http"]["content"]);

    return file_get_contents($url, false, stream_context_create($context));
}

/**
 *
 * @param string $url
 * @param array $params
 * @return string
 */
function postRequest(string $url, array $params = array()) {

    $data = http_build_query($params, '', '&');

    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "User-Agent: php.file_get_contents\r\n" . // 適当に名乗ったりできます
            "Content-Length: " . strlen($data) . "\r\n",
            'content' => $data
        )
    );

    $context = stream_context_create($options);

    return file_get_contents($url, false, $context);
}

/**
 *
 * @param string $target
 * @param string $pattern
 * @return boolean
 */
function contains($target, $pattern) {
    if (strpos($target, $pattern) === false)
        return false;
    else
        return true;
}

/**
 * GETパラメータを取得する
 */
function getGetParam($key, $default = "")
{
    if (isset($_GET[$key]))
        return $_GET[$key];
        else
            return $default;
}

/**
 * GETパラメータを設定する
 */
function setGetParam($key, $value)
{
    $_GET[$key] = $value;
}
/**
 * POSTパラメータを取得する
 */
function getPostParam($key, $default = "")
{
    if (isset($_POST[$key]))
        return $_POST[$key];
        else
            return $default;
}

/**
 * SESSIONパラメータを取得する
 */
function getSessionParam($key, $default = "")
{
    if (isset($_SESSION[$key]))
        return $_SESSION[$key];
        else
            return $default;
}

/**
 * SERVERパラメータを設定する
 *
 * @param string $key
 * @param string $value
 */
function setServerParam($key, $value)
{
    $_SERVER[$key] = $value;
}

/**
 * REQUESTパラメータを取得する
 */
function getRequestParam($key, $default = "")
{
    if (isset($_REQUEST[$key]))
        return $_REQUEST[$key];
        else
            return $default;
}

/**
 * REQUESTパラメータを設定する
 *
 * @param string $key
 * @param string $value
 */
function setRequestParam($key, $value)
{
    $_REQUEST[$key] = $value;
}

/**
 * SERVERパラメータを取得する
 */
function getServerParam($key, $default = "")
{
    if (isset($_SERVER[$key]))
        return $_SERVER[$key];
        else
            return $default;
}

/**
 * SESSIONパラメータを設定する
 *
 * @param string $key
 * @param string $value
 */
function setSessionParam($key, $value, $override=true)
{
    if($override) {
        $_SESSION[$key] = $value;
    } else if(!$override) {
        if(!isset($_SESSION[$key])) {
            $_SESSION[$key] = $value;
        } else {
            ;
        }
    }
}

/**
 * SERVERパラメータを取得する
 */
function getCookieParam($key, $default = "")
{
    if (isset($_COOKIE[$key]))
        return $_COOKIE[$key];
        else
            return $default;
}

/**
 * SESSIONパラメータを設定する
 *
 * @param string $key
 * @param string $value
 */
function setCookieParam($key, $value)
{
    $_COOKIE[$key] = $value;
}


/**
 * グローバルパラメータを設定する
 *
 * @param string $key
 * @param string $value
 */
function getGlobalParam($key, $default = "")
{
    if (isset($GLOBALS[$key]))
        return $GLOBALS[$key];
        else
            return $default;
}


/**
 * グローバルパラメータを設定する
 *
 * @param string $key
 * @param string $value
 */
function setGlobalParam($key, $value)
{
    $GLOBALS[$key] = $value;
}

/**
 * 配列のキーを存在チェックして取得
 *
 * @param array $target
 * @param string $key
 * @param string $default
 * @return object
 */
function getArrayParam(array $target, string $key, $default = "") {
    if(!is_array($target)) {
        return $default;
    } else if (isset($target[$key])) {
        return $target[$key];
    } else {
        return $default;
    }
}

/**
 * オブジェクトのプロパティを存在チェックして取得
 *
 * @param array $target
 * @param string $key
 * @param string $default
 * @return object
 */
function getObjectProps($target, string $key, $default = "") {
    if (isset($target->$key)) {
        return $target->$key;
    } else {
        return $default;
    }
}

/**
 * var_dumpを見やすく出力
 *
 * @param mixed $object
 */
function myVarDump($object, bool $comment=false) {
    if ($comment) {
        ?><!-- <?php
        var_dump($object);
        ?> --><?php
    } else {
        ?><pre><?php
        var_dump($object);
        ?></pre><?php
        exit();
    }
}

function logout() {

    //セッション変数を全て解除
    $_SESSION = array();

    //セッションクッキーの削除
    if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
    }
    if (isset($_COOKIE["login_cookie_id"])) {
        setcookie("login_cookie_id", '', time() - 1800, '/twimado/', 'www.suki.pics', false, false);
    }

    //セッションを破棄する
    session_destroy();
}

function my_json_decode(string $json, $assoc=false) {
	$result = json_decode($json, $assoc);

	if($result==NULL) {
		$constants = get_defined_constants(true);
		$json_errors = array();
		foreach ($constants["json"] as $name => $value) {
		    if (!strncmp($name, "JSON_ERROR_", 11)) {
		        $json_errors[$value] = $name;
		    }
		}
		echo "JSON Decode Error: ".$json_errors[json_last_error()].":error code:".json_last_error();
		myVarDump($json);
	} else {
		return  $result;
	}

}

/**
 *
 * @param object $a
 * @param object $b
 * @return number
 */
function sort_twitter_account_by_name($a, $b) {
    if ($a->name == $b->name) {
        return 0;
    }
    return ($a->name > $b->name) ? -1 : 1;
}

/**
 *
 * @param object $a
 * @param object $b
 * @return number
 */
function sort_pawoo_account_by_followers_count($a, $b) {
    if (((int)$a['followers_count']) == ((int)$b['followers_count'])) {
        return 0;
    }
    return (((int)$a['followers_count']) > ((int)$b['followers_count'])) ? -1 : 1;
}

/**
 *
 * @param object $a
 * @param object $b
 * @return number
 */
function sort_twitter_account_by_followers_count($a, $b) {
    if (((int)$a->followers_count) == ((int)$b->followers_count)) {
        return 0;
    }
    return (((int)$a->followers_count) > ((int)$b->followers_count)) ? -1 : 1;
}