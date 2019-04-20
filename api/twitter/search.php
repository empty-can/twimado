<?php

require_once ("init.php");

$api = 'search/tweets';

$q = getGetParam('q', '');
$count = getGetParam('count', '100');
$max_id = getGetParam('max_id', '');

$params = array(
    "q" => $q
    , "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

if(!empty($q)) {
    $tweets = getTwitterConnection("", "")->get($api, $params);
} else {
    $tweets = array();
}

$mutters = array();

if(isset($tweets->statuses)) {
    $statuses = $tweets->statuses;
    
    usort($statuses, function ($a, $b) {
        return $a->id > $b->id ? -1 : 1;
    });
    
    foreach ($statuses as $tweet) {
        $tmp = new Tweet($tweet);
        
        $oldest_mutter = $tmp;
        
        if ($tmp->hasMedia()) {
            if(isset($tmp->originalId)) {
                if(!isset($mutters[$tmp->originalId])) {
                    $mutters[$tmp->originalId] = $tmp;
                    //                 $mutters[$tmp->id] = $tmp;
                }
            } else {
                $mutters[$tmp->id] = $tmp;
            }
        }
    }
}

$response = array();
$response['mutters'] = array();
$response['oldest_mutter'] = null;

$response['error'] = ob_get_contents();
ob_end_clean();

if(!empty($max_id) && $oldest_mutter->id >= $max_id) {
    $response['error'] .= "no result";
} else if(empty($mutters)) {
    $response['error'] .= "no result";
} else {
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest_mutter;
}


// $response['error'] = count($tweets);
// echo $response['error'];
// myVarDump($tweets->statuses);
// myVarDump($mutters);
// myVarDump($response['oldest_mutter']);

echo json_encode($response);