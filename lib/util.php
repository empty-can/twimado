<?php
require_once ("init.php");

function searchTag(string $text, string $target="_blank") {
    $pattern = '([#＃][^』」】 \r\n]+)';
    $replacement = '<a href="'.AppURL.'/timeline/search.php?q=\1" target="$target">\1</a>';
    return str_replace('=#', '=%23', mb_ereg_replace($pattern, $replacement, $text));
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
    return json_decode(json_encode($object), true);
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
//     myVarDump($url.'?'.$data);

    return file_get_contents($url.'?'.$data);
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
function setSessionParam($key, $value)
{
    $_SESSION[$key] = $value;
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
 * var_dumpを見やすく出力
 *
 * @param mixed $object
 */
function myVarDump($object) {
    ?><pre><?php
    var_dump($object);
    ?></pre><?php
    exit();
}