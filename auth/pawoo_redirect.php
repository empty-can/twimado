<?php
require_once ("init.php");
use theCodingCompany\Mastodon;


$code = getGetParam('code', '');

if(!empty($code)) {
    $app_config = array(
        "client_name"   => AppName,
        "redirect_uris" => AppURL."/auth/pawoo_redirect.php",
        "scopes"    => "read write",
        "website"   => AppURL
    );
    
    $credentials = array(
        "client_id"   => PawooClientID,
        "client_secret" => PawooClientSecret,
        "bearer"    => ""
    );
    
    $pawoo = new Mastodon(PawooDomain);
    $pawoo->setAppConfig($app_config);
    $pawoo->setCredentials($credentials);
    $token = $pawoo->getAccessToken($code);
    setSessionParam("pawoo_access_token", $token);
    header('Location: /');
    //     header('Location: '.AppURL."/auth/auth.php");
    exit();
}