<?php
require_once ("init.php");

$api = 'statuses/home_timeline';

$account = getPostParam('account', '');
$id = getPostParam('id', '');
$count = getPostParam('count', '20');
$max_id = getPostParam('max_id', '');
$mo = getPostParam('mo', 'true');


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

$response = array();
$response['mutters'] = array();
$response['oldest_mutter'] = new EmptyMutter();

$params = array(
    "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

$tweets = getTwitterConnection($access_token, $access_token_secret)->get($api, $params);

if(isset($tweets->error)) {
    $errorMutter = new ErrorMutter();
    $errorMutter->addError($tweets->error);
    $response['mutters'][] = $errorMutter;
    json_encode($response);
    exit();
}

$mutters = array();
$oldest = "";
$i = (int)0;

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);
    
    $oldest = $tmp;
    $originalId = $tmp->originalId();
    
    if($mo=='false') {
        $mutters[$originalId] = $tmp;
    } else if ($tmp->hasMedia() && !isset($mutters[$originalId])) {
        $mutters[$originalId] = $tmp;
    }
    
    $i++;
    
    if($i>$count) break;
}

if(count($mutters)==0) {
    $errorMutter = new ErrorMutter();
    $errorMutter->addMessage("検索結果".count($mutters)."件");
    $mutters['-1'] = $errorMutter;
}

$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

$response['error'] = ob_get_contents();
ob_end_clean();

echo json_encode($response);