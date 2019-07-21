<?php
require_once ("init.php");

$api = "api/v1/timelines/public"; // アクセスするAPI

/*------------ パラメータの取得設定 ------------*/
$param = new Parameters();
$param->constructFromPostParameters();
$param->required = array();
$param->optional = ["local", "only_media", "max_id", "since_id", "limit"];

$param->copyValue('count', 'limit');
$min_count = $param->putValue('count');
$param->setInitialValue('limit', '40');
$param->setParam('local', 'true');
// $param->moveValue('mo', 'only_media');

$account = $param->putValue('account');
$passenger_id = $param->putValue('id');

$media_only = $param->getValue('mo');
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
$tokens = getPawooTokens($account, $passenger_id, true);

if($tokens->isEmpty()) {
    echo "認証情報が取得できませんでした。";
    goto end;
}

// APIアクセス
$toots = getMastodonConnection(PawooDomain, $tokens->token)
                ->executeGetAPI($api.'?'.http_build_query($param->parameters));

// 検索結果数の確認
if(empty($toots)) {
    echo "該当トゥートが0件でした。";
    goto end;
}

/*------------　API実行結果のインスタンス化　------------*/
$mutters = array();
$oldest = new EmptyMutter("pawoo");
$i = (int)0;

foreach ($toots as $toot) {
    $tmp = new Pawoo($toot);

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


// 新しいトゥートが取得できているかどうかのチェック
if($param->getValue('max_id') === $oldest->id) {
    echo "最後のトゥートまで到達しました。";
    goto end;
}

/*-------------------- 出力処理 --------------------*/
end:

$stdout = ob_get_contents();
ob_end_clean();

if(!empty($stdout)) {
    //     $stdout .= "<br>\r\n実行API：".$api;
    $response = gerErrorResponse("pawoo", $stdout);
    echo json_encode($response);
} else {
    $response = getResponse($mutters, $oldest);
    echo json_encode($response);
}
/*-------------------------------------------------*/