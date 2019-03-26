<?php
require_once ("init.php");

abstract class StandardAccount implements Account {
    
    public $id = "-1";
    public $displayName = "未初期化";
    public $accountName = "未初期化";
    public $profileImage = "未初期化";
    public $followingCount = -1;
    public $followersCount = -1;
    
    public function id() {
        return $this->id;
    }
    
    public function displayName() {
        return $this->displayName;
    }
    
    public function accountName() {
        return $this->accountName;
    }
    
    public function profileImage() {
        return $this->profileImage;
    }
    
    public function followingCount() {
        return $this->followingCount;
    }
    
    public function followersCount() {
        return $this->followersCount;
    }
    
    
}