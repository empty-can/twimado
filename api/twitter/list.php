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

$params = array(
    "list_id" => $list_id,
    "count" => $count
);

if(!empty($max_id)) {
    $params['max_id'] = $max_id;
}

ob_start();

if (empty($list_id)) {
    echo "リストIDが指定されていません。";
    goto end;
}

$tweets = getTwitterConnection($access_token, $access_token_secret)->get($api, $params);

if (isset($tweets->errors)) {
    echo "APIの実行に失敗しました。";
    foreach ($tweets->errors as $error) {
        echo "<br>\r\nエラーコード：".$error->code;
        echo "<br>\r\nメッセージ：".$error->message;
    }
    goto end;
}

$mutters = array();
$oldest = new EmptyMutter();
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

if($max_id == $oldest->id) {
    echo "最後のツイートまで到達しました。";
    goto end;
}

// myVarDump($response['error']);
// $response['error'] = json_encode($_POST);

end:

$stdout = ob_get_contents();
ob_end_clean();

if(!empty($stdout)) {
    $errorMutter = new ErrorMutter("twitter");
    $errorMutter->addMessage($stdout);
    $response['mutters']['-1'] = obj_to_array($errorMutter);
    echo json_encode($response);
    exit();
} else {
    $response['mutters'] = $mutters;
    $response['oldest_mutter'] = $oldest;
    echo json_encode($response);
}