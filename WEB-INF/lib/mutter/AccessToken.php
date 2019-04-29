<?php
require_once ("init.php");

class AccessToken {
    public $access_token = "";
    public $access_token_secret = "";
    
    public function __construct(string $token = "", string $secret = "") {
        $this->access_token = $token;
        $this->access_token_secret = $secret;
    }
}