<?php
require_once ("init.php");

class AccessToken {
    public $token = "";
    public $secret = "";
    
    public function __construct(string $token = "", string $secret = "") {
        $this->token = $token;
        $this->secret = $secret;
    }
}