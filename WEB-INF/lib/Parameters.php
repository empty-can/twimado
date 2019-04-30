<?php
require_once ("init.php");

class Parameters {
    public $parameters = array();
    
    public function constructFromGetParameters() {
        $this->parameters = $_GET;
    }
    public function constructFromPostParameters() {
        $this->parameters = $_POST;
    }
    
    public function setParam(string $key, $value) {
        if(isset($value)&& $value!=NULL && !empty($value))
            $this->parameters[$key] = $value;
    }
    
    public function setInitialValue(string $key, $value) {
        if(!isset($this->parameters[$key]))
            $this->parameters[$key] = $value;
    }
    
    public function unset(string $key) {
        if(isset($this->parameters[$key]))
            unset($this->parameters[$key]);
    }
    
    public function moveValue(string $from_key, string $to_key) {
        if(isset($this->parameters[$from_key])) {
            $this->parameters[$to_key] = $this->parameters[$from_key];
            unset($this->parameters[$from_key]);
        }
    }
    
    public function putValue(string $key) {
        if(isset($this->parameters[$key])) {
            $result = $this->parameters[$key];
            unset($this->parameters[$key]);
            return $result;
        }
        
        return "";
    }
    
    public function getValue(string $key) {
        if(isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        
        return "";
    }
}