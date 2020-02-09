<?php
require_once ("init.php");

$target_matome = getGetParam('target_matome', '');
$domain = getGetParam('domain', '');
$submit_data = getPostParam('tweets', '');

$matomeInfo = getMatomeInfo($target_matome);

echo $matomeInfo[title];

if(!empty($submit_data)) {
    $mydb = new MyDB();

    $URLs = str_replace(array("\r\n","\r","\n"), "\n", $submit_data);
    $URLs = explode("\n", $URLs);

    $target_matome = $mydb->escape($target_matome);
    $domain = $mydb->escape($domain);

    foreach ($URLs as $URL) {
        $tweet_id = explode("/status/", $URL)[1];
        var_dump($tweet_id);

        $tweet_id = $mydb->escape($tweet_id);
// //         var_dump($decoded_json);

//         if(!empty($decoded_json->created_at_date)) {
//             $created_at = str_replace('/', '-', $decoded_json->created_at_date);
//         } else if(!empty($decoded_json->created_at)) {
//             $created_at = date("Y-m-d H:i:s", $decoded_json->created_at);
//         } else if(!empty($decoded_json->time)) {
//             $created_at = date("Y-m-d H:i:s", strtotime($decoded_json->time));
//         }
// //         var_dump($created_at);

        $sql = "INSERT INTO mvsm (mutter_id, mutter_domain, matome_id) VALUE ($tweet_id, '$domain', $target_matome);";

        $results = $mydb->insert($sql);
        var_dump($results);
    }

    $mydb->close();
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
<title>まとめ登録</title>
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/common.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top_m.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/top_pc.css?2019-08-04_14:04:14" />
</head>
<body>
	<form action="./regist_matome.php?target_matome=<?php echo $target_matome;?>&domain=<?php echo $domain;?>" method="POST">
		<textarea name="tweets" rows="25" cols="200"><?php echo $submit_data;?></textarea>
  		<button name="submit" value="true">送信</button>
	</form>
</body>
</html>