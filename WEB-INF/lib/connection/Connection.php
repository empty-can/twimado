<?php
require_once ("init.php");

class Connection {
    
    public $connection = null;
    
    public function __construct($connection) {
        $this->$connection = $connection;
    }
    
    public function getRequest() {
        
    }
    
    public function postRequest() {
        
    }
}