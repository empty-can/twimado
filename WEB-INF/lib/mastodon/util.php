<?php
require_once ("init.php");
use theCodingCompany\Mastodon;


/**
 * Mastodon のコネクションを取得するための共通関数
 * 
 * @param string $mastodon_url
 * @param string $user_token
 * @return theCodingCompany\Mastodon
 */
function getMastodonConnection(string $mastodon_domain, string $user_token = "") {
    $twitterAccessToken = getSessionParam('pawooAccessToken', "");
    
    if (empty($user_token)) {
        if (!empty($twitterAccessToken) && isset($twitterAccessToken->access_token)) {
            $user_token = $twitterAccessToken->access_token;
        } else {
            $user_token = TwitterAccessToken;
        }
    }
    
    if (empty($user_token)) {
        $user_token = getSessionParam("pawoo_access_token", PawooAccessToken);
    }
    
    $connection = new Mastodon($mastodon_domain);
    
    $connection->setMastodonDomain($mastodon_domain);
    $connection->setAppConfig([
        "client_name"   => AppName,
        "redirect_uris" => AppURL."/auth/pawoo_redirect.php",
        "scopes"    => "read write",
        "website"   => AppURL
    ]);
    $connection->setCredentials([
        "client_id" => PawooClientID,
        "client_secret" => PawooClientSecret,
        "bearer" => $user_token
    ]);
    
    return $connection;
}


/**
 * Mastodonのアクセストークンを手動で取得するときに使う
 *
 * @param string $url
 * @param string $grant_type
 * @param string $redirect_uri
 * @param string $client_id
 * @param string $client_secret
 * @param string $code
 * @return string
 */
function getAccessToken($url, $grant_type, $redirect_uri, $client_id, $client_secret, $code) {
    
    $data = http_build_query(array(
        'grant_type' => $grant_type,
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $code
    ), '', '&');
    
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