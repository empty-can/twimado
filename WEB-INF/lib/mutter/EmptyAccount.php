<?php
require_once ("init.php");

class EmptyAccount {
    
    public $id = "-1";
    public $displayName = "管理者からのお知らせ";
    public $accountName = "Suki Pic";
    public $profileImage = AppURL."/favicon.png";
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