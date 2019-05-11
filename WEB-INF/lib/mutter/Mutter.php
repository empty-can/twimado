<?php
require_once ("init.php");

interface Mutter {
    
    public function hasMedia();
    
    public function isRe();
    
    public function isImg();
    
    public function isVideo();
    
    public function isObject();
    
    public function comCount();
    
    public function favCount();
    
    public function reCount();
    
    public function time();
    
    public function date();
    
    public function domain();
    
    public function originalDate();
    
    public function id();
    
    public function account();
    
    public function text();
    
    public function providerIcon();
    
    public function sensitive();
    
    public function originalId();
    
    public function originalTime();
    
    public function retweeter();
    
    public function mutterURL();
    
    public function convertArray();
    
    public function isFavorited();
    
    public function isRetweeted();
    
    public function getSortValue();
    
    public function extractGoods();
    
    public function getGoods();
}