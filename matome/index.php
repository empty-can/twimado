<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('sc', '');
$param->setInitialValue('id', '');
$param->setInitialValue('domain', 'pawoo');
$param->setInitialValue('count', '20');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);

$sc = $param->getValue('sc', '');
$id = $param->getValue('id', '');

$param = new Parameters();

if(!empty($sc)) {
    $param->setParam('screen_name', $sc);
} else if(!empty($id)) {
    $param->setParam('user_id', $id);
}

$api = 'users/show'; // アクセスするAPI

$account = Account;

$tokens = getTwitterTokens($account, "", true);
if ($tokens->isEmpty()) {
    echo "認証情報が取得できませんでした。";
}

// APIアクセス
$user = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);


if(empty($id)) {
    $id = $user->id;
}

$mydb = new MyDB();

$sql = "SELECT * FROM matome WHERE user_id='$id' ORDER BY id ASC";

$matomes = $mydb->select($sql);

$mydb->close();


?>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<meta content="http://www.suki.pics/favicon.png" itemprop="image">
<link rel="shortcut icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="apple-touch-icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<title><?php echo $user->name;?></title>
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/common.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_m.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_pc.css?2019-08-04_14:04:14" />
</head>
<body>
<br>
	    	<div class="banner">
	    		<img src="<?php echo $user->profile_banner_url;?>">
	    	</div>
	    	<br>
	    	<div class="icon left">
	    		<img src="<?php echo str_replace('normal.jpg', '200x200.jpg', $user->profile_image_url_https);?>" style="width:128px">
	    	</div>
	    	<div class="profile left">
	    		<div class="account">
	    			<div class="name">
	    				<?php echo $user->name;?>
		    			<span class="account_name">@<?php echo $user->screen_name;?></span>
	    			</div>
	    		</div>
	    		<div class="text">
	    			<?php echo $user->description;?>
	    		</div>
	    	<div class="clear"></div>
	    	<br>
	    	<?php
	    	foreach($matomes as $matome) {
	    	    ?>
	    	    ・<a href="http://www.suki.pics/matome/timeline.php?matome_id=<?php echo $matome['id'];?>"><?php echo $matome['title'];?></a><br>
	    		<?php
	    	}
	    	?>
	    </div>
</body>
</html>