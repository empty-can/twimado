<?php
require_once ("init.php");

$target_id = getGetParam('target_id', '');
$domain = getGetParam('domain', '');
$submit_data = getPostParam('matome_name', '');

if(!empty($submit_data)) {
    if(createMatome($target_id, $domain, $submit_data)==1) {
        echo $submit_data+" の登録に成功しました。";
    } else {
        echo $submit_data+" の登録に失敗しました。";
    }
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
<title>まとめ登録</title>
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/common.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_m.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_pc.css?2019-08-04_14:04:14" />
</head>
<body>
	<form action="./create_matome.php?target_id=<?php echo $target_id;?>&domain=<?php echo $domain;?>" method="POST">
		<input type="text" name="matome_name" value="">
  		<button name="submit" value="true">送信</button>
	</form>
</body>
</html>