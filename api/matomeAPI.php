<?php
require_once ("init.php");

use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

$param = new Parameters();
$param->constructFromGetParameters();

$tweet_id = $param->parameters['tweet_id'];
$user_id = $param->parameters['user_id'];
$domain = $param->parameters['domain'];
$action = $param->parameters['action'];
$matome = $param->parameters['matome'];
error_reporting(-1);

$result="";

if ($action == 'reg') {
    if (isset($matome) && $matome!='' && ! empty($tweet_id)) {
        // アクセストークンの取得
        $tokens = getTwitterTokens();

        if ($tokens->isEmpty()) {
            echo "認証情報が取得できませんでした。";
        }

        // APIアクセス
        $param = array(
            "id" => "$tweet_id"
        );
        $tweet = getTwitterConnection($tokens->token, $tokens->secret)->get('statuses/show', $param);

        // APIアクセスのエラー確認
        if (isset($tweet->errors)) {
            echo "APIの実行に失敗しました。";
            foreach ($tweet->errors as $error) {
                echo "<br>\r\nエラーコード：" . $error->code;
                echo "<br>\r\nメッセージ：" . $error->message;
            }
        }

        $hasher = new ImageHash(new DifferenceHash());
        $canRegist = false;
        $media = (new Tweet($tweet))->media;
        foreach ($media as $medium) {
            $hash = $hasher->hash($medium->thumb)->toHex();
            // echo $hash."<br>\r\n";
            if(!isSameMedia($matome, $user_id, $hash)) {
                $canRegist |= true;
            }
        }
        // echo $canRegist."<br>";
        // exit();

        if($canRegist) {
            $result = regMatome($tweet_id, $domain, $user_id, $matome);
        } else {
            $result = regMatome($tweet_id, $domain, $user_id, $matome);
        }
    }
    if (contains($domain, 'twitter')) {
        $collection_ids = getTwitterCollection($user_id, $matome);
        addTwitterCollection($tweet_id, $collection_ids);
    }
} else if ($action == 'del') {
    if (!empty($matome) && !empty($tweet_id)) {
        $result = delMatome($tweet_id, $domain, $user_id, $matome);
    }
    if(contains($domain, 'twitter')) {
        $collection_ids = getTwitterCollection($user_id, $matome);
        removeTwitterCollection($tweet_id, $collection_ids);
    }
}
// myVarDump($param->parameters);

$response = array();
$response['result'] = $result;
echo json_encode($response);