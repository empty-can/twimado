<?php
require_once ("init.php");

$api = 'lists/statuses'; // アクセスするAPI

/*------------ パラメータの取得設定 ------------*/
$param = new Parameters();

if(isPost()) {
	$param->constructFromPostParameters();
} else {
	$param->constructFromGetParameters();
}

$param->required = ["list_id"];
$param->optional = ["since_id", "max_id", "count"];

$param->setInitialValue('count', '200');
$param->setInitialValue('include_rts', 'false');
$min_count = $param->getValue('count');

$account = $param->putValue('account');
$passenger_id = $param->putValue('id');
$media_only = $param->putValue('mo');
/*-----------------------------------------*/


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
$tweets = getTwitterConnection($tokens->token, $tokens->secret)
                ->get($api, $param->parameters);

// APIアクセスのエラー確認
if (isset($tweets->errors)) {
    echo "APIの実行に失敗しました。";
    foreach ($tweets->errors as $error) {
        echo "<br>\r\nエラーコード：".$error->code;
        echo "<br>\r\nメッセージ：".$error->message;
    }
    goto end;
}

// 検索結果数の確認
if(empty($tweets)) {
    echo "該当ツイートが0件でした。";
    echo var_dump($param->parameters);
    goto end;
}

/*------------　API実行結果のインスタンス化　------------*/
$mutters = array();
$oldest = new EmptyMutter("twitter");
$i = (int)0;

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);

    $oldest = $tmp;
    $originalId = $tmp->originalId();

    if(isset($mutters[$originalId])) {
        continue;
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

// 新しいツイートが取得できているかどうかのチェック
if($param->getValue('max_id') === $oldest->originalId) {
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