<?php
require_once ("init.php");

class Media {
    public $url = "";
    public $thumb = "";
    
    public function __construct(string $url, string $thumb) {
        $this->url = $url;
        $this->thumb = $thumb;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getTumb() {
        return $this->thumb;
    }
}

