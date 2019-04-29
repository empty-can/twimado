<?php
require_once ("init.php");

$hs = getPostParam('hs', 'true');
$count = getPostParam('count', '20');
$thumb = getPostParam('thumb', 'true');

$csss=array();
$csss[] = "timeline";
$smarty->assign("csss", $csss);

$jss=array();
$jss[] = "jquery-3.3.1.min";
$jss[] = "common";

$embedded_js_params_string = [
    "hs" => $hs
    ,"thumb" => $thumb
    ,"count" => $count
];

$embedded_js_params_int = [
];

$embedded_js_int = [
    "count" => $count
];

$smarty->assign("AppContext", AppContext);
$smarty->assign("hs", $hs);
$smarty->assign("mylists", getSessionParam("twitter_mylists", array()));
