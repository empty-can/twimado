<?php
require_once ("init.php");


$smarty_path = 'sample/';

$api = 'users/show'; // アクセスするAPI

/*------------ パラメータの取得設定 ------------*/
$param = new Parameters();
$param->constructFromGetParameters();
$param->required = ['screen_name'];
$param->setInitialValue('include_entities', 'true');

$screen_name = $param->getValue('screen_name');
$account = '';
$passenger_id = '';
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
$result = getTwitterConnection($tokens->token, $tokens->secret)
                ->get($api, $param->parameters);

// APIアクセスのエラー確認
if (isset($result->errors)) {
    echo "APIの実行に失敗しました。";
    foreach ($result->errors as $error) {
        echo "<br>\r\nエラーコード：".$error->code;
        echo "<br>\r\nメッセージ：".$error->message;
    }
    goto end;
}
end:

$smarty->assign('account', $result);


// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", $result->name.'さんのプロファイル');

$csss=array();
$csss[] = "sample/profile";
$csss[] = "matome/kuragebunch";
$smarty->assign("csss", $csss);

$jss=array();
$jss[] = "ofi.min";
$smarty->assign("jss", $jss);

$embedded_js_params_string = $param->parameters;

$embedded_js_params_int = [
];

$embedded_js_string = [
];
$embedded_js_int = [
];

$smarty->assign("embedded_js_params", $embedded_js_params_string);
$smarty->assign("embedded_js", $embedded_js_params_int);
// $smarty->assign("embedded_js_params", build_embededd_js_params($embedded_js_params_string, $embedded_js_params_int));
// $smarty->assign("embedded_js", build_embededd_js($embedded_js_string, $embedded_js_int));

// テンプレートを表示する
$smarty->display($smarty_path.$screen_name.'.tpl');


// myVarDump($result);