<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$domain = $param->putValue('domain');
$method = $param->putValue('method');
$id = $param->getValue('id');

$result=["未実行"=>"0"];
$response = array();

// pawooの自分TL取得
if (contains($domain, 'pawoo')) {
    // アクセストークンの取得
    $tokens = getSessionParam('pawooAccessToken', "");
    if (! isset($tokens) || $tokens->isEmpty()) {
        $response['error'] = "認証情報が取得できませんでした。\r\n操作にはログインまたはPawooとの連携が必要です。";
        goto end;
    }

    if ($method == 'do') {
        $api = "api/v1/statuses/$id/favourite";
    } else if ($method == 'undo') {
        $api = "api/v1/statuses/$id/unfavourite";
    }

    if (! empty($api)) {
        $connection = getMastodonConnection(PawooDomain, $tokens->access_token);
        $result = $connection->executePostAPI($api);
    }
    
    if (empty($result) || !$result) {
        $response['error'] = "APIの実行に失敗しました。";
        goto end;
    } else if (isset($result['response']) && empty($result['response'])) {
        $response['error'] = "APIの実行に失敗しました。";
        $response['error'] .= "\r\nエラータイプ：" . $result['error_get_last']['type'];
        $response['error'] .= "\r\nメッセージ：" . $result['error_get_last']['message'];
        goto end;
    }
}

// 自分のイラストリストTL取得
if (contains($domain, 'twitter')) {
    
    // アクセストークンの取得
    $tokens = getSessionParam('twitterAccessToken', "");
    
    if($tokens->isEmpty()) {
        $response['error'] = "認証情報が取得できませんでした。\r\n操作にはログインまたはTwitterとの連携が必要です。";
        goto end;
    }
    
    if ($method == 'do') {
        $api = 'favorites/create';
    } else if ($method == 'undo') {
        $api = 'favorites/destroy';
    }
    
    if(!empty($api)) {
        $result = getTwitterConnection($tokens->access_token, $tokens->access_token_secret)->post($api, $param->parameters);
    }
    
    if(empty($result)) {
        $response['error'] = "APIの実行に失敗しました。";
        goto end;
    } else if(isset($result->errors)) {
        $response['error'] = "APIの実行に失敗しました。";
        foreach ($result->errors as $error) {
            $response['error'] .= "\r\nエラーコード：".$error->code;
            $response['error'] .= "\r\nメッセージ：".$error->message;
        }
        goto end;
    }
}


/*-------------------- 出力処理 --------------------*/
end:

if(!empty($response['error'])) {
    echo json_encode($response);
} else {
    echo json_encode($result);
}
/*-------------------------------------------------*/