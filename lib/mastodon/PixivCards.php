<?php
require_once ("init.php");

class PixivCards extends Media {
    
    public function __construct(object $card) {
        $this->url = $card['image_url'];
        $this->thumb = $card['image_url'];
    }
}

