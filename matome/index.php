<?php
require_once ("init.php");

/**
 *
 * @return array
 */
function getMatomes() {

    $mydb = new MyDB();

    // $sql = "SELECT matome.id AS matome_id, matome.user_id AS user_id, matome.title AS title, matome.description AS description, mutter.id AS mutter_id, mutter.domain AS domain, mutter.created_at AS created_at FROM mutter, mvsm, matome WHERE matome.private = 0 AND matome.id = mvsm.matome_id AND mutter.id = mvsm.mutter_id AND NOT EXISTS (SELECT 1 FROM mutter AS mu, mvsm AS mv, matome AS ma WHERE matome.id = mv.matome_id AND mu.id = mv.mutter_id AND mutter.created_at < mu.created_at);";
    $sql = "SELECT * FROM matome WHERE matome.private = 0 ORDER BY user_id, id;";

    $results = $mydb->select($sql);

    $matomes = array();
    foreach ($results as $row) {
        $matomes[$row['user_id'].$row['id']] = $row;
    }

    $mydb->close();

    return $matomes;
}

/**
 *
 * @param string $matomes
 * @return string
 */
function getTweetIds($matomes) {

    $tweet_ids = array();

    foreach ($matomes as $matome) {
        $tweet_ids[] = $matome['latest_mutter_id'];
    }

    return implode(',', $tweet_ids);
}

/**
 *
 * @param string $tweet_ids
 * @return array[]|object[]
 */
function getMatomeTopTweets($tweet_ids = "") {
    $api = 'statuses/lookup'; // アクセスするAPI

    $param = new Parameters();
    $param->setParam('id', $tweet_ids);

    $account = Account;

    $tokens = getTwitterTokens($account, "", true);
    if ($tokens->isEmpty()) {
        echo "認証情報が取得できませんでした。";
    }

    // APIアクセス
    $result = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);
    $tweets = array();
    foreach ($result as $tweet) {
        $tweets[$tweet->id_str] = $tweet;
    }

    return $tweets;
}

/**
 *
 * @param array $tweets
 * @return array
 */
function getCreators(array $tweets) {

    $creators = array();
    foreach ($tweets as $tweet) {
        $creators[$tweet->user->id_str] = $tweet->user;
    }

    return $creators;
}

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'pawoo');
$param->setInitialValue('count', '20');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);

// $target_id = $param->getValue('target_id', '');
// if (empty($users)) {
// }

$matomes = getMatomes();
$tweet_ids = getTweetIds($matomes);
$tweets = getMatomeTopTweets($tweet_ids);
$creators = getCreators($tweets);
// myVarDump($matomes);

?>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<meta content="//www.suki.pics/favicon.png" itemprop="image">
<link rel="shortcut icon" href="//www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="//www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="apple-touch-icon" href="//www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<title>まとめトップ</title>
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/common.css" />
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/matome/kuragebunch.css" />
<style type="text/css">
<!--
h4 {
 overflow: hidden;
 white-space: nowrap;
 text-overflow:ellipsis;
}

.latest-update-list {
    margin: auto;
}
-->
</style>
</head>
<body>
	<div class="top-contents"  style="margin-top: 2vh;margin-bottom: 10vh;">
	<?php
	$before_creator = null;
	foreach ($matomes as $matome) {
	    $creator = $creators[$matome['user_id']];
	    
	    if(empty($before_creator)) {
	?>
<div class="flx fww jcfs">
	<?php
	    }
	    
	    if($before_creator !== $creator) {
	?>
	</div>
<div class="flx fww jcfs">
	<?php
	    }
		$before_creator = $creator;
	    
	    $tweet = $tweets[$matome['latest_mutter_id']];
?>
<!--
<?php // var_dump($tweet);?>
<?php // var_dump($creators[$matome['user_id']]);?>
 -->
<div style="width:256px; margin:2vh 1vw;">
	<h3 style="margin-top:5px;overflow: hidden;white-space: nowrap;text-overflow:ellipsis;"><?php echo $matome['title'];?></h3>
	<img style="height:48px; border-radius: 50% 50% 50% 50%; border: solid 1px black;"
		alt="<?php echo $creator->name;?>"
		src="<?php echo $creator->profile_image_url_https;?>">
	<span style="font-size:15px;font-weight:bold; color:rgb(20, 23, 26);"><?php echo $creator->name;?></span>
	<span style="font-size:12px;font-weight:400; color:rgb(101, 119, 134); line-height:1.3125;">@<?php echo $creator->screen_name;?></span>
	<div style="width;100%;text-align:right;font-size:12px;font-weight:400; color:rgb(101, 119, 134); line-height:1.3125;"><?php echo date('Y-m-d H:i:s', strtotime($tweet->created_at));?></div>
	<div style="width;100%;height:64px;":><?php echo $tweet->text;?></div>
	<a href="//www.suki.pics/matome/timeline.php?user_id=<?php echo $matome['user_id'];?>&matome_id=<?php echo $matome['id'];?>&asc=0">
	<img style="width:256px; height:256px; object-fit:cover; object-position: center top;box-shadow: 10px 10px 10px rgba(0,0,0,0.4);"
		alt="<?php echo $matome['title'];?>"
		src="<?php echo $tweet->entities->media[0]->media_url_https;?>">
	</a>
</div>
<?php
	    
	    if($before_creator !== $creator) {
	?>
</div>
	<?php
	    }
	}
	?>
</body>
</html>