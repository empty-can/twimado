<?php
require_once ("init.php");

class EmptyMutter extends StandardMutter {
    public $providerIcon = AppURL."/favicon.png";
    public $domain = '';
    public $mutterBase = AppURL;
    public $mutterURL = AppURL;
    
    public function __construct() {
        $this->text = "ダミーツイートです";
        $this->account = new EmptyAccount();
        $this->retweeter = new EmptyAccount();
    }
}