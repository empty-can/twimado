<?php
require_once ("init.php");

/**
 * 埋め込み用JavaScript を生成するための関数
 * 
 * @param string $api
 * @param array $max_ids
 * @param int $count
 * @return mixed
 */
function build_embededd_js(array $js_string_params, array $js_int_params) {
    $result = "";
    
    foreach(array_keys($js_string_params) as $param_key) {
        $result .= "var $param_key='".$js_string_params[$param_key]."';\r\n";
    }
    foreach(array_keys($js_int_params) as $param_key) {
        $result .= "var $param_key=".$js_int_params[$param_key].";\r\n";
    }
    
    return $result;
}

/**
 * 埋め込み用JavaScript を生成するための関数
 *
 * @param string $api
 * @param array $max_ids
 * @param int $count
 * @return mixed
 */
function build_embededd_js_params(array $js_string_params, array $js_int_params) {
    $result = "var params = {};\r\n";
    
    foreach(array_keys($js_string_params) as $param_key) {
        $result .= "params['$param_key']='".$js_string_params[$param_key]."';\r\n";
    }
    foreach(array_keys($js_int_params) as $param_key) {
        $result .= "params['$param_key']='".$js_int_params[$param_key]."';\r\n";
    }
    
    return $result;
}