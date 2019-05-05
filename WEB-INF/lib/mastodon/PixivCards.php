<?php
require_once ("init.php");

class PixivCards extends Media {
    
    public function __construct(array $card) {
        $this->url =  (isset($card['image_url'])) ? $card['image_url']: "" ;
        $this->thumb = $this->url;
    }
}

