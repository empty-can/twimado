<?php
require_once ("init.php");

$users = getSessionParam('users');

if(empty($users)) {
    $user_ids = "";
    $mydb = new MyDB();

    $sql = "SELECT DISTINCT user_id FROM mutter ORDER BY user_id ASC";

    $results = $mydb->select($sql);

    foreach ($results as $row) {
        $user_ids .= $row['user_id'].',';
    }

    $mydb->close();

    if(!empty($user_ids)) {
        $user_ids = substr($user_ids, 0, -1);
    }
}

if(!empty($user_ids)) {
    $api = 'users/lookup'; // アクセスするAPI

    $param = new Parameters();
    $param->setParam('user_id', $user_ids);

    $account = Account;

    // アクセストークンの取得
    $tokens = getTwitterTokens($account, "", true);

    if($tokens->isEmpty()) {
        echo "認証情報が取得できませんでした。";
    }

    // APIアクセス
    $users = getTwitterConnection($tokens->token, $tokens->secret)
                    ->get($api, $param->parameters);

        // APIアクセスのエラー確認
    if (isset($users->errors)) {
        echo "APIの実行に失敗しました。";
        foreach ($users->errors as $error) {
            echo "<br>\r\nエラーコード：" . $error->code;
            echo "<br>\r\nメッセージ：" . $error->message;
        }
    }

    foreach ($users as $user) {
        $count = existCreator($user->id_str, 'twitter');

        if($count==0) {
            insertCreator($user->id_str, 'twitter', $user->screen_name, $user->name);
        }
    }

    // 検索結果数の確認
    if (empty($users)) {
        echo "該当が0件でした。";
        echo var_dump($param->parameters);
    }

    setServerParam('users', $users);
}

?>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<meta content="http://www.suki.pics/favicon.png" itemprop="image">
<link rel="shortcut icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="apple-touch-icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<title>lookupトップページ</title>
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/common.css?2019-08-08_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top.css?2019-08-08_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_m.css?2019-08-08_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_pc.css?2019-08-08_14:04:14" />
</head>
<body>
<?php
foreach ($users as $user) {
    ?>
	<a href="http://www.suki.pics/timeline/lookup.php?target_id=<?php echo $user->id;?>&name=<?php echo $user->name;?>" target="_blank">
		<img alt="<?php echo $user->name;?>" src="<?php echo $user->profile_image_url_https;?>">
		<?php echo $user->name;?>@<?php echo $user->screen_name;?>
	</a>
	　
	<a href="http://www.suki.pics/matome/matome.php?target_id=<?php echo $user->id;?>&name=<?php echo $user->name;?>&target_domain=twitter" target="_blank">
		まとめ用ページ
	</a>
	<br>
	<?php echo $user->description;?>
	<hr>
    <?php
}
?>
</body>
</html>