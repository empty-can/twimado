<?php
require_once ("init.php");

$api = 'statuses/lookup'; // アクセスするAPI

/*------------ パラメータの取得設定 ------------*/
$param = new Parameters();

if(isPost()) {
	$param->constructFromPostParameters();
} else {
	$param->constructFromGetParameters();
}

$param->required = ["ids"];

$param->setInitialValue('count', '100');
$param->setInitialValue('account', '');
$param->setInitialValue('id', '');
$min_count = $param->getValue('count');

$account = $param->putValue('account');
$passenger_id = $param->putValue('id');
$media_only = $param->putValue('mo');
/*-----------------------------------------*/

// 標準出力の監視開始
//ob_start();

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

$param->moveValue('ids', 'id');

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
$latest = null;
$i = (int)0;
$originalId = PHP_INT_MAX;
$latestId = 1;
// $new_media_tweets = array();

foreach ($tweets as $tweet) {
    $tmp = new Tweet($tweet);

    if($originalId > $tmp->originalId()) {
        $originalId = $tmp->originalId();
        $oldest = $tmp;
    }

    if($tmp->originalId() > $latestId) {
        $latestId = $tmp->originalId();
        $latest = $tmp;
    }

//     if($tmp->isNotMediaInfo()) {
//         $new_media_tweets[] = $tmp;
//     }

    if(isset($mutters[$tmp->originalId])) {
        if($tmp->account()->id()==$mutters[$originalId]->account()->id()) {
            $mutters[$originalId]=$tmp;
        } else {
            continue;
        }
    } else if($media_only=='false') {
        $mutters[$tmp->originalId] = $tmp;
        $i++;
    } else if ($tmp->hasMedia() && !isset($mutters[$tmp->originalId])) {
        $mutters[$tmp->originalId] = $tmp;
        $i++;
    }

    if($i>$min_count)
        break;
}
/*-------------------------------------------------*/
// myVarDump($mutters);
// Tweet::insertMediaTable($new_media_tweets);

// 新しいツイートが取得できているかどうかのチェック
if($param->getValue('max_id') === $oldest->originalId) {
    echo "最後のツイートまで到達しました。";
    goto end;
} else if($originalId == PHP_INT_MAX) {
    echo "ツイートがありませんでした。";
    goto end;
}


/*-------------------- 出力処理 --------------------*/
end:

$stdout = ob_get_contents();
$stdout = "";
ob_end_clean();

if(!empty($stdout)) {
//     $stdout .= "<br>\r\n実行API：".$api;
    $response = gerErrorResponse("twitter", $stdout);
} else {
    $response = getResponse($mutters, $oldest, $latest);
    $response['twitter_oldest_id'] = $originalId;
}
// myVarDump($response);
echo json_encode($response);
/*-------------------------------------------------*/