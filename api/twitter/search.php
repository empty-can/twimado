<?php
require_once ("init.php");

$api = 'search/tweets';

$account = getPostParam('account', '');
$id = getPostParam('id', '');
$q = getPostParam('q', '');
$count = getPostParam('count', '200');
$max_id = getPostParam('max_id', '');

if(!empty($account)) {
    $pair = get_access_tokens($account, 'twitter');
    $access_token = $pair['access_token'];
    $access_token_secret = $pair['access_token_secret'];
} else if(!empty($id)){
    $tokens = getPassengerTokens($id, 'twitter');
    $access_token = $tokens['access_token'];
    $access_token_secret = $tokens['access_token_secret'];
} else {
    $access_token = TwitterAccessToken;
    $access_token_secret = TwitterAccessTokenSecret;
}

$params = array(
    "q" => $q
    , "count" => 100
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

if(!empty($q)) {
    $tweets = getTwitterConnection($access_token, $access_token_secret)->get($api, $params);
} else {
    $tweets = array();
}

$mutters = array();

if(isset($tweets->statuses)) {
    $statuses = $tweets->statuses;
    
    usort($statuses, function ($a, $b) {
        return $a->id > $b->id ? -1 : 1;
    });
    
    $i = (int)0;
    
    foreach ($statuses as $tweet) {
        $tmp = new Tweet($tweet);
        
        $oldest = $tmp;
        $originalId = $tmp->originalId();
        
        if ($tmp->hasMedia() && !isset($mutters[$originalId]))
            $mutters[$originalId] = $tmp;
    
        $i++;
        
        if($i>$count) break;
    }
}

$response = array();
$response['mutters'] = array();
$response['oldest_mutter'] = null;

$response['error'] = ob_get_contents();
ob_end_clean();

if(!empty($max_id) && $oldest->id >= $max_id) {
    $response['error'] .= "no result";
} else if(empty($mutters)) {
    $response['error'] .= "no result";
} else {
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
}


// $response['error'] = count($tweets);
// echo $response['error'];
// myVarDump($tweets->statuses);
// myVarDump($mutters);
// myVarDump($response['oldest_mutter']);

echo json_encode($response);