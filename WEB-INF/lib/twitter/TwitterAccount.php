<?php
require_once ("init.php");

class TwitterAccount extends StandardAccount implements Account {
    
    public function __construct($account) {
        //         $this->id = $account->id;
        $this->id = $account->screen_name;
        $this->displayName = $account->name;
        $this->accountName = $account->screen_name;
        $this->profileImage = $account->profile_image_url_https;
    }
    
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
    
    public function profileThumbnail() {
        return $this->profileImage;
    }
}