<?php
require_once ("init.php");

interface Mutter {
    
    public function account();
    
    public function hasMedia();
    
    public function isRe();
    
    public function mediaURLs();
    
    public function comCount();
    
    public function favCount();
    
    public function reCount();
    
    public function text();
    
    public function id();
    
    public function providerIcon();
    
    public function sensitive();
    
    public function originalId();
    
    public function originalTime();
    
    public function originalDate();
    
    public function mutterURL();
    
    public function convertArray();
    
    public function thumbnailURLs();
}