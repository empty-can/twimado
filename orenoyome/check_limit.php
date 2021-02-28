<?php
require_once ("init.php");

// myVarDump(TwitterAccountID);
// myVarDump($_SESSION);
// myVarDump($account);

// アクセストークンの取得
$tokens = getTwitterTokens(Account, (string)TwitterAccountID, true);

// myVarDump($tokens);

if ($tokens->isEmpty()) {
    echo "認証情報が取得できませんでした。";
    exit();
} else {

    $api = 'account/settings'; // アクセスするAPI

    $param = new Parameters();

    // APIアクセス
    $account = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);


    $screen_name = $account->screen_name;

    $api = 'users/show'; // アクセスするAPI

    $param = new Parameters();
    $param->setParam('screen_name', $screen_name);

    // APIアクセス
    $profile = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);

    $profile_image_url_https = $profile->profile_image_url_https;

    $api = 'application/rate_limit_status'; // アクセスするAPI

    $param = new Parameters();
    $param->setParam('resources', 'application,statuses,search,lists,users,account,collections,collections,friends,favorites,trends,geo');

    // APIアクセス
    $result = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);

    $result_array = obj_to_array($result)['resources'];

    $rates = array();
    foreach ($result_array as $resource) {
        $keys = array_keys($resource);
        foreach ($keys as $key) {
            $rates[] = [
                'url' => $key,
                'limit' => $resource[$key]['limit'],
                'remaining' => $resource[$key]['remaining'],
                'diff' => ($resource[$key]['limit'] - $resource[$key]['remaining']),
                'reset' => date('Y/m/d H:i:s', ($resource[$key]['reset']))
            ];
        }
    }

    function cmp($a, $b)
    {
        if ($a['diff'] == $b['diff']) {
            return ($a['url'] > $b['url']) ? - 1 : 1;
        }
        return ($a['diff'] > $b['diff']) ? - 1 : 1;
    }

    usort($rates, "cmp");

    // myVarDump($account);
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
<title>レートチェックページ</title>
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/common.css?2019-08-08_14:04:14" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top.css?2019-08-08_14:04:14" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top_m.css?2019-08-08_14:04:14" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top_pc.css?2019-08-08_14:04:14" />
</head>
<body>
	ログインユーザ：
	<br>
	<img src="<?php echo $profile->profile_image_url_https;?>" style="width: 24px;" class="circle">
<?php echo $profile->name;?>@<?php echo $profile->screen_name;?>
<br>
	<br>
	<table>
		<tr>
			<th>url</th>
			<th>diff</th>
			<th>limit</th>
			<th>remaining</th>
			<th>reset</th>
		</tr>
<?php
foreach ($rates as $rate) {
    ?>
<tr>
			<td><?php echo $rate['url'];?></td>
			<td><?php
    if ($rate['diff'] > 0) {
        echo '<span style="color:red;font-weight:bold;">' . $rate['diff'] . '</span>';
    } else {
        echo "0";
    }
    ?></td>
			<td><?php echo $rate['limit'];?></td>
			<td><?php echo $rate['remaining'];?></td>
			<td><?php echo $rate['reset'];?></td>
		</tr>
<?php
}
?>
</table>
</body>
</html>