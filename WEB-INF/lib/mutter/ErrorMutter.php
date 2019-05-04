<?php
require_once ("init.php");

class ErrorMutter extends StandardMutter {
    public $providerIcon = AppURL."/favicon.png";
    public $domain = '';
    public $mutterBase = AppURL;
    public $mutterURL = AppURL;
    
    public function __construct() {
        $this->text = "";
        $this->account = new EmptyAccount();
        $this->retweeter = new EmptyAccount();
    }
    
    public function addMessage(string $message) {
        $this->text = "メッセージ：".$this->text.$message;
    }
    
    public function addError(object $error) {
        $this->text = $this->text.implode('エラーが発生しました。', obj_to_array($error));
    }
}