<?php
require_once ("init.php");

class ErrorMutter extends StandardMutter {
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
        
        $this->text = "";
        $this->account = new EmptyAccount();
        $this->retweeter = new EmptyAccount();
        $this->originalTime = time();
        $this->originalDate = time();
    }
    
    public function addMessage(string $message) {
        $this->text = "メッセージ：".$this->text.$message;
    }
    
    public function addError(object $error) {
        $this->text = $this->text.implode('エラーが発生しました。', obj_to_array($error));
    }
}