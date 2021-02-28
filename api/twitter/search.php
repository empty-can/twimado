<?php
require_once ("init.php");

$api = 'search/tweets';  // アクセスするAPI

/*------------ パラメータの取得設定 ------------*/
$param = new Parameters();

if(isPost()) {
	$param->constructFromPostParameters();
} else {
	$param->constructFromGetParameters();
}

$param->required = ["q"];
$param->optional = ["since_id", "max_id", "count", "until", "locale", "result_type", "f"];

$param->setInitialValue('count', '100');
$param->setParam('count', '100');
$min_count = $param->getValue('count');

$param->setParam('raw', 'false');
$param->setParam('locale', 'ja');
$param->setParam('result_type', 'mixed');

$account = $param->putValue('account');
$passenger_id = $param->putValue('id');
$media_only = $param->putValue('mo');
$filter = $param->putValue('f');
/*-----------------------------------------*/

$query = $param->getValue('q');

if(!empty($filter)) {
    $q = $param->getValue('q');
    $param->setParam('q', $q.' filter:'.$filter);
}


// 標準出力の監視開始
ob_start();

// パラメータのチェック
$validated = $param->validate();

if(!empty($validated)) {
    echo $validated;
    goto end;
}

// アクセストークンの取得
$tokens = getTwitterTokens($account, $passenger_id, true);

if($tokens->isEmpty()) {
    echo "認証情報が取得できませんでした。";
    goto end;
}

// APIアクセス


$api_result = getTwitterConnection($tokens->token, $tokens->secret)
                ->get($api, $param->parameters);

if($param->getValue('json')=='true') {
    ob_end_clean();
    // echo json_encode($api_result);
    echo json_encode($api_result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

// APIアクセスのエラー確認
if (isset($api_result->errors)) {
    echo "APIの実行に失敗しました。";
    foreach ($api_result->errors as $error) {
        echo "<br>\r\nエラーコード：".$error->code;
        echo "<br>\r\nメッセージ：".$error->message;
    }
    goto end;
}

// 検索結果数の確認
if(!isset($api_result->statuses) || empty($api_result->statuses)) {
    echo "検索結果が0件でした。";
    goto end;
}

$tweets = $api_result->statuses;

/*------------　API実行結果のインスタンス化　------------*/
$mutters = array();
$i = (int)0;

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);

    $oldest = $tmp;
    $originalId = $tmp->originalId();

    if(isset($mutters[$originalId])) {
        if($tmp->account()->id()==$mutters[$originalId]->account()->id()) {
            $mutters[$originalId]=$tmp;
        } else {
            continue;
        }
    } else if($media_only=='false') {
        $mutters[$originalId] = $tmp;
        $i++;
    } else if ($tmp->hasMedia() && !isset($mutters[$originalId])) {
        $mutters[$originalId] = $tmp;
        $i++;
    }

    if($i>$min_count)
        break;
}
/*-------------------------------------------------*/


usort($mutters, "sort_mutter_object");

foreach ($mutters as $mutter) {
    if($mutter->hasMedia() && !$mutter->sensitive() && !$mutter->isVideo()) {
        setPageImages($query, $mutter->getRawURL());
        break;
    }
}

// 新しいツイートが取得できているかどうかのチェック
if($param->getValue('max_id') === $oldest->id) {
    echo "最後のツイートまで到達しました。";
    goto end;
}

/*-------------------- 出力処理 --------------------*/
end:

$stdout = ob_get_contents();
ob_end_clean();

if(!empty($stdout)) {
//     $stdout .= "<br>\r\n実行API：".$api;
    $response = gerErrorResponse("twitter", $stdout);
    echo json_encode($response);
} else {
    $response = getResponse($mutters, $oldest);
    echo json_encode($response);
}
/*-------------------------------------------------*/