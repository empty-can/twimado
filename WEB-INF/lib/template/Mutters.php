<?php
require_once ("init.php");

class Mutters {
    public $parameters = array();
    public $oldest_id = "";
    
    
    public function __construct($parameters, $oldest_id) {
        $this->parameters = $parameters;
        $this->oldest_id = $oldest_id;
    }
    
    
    public function get_parameters() {
        return $this->parameters;
    }
    
    public function get_oldest_id() {
        if(!empty($this->oldest_id)) {
            return $this->oldest_id;
        }
    }
}