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

<meta content="//www.suki.pics/favicon.png" itemprop="image">
<link rel="shortcut icon" href="//www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="//www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="apple-touch-icon" href="//www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<title>lookupトップページ</title>
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/common.css" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top.css" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top_m.css" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top_pc.css" />
</head>
<body>
<div class="flx fww jcsa" style="margin: 5vh auto 5vh auto;">
<?php
foreach ($users as $user) {
    ?>
    <div style="width:256px; margin: 10px auto;">
    	<div class="flx fww jcsa aife">
    		<div style="width:50px;">
    			<img alt="<?php echo htmlentities($user->name);?>" src="<?php echo $user->profile_image_url_https;?>">
			</div>
    		<div class="ellip" style="width:200px;vertical-align:bottom;">
				<a href="//www.suki.pics/timeline/user.php?domain=twitter&target_id=<?php echo $user->id;?>&name=<?php echo htmlentities($user->name);?>" target="_blank">
					<?php echo htmlentities($user->name);?>
					<br>
					@<?php echo $user->screen_name;?>
				</a>
			</div>
		</div>
		<div style="margin: 10px auto;">
    		<a href="//www.suki.pics/timeline/lookup.php?domain=twitter&target_id=<?php echo $user->id;?>&name=<?php echo htmlentities($user->name);?>&asc=0" target="_blank">
    			最新から
    		</a>
    		　
    		<a href="//www.suki.pics/timeline/lookup.php?domain=twitter&target_id=<?php echo $user->id;?>&name=<?php echo htmlentities($user->name);?>&asc=1" target="_blank">
    			過去から
    		</a>
		</div>
		<a href="//www.suki.pics/matome/matome.php?domain=twitter&target_id=<?php echo $user->id;?>&name=<?php echo htmlentities($user->name);?>&target_domain=twitter&asc=0&edit=true" target="_blank">
			まとめ用ページ（最新から）
		</a>
		<br>
		<a href="//www.suki.pics/matome/matome.php?domain=twitter&target_id=<?php echo $user->id;?>&name=<?php echo htmlentities($user->name);?>&target_domain=twitter&asc=1&edit=true" target="_blank">
			まとめ用ページ（過去から）
		</a>
		<br>
		<a href="//www.suki.pics/orenoyome/create_matome.php?target_id=<?php echo $user->id;?>&domain=twitter&name=<?php echo htmlentities($user->name);?>" target="_blank">
			まとめを追加
		</a>
		<br>
		<div style="max-height:64px;overflow:hidden;"><?php echo htmlentities($user->description); ?></div>
	</div>
    <?php
}
?>
</div>
</body>
</html>