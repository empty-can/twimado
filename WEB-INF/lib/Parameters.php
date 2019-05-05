<?php
require_once ("init.php");

class Parameters {
    public $parameters = array();
    public $required = array();
    public $optional = array();
    
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
    
    public function copyValue(string $from_key, string $to_key) {
        if(isset($this->parameters[$from_key])) {
            $this->parameters[$to_key] = $this->parameters[$from_key];
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
    
    /**
     * APIに渡す前に実行するバリデーション
     * 
     * @return string
     */
    public function validate(bool $delete_unknown_param=true) {
        $result = "";
        $keys = array_keys($this->parameters);
        
        foreach($this->required as $key) {
            if(!in_array($key, $keys)) {
                $result .= "必須パラメータ $key が設定されていません。<br>\r\n";
            }
        }
        
        foreach($keys as $key) {
            if(!in_array($key, $this->required) && !in_array($key, $this->optional)) {
                if($delete_unknown_param) {
                    // ToDo：不要なパラメータを削除する処理
                } else {
                    $result .= "不要なパラメータ $key が設定されています。<br>\r\n";
                }
            }
        }
        
        return $result;
    }
}