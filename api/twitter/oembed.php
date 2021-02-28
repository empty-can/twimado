<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$tweet_id = $param->parameters['id'];
$maxwidth = $param->parameters['maxwidth'];
$account_name = $param->parameters['account'];

$target_url = "https://twitter.com/$account_name/status/$tweet_id";
echo json_decode(file_get_contents("https://publish.twitter.com/oembed?omit_script=false&lang=ja&maxwidth=$maxwidth&align=center&url=".urlencode($target_url)))->html;
exit();