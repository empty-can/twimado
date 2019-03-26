<?php
require_once ("init.php");

interface Account {
    
    public function id();
    
    public function displayName();
    
    public function accountName();
    
    public function profileImage();
    
    public function profileThumbnail();
    
    public function followingCount();
    
    public function followersCount();
    
}