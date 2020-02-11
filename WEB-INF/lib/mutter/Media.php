<?php
require_once ("init.php");

class Media {
    public $url = "";
    public $thumb = "";
    public $raw = "";

    public function __construct(string $url = "", string $thumb = "", string $raw = "") {
        $this->url = $url;
        $this->thumb = $thumb;
        $this->raw = $raw;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getTumb() {
        return $this->thumb;
    }

    public function getRaw() {
        return $this->raw;
    }

    public function hasMedia() {
        return !empty($this->url);
    }
}

