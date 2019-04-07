<?php
require_once ("init.php");

abstract class StandardMutter implements Mutter {
    
    public $id = "-1";
    public $isRe = false;
    public $date = null;
    public $time = "-1";
    public $text = "初期値";
    
    public $account = null;
    public $retweeter = null;
    
    public $originalId = null;
    public $originalTime = null;
    
    public $comCount = "-1";
    public $favCount = "-1";
    public $reCount = "-1";
    
    public $sensitive = false;
    
    public $mediaURLs = null;
    public $thumbnailURLs = null;
    
    public $providerIcon = "初期値";
    public $domain = "初期値";
    public $mutterBase = "初期値";
    public $mutterURL = "初期値";
    
    public function hasMedia() {
        return !empty($this->mediaURLs);
    }
    
    public function isRe() {
        return $this->isRe;
    }
    
    public function mediaURLs() {
        return $this->mediaURLs;
    }
    
    public function thumbnailURLs() {
        return $this->thumbnailURLs;
    }
    
    public function comCount() {
        return $this->comCount;
    }
    
    public function favCount() {
        return $this->favCount;
    }
    
    public function reCount() {
        return $this->reCount;
    }
    
    public function time() {
        return $this->time;
    }
    
    public function date() {
        return $this->time;
    }
    
    public function originalDate() {
        return $this->originalTime;
    }
    
    public function id() {
        return $this->id;
    }
    
    public function account() {
        return $this->account;
    }
    
    public function text() {
        return $this->text;
    }
    
    public function providerIcon() {
        return $this->providerIcon;
    }
    
    public function sensitive() {
        return $this->sensitive;
    }
    
    public function originalId() {
        return $this->originalId;
    }
    
    public function originalTime() {
        return $this->originalTime;
    }
    
    public function retweeter() {
        return $this->retweeter;
    }
    
    public function mutterURL() {
        return $this->mutterBase.$this->id;
    }
    
    public function convertArray() {
        return obj_to_array($this);
    }
}