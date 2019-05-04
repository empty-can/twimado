<?php
require_once ("init.php");

$api = 'lists/statuses';

$account = getPostParam('account', '');
$id = getPostParam('id', '');
$list_id = getPostParam('list_id', TwitterList);
$count = getPostParam('count', '200');
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
$response['oldest_mutter'] = new EmptyMutter("twitter");

if (empty($list_id)) {
    $errorMutter = new ErrorMutter("twitter");
    $errorMutter->addMessage("No list id specified.");
    $response['mutters'][] = obj_to_array($errorMutter);
    echo json_encode($response);
    exit();
}

$params = array(
    "list_id" => $list_id,
    "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

$tweets = getTwitterConnection($access_token, $access_token_secret)->get($api, $params);

$mutters = array();
$oldest = new EmptyMutter();

if(isset($tweets->error)) {
    $errorMutter = new ErrorMutter("twitter");
    $errorMutter->addError($tweets->error);
    $response['mutters'][] = obj_to_array($errorMutter);
    echo json_encode($response);
    exit();
}

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
    $errorMutter = new ErrorMutter("twitter");
    $errorMutter->addMessage("検索結果".count($mutters)."件");
    $mutters['-1'] = obj_to_array($errorMutter);
}


$response['mutters'] = $mutters;
$response['oldest_mutter'] = $oldest;

$response['error'] = ob_get_contents();
ob_end_clean();

// myVarDump($response['error']);
// $response['error'] = json_encode($_POST);

echo json_encode($response);