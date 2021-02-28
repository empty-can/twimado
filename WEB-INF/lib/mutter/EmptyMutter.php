<?php
require_once ("init.php");

class EmptyMutter extends StandardMutter {
    public $providerIcon = AppURL."/favicon.png";
    public $domain = '';
    public $mutterBase = AppURL;
    public $mutterURL = AppURL;
    
    public function __construct($domain) {
        
        if($domain=='twitter') {
            $this->providerIcon = 'https://abs.twimg.com/responsive-web/web/icon-default.604e2486a34a2f6e1.png';
        } else if($domain=='pawoo') {
            $this->providerIcon = 'https://pawoo.net/favicon.ico';
        }
        
        $this->id = -1;
        $this->text = "ダミーツイートです";
        $this->account = new EmptyAccount();
        $this->retweeter = new EmptyAccount();
    }
}