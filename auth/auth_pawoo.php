<?php
require_once ("init.php");

use theCodingCompany\Mastodon;

$pawoo = new Mastodon(PawooDomain);

$pawoo->setAppConfig([
    "client_name"   => AppName,
    "redirect_uris" => AppURL."/auth/pawoo_redirect.php",
    "scopes"    => "read write",
    "website"   => AppURL
]);

$pawoo->setCredentials([
    "client_id"   => PawooClientID,
    "client_secret" => PawooClientSecret,
    "bearer"    => ""
]);

$auth_url = $pawoo->getAuthUrl();

header('Location: '.$auth_url);