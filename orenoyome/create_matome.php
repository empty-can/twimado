<?php
require_once ("init.php");

$target_id = getGetParam('target_id', '');
$domain = getGetParam('domain', '');
$name = getGetParam('name', '');
$matome_title = getPostParam('matome_title', '');
$matome_title_short = getPostParam('matome_title_short', '');
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
<?php
if(!empty($matome_title) && !empty($matome_title_short)) {
    if(createMatome($target_id, $domain, $matome_title, $matome_title_short)==1) {
        echo "$name さんの $submit_data の登録に成功しました。";
    } else {
        echo "$name さんの  $submit_data の登録に失敗しました。";
    }
}
?>
	<form action="./create_matome.php?target_id=<?php echo $target_id;?>&name=<?php echo $name;?>&domain=<?php echo $domain;?>" method="POST">
		まとめ名：<input type="text" name="matome_title" value=""><br>
		まとめ略称：<input type="text" name="matome_title_short" value=""><br>
  		<button name="submit" value="true">送信</button>
	</form>
</body>
</html>