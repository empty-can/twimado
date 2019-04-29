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
    
    $connection = getMastodonConnection(PawooDomain, $token);
    $pawooAccount = $connection->executeGetAPI('api/v1/accounts/verify_credentials');

    //各値をセッションに入れる
    setSessionParam("pawooLoginAccount", $pawooAccount);
    setSessionParam("pawooAccessToken", new AccessToken($token, ""));
    
    
    // アプリにログインしていればDBへ連携情報を登録する
    $account_id = getSessionParam("account", "");
    if(!empty($account_id)) {
        
        $service_user_info = [
            'id' => $pawooAccount["id"]
            ,'user_name' => $pawooAccount["username"]
            ,'display_name' => $pawooAccount["display_name"]
            ,'token' => $token
        ];
        
        register_pairing($account_id, "pawoo", $service_user_info);
    } else {
        setPassengerTokens($pawooAccount['id'], 'pawoo', $pawooAccount['display_name'], $pawooAccount['username'], $token,  "");
    }
    
    header('Location: /');
    exit();
}else{
    header('Location: /');
    exit();
}