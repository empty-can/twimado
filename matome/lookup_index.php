<?php
require_once ("init.php");

$users = getSessionParam('users');

if(empty($users)) {
    $mydb = new MyDB();

    $sql = "SELECT c.id, c.domain, c.screen_name, c.name, c.profile_image, c.followers_count, n.follow_date, n.crawled FROM creator AS c, new_creator AS n WHERE c.id = n.id AND n.crawled=1 ORDER BY followers_count DESC";

    $users = $mydb->select($sql);
    
    // myVarDump($users);

    $mydb->close();
//     echo '$user_names = array(<br>';
//     foreach ($users as $user) {
//         echo ', "'.$user['screen_name'].'"<br>';
//     }
//     echo ');<br>';
//     exit();
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
$user_name = preg_replace('/&＃/', '&#', $user['name']);
// $user_name = $user['name'];
// myVarDump($user_name);
    ?>
    <div style="width:256px; margin: 10px auto;">
    	<div class="flx fww jcsa aife">
    		<div style="width:50px;">
    			<img alt="<?php echo $user_name;?>" src="<?php echo $user['profile_image'];?>">
			</div>
    		<div class="ellip" style="width:200px;vertical-align:bottom;">
				<a href="//www.suki.pics/timeline/user.php?domain=twitter&target_id=<?php echo $user['id'];?>&name=<?php echo $user_name;?>" target="_blank">
					<?php echo $user_name;?>
					<br>
					@<?php echo $user['screen_name'];?>
				</a>
			</div>
		</div>
		<div style="margin: 10px auto;">
    		<a href="//www.suki.pics/timeline/lookup.php?domain=twitter&target_id=<?php echo $user['id'];?>&name=<?php echo $user_name;?>&asc=0&hs=false&mo=false" target="_blank">
    			最新から
    		</a>
    		　
    		<a href="//www.suki.pics/timeline/lookup.php?domain=twitter&target_id=<?php echo $user['id'];?>&name=<?php echo $user_name;?>&asc=1&hs=false&mo=false" target="_blank">
    			過去から
    		</a>
		</div>
		<a href="//www.suki.pics/matome/matome.php?domain=twitter&target_id=<?php echo $user['id'];?>&name=<?php echo $user_name;?>&target_domain=twitter&asc=0&hs=false&edit=true" target="_blank">
			まとめ用ページ（最新から）
		</a>
		<br>
		<a href="//www.suki.pics/matome/matome.php?domain=twitter&target_id=<?php echo $user['id'];?>&name=<?php echo $user_name;?>&target_domain=twitter&asc=1&hs=false&edit=true" target="_blank">
			まとめ用ページ（過去から）
		</a>
		<br>
		<a href="//www.suki.pics/orenoyome/create_matome.php?target_id=<?php echo $user['id'];?>&domain=twitter&name=<?php echo $user_name;?>" target="_blank">
			まとめを追加
		</a>
		<br>
		<!-- div style="max-height:64px;overflow:hidden;"><?php //echo htmlentities($user['description']); ?></div -->
	</div>
    <?php
}
?>
</div>
</body>
</html>