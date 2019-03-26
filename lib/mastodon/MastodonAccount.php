<?php
require_once ("init.php");

class MastodonAccount extends StandardAccount implements Account {
    
    public function __construct($account) {
        if(is_array($account))
            $account = (object)$account;
        
        $this->id = $account->id;
        $this->displayName = $account->display_name;
        $this->accountName = $account->acct;
        $this->profileImage = $account->avatar;
        $this->followingCount = $account->following_count;
        $this->followersCount = $account->followers_count;
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