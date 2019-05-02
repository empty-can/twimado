<?php

require_once ("init.php");

$key = $_GET['key'];

session_write_close();
session_start();

if(!empty($key)) {
    $value = (isset($_SESSION[$key])) ? $_SESSION[$key] : true;
    var_dump($value);
    var_dump($value=='true');
    var_dump($value=='false');
    if($value=='true') {
        $_SESSION[$key]='false';
    } else if($value=='false'){
        $_SESSION[$key]='true';
    }
}

// return json_encode(var_dum(getSessionParam($key)));