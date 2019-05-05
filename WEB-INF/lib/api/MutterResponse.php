<?php
require_once ("init.php");

class MutterResponse {
    public $mutters = array();
    public $oldest_mutter = null;
    
    public function __construct() {
    }
    
    public function setMutters(array $mutters) {
        $this->mutters = $mutters;
    }
    
    public function setErrorMutter(ErrorMutter $mutter) {
        $this->mutters = array();
        $this->mutters['-1'] = $mutter->toArray();
    }
}