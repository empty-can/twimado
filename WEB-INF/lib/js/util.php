<?php
require_once ("init.php");

/**
 * 連想配列を埋め込みJavaScriptにするための関数
 * 
 * @param array $js_string_params
 * @param array $js_assocarray
 * @return string
 */
function build_embededd_js_assocarray(array $js_string_params) {
    //     $result = "var mutterQueue = [];\r\n";
    $result = "";
    
    foreach ($js_string_params as $key => $value) {
        $result .= "var $key = [];\r\n";
        
        foreach ($value as $tmp_key => $tmp_value) {
            $result .= $key."['".$tmp_key."'] = '".preg_replace("/[\r\n]/", "", $tmp_value)."'\r\n";
        }
        $result .= ";";
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
function build_embededd_js(array $js_string_params, array $js_int_params) {
    //     $result = "var mutterQueue = [];\r\n";
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

/**
 * mutterをjsに変換
 * 
 * @param array $mutters
 * @return string
 */
function build_embededd_mutters(array $mutters) {
    $mutterIds = "";
    $mutterQueue = "";
    
    foreach(array_keys($mutters) as $mutters_key) {
        $mutterIds .= '"'.$mutters_key.'"'.",\r\n";
        $mutterQueue .= "'".preg_replace("/'/", '&#39;', preg_replace("/[\r\n]+/", '', $mutters[$mutters_key]))."',\r\n";
    }
    
    $mutterIds = preg_replace("/,\r\n$/", "", $mutterIds);
    $mutterQueue = preg_replace("/,\r\n$/", "", $mutterQueue);
    
    return "var mutterIds = [".$mutterIds."];\r\nvar mutterQueue = [".$mutterQueue."];\r\n";
}