<?php
require_once ("init.php");

$woeid = "1118370"; // Tokyo
$api = 'trends/place';

$target = "_blank";

$params = array(
    "id" => $woeid
);

$trends = getTwitterConnection("", "")->get($api, $params);

$userInfo = getSessionParam("twitter_user_info", "");
// myVarDump($userInfo);

$twitterLogin = (!empty(getSessionParam("twitter_access_token", ""))
    && !empty(getSessionParam("twitter_access_token_secret", "")));

$lists = getSessionParam("twitter_mylists", "");
if($twitterLogin) {
    $api = 'lists/list';
    $params = array(
        "screen_name" => $userInfo->screen_name
    );
    setSessionParam("twitter_id", $userInfo->id);
    
    $twitter_access_token = getSessionParam("twitter_access_token", "");
    $twitter_access_token_secret = getSessionParam("twitter_access_token_secret", "");
    //  echo "twitter_access_token:".$twitter_access_token."<br>\r\n";
    //  echo "twitter_access_token_secret:".$twitter_access_token_secret."<br>\r\n";
    
    setTokens($userInfo->id, $userInfo->name."@".$userInfo->screen_name, $twitter_access_token, $twitter_access_token_secret);
    
    if(empty($lists)) {
        $lists = getTwitterConnection($twitter_access_token, $twitter_access_token_secret)->get($api, $params);
        setSessionParam("twitter_mylists", $lists);
    }
}

$pawooLogin = false;
$pawooAccessToken = getSessionParam("pawoo_access_token", "");
$pawooAccount = getSessionParam("pawoo_account", "");

if(!empty($pawooAccessToken)) {
    if(empty($pawooAccount)) {
        $connection = getMastodonConnection(PawooDomain, $pawooAccessToken);
        $pawooAccount = $connection->executeGetAPI('api/v1/accounts/verify_credentials');
        setSessionParam("pawoo_account", $pawooAccount);
        setSessionParam("pawoo_id", $pawooAccount["id"]);
    }
    
    $pawooLogin = true;
}
if($pawooLogin) {
    setTokens($pawooAccount["id"], $pawooAccount["display_name"]."@".$pawooAccount["username"], $pawooAccessToken, "");
}

?>
<html>
  <head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<!--[if IE]>
	    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<title>ツイ窓</title>
    <link rel="stylesheet" type="text/css" href="/twimado/css/common.css?2019-04-14_22:35:35" />
    <link rel="stylesheet" type="text/css" href="/twimado/css/top.css?2019-04-14_22:35:35" />
  </head>
  <body>
  <br>
  <?php 
  if(isset($userInfo->name)) {
      ?>
  <img src="<?php echo $userInfo->profile_image_url_https; ?>" style="width:30px;">:<a href="https://twitter.com/" target="_balnk"><?php echo $userInfo->name; ?></a><br>
  <?php 
  }
  if(!empty($pawooAccessToken)) {
      ?>
  <img src="<?php echo $pawooAccount['avatar']; ?>" style="width:30px;">:<a href="https://pawoo.net/" target="_balnk"><?php echo $pawooAccount['display_name']."@".$pawooAccount['username']; ?></a><br>
  <?php 
  }
  ?>
<h3>タイムライン</h3>
<ul class="breadcrumb">
  <?php if($twitterLogin && $pawooLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/home.php?domain=twitterpawoo&hs=false&thumb=false" target="<?php echo $target;?>"><img src="<?php echo AppURL; ?>/imgs/home_64.svg" style="width:24px;"> ホームTL</a>
  </li>
  <?php } 
  if($twitterLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/home.php?domain=twitter&hs=false&thumb=false" target="<?php echo $target;?>"><img src="<?php echo AppURL; ?>/imgs/home_64.svg" style="width:24px;"> ホームTL(Twitter)</a>
  </li>
  <?php } 
  if($pawooLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/home.php?domain=pawoo&hs=false&thumb=false" target="<?php echo $target;?>"><img src="<?php echo AppURL; ?>/imgs/home_64.svg" style="width:24px;"> ホームTL(Pawoo)</a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/local.php?domain=pawoo&hs=false&thumb=false" target="<?php echo $target;?>">ローカルTL(Pawoo)</a>
  </li>
  <?php } ?>
</ul>
<br>
<ul class="breadcrumb">
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?domain=twitter&hs=false&thumb=false&twitter_list=1120163652441481217" target="<?php echo $target;?>">公式TL（マンガ家）</a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/user.php?domain=twitter&id=2656042465&hs=false&thumb=false" target="<?php echo $target;?>"><img src="https://pbs.twimg.com/profile_images/751972552789020672/1Ml7URFU_normal.jpg" style="width:30px;"> 横島TL🔞 </a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?domain=pawoo&hs=false&thumb=false" target="<?php echo $target;?>">公式Pawoo TL🔞 </a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?hs=false&thumb=false" target="<?php echo $target;?>">Twitter＆Pawoo TL🔞</a>
  </li>
</ul>
<h3>アプリ連携</h3>
<ul class="breadcrumb">
<?php if(!$twitterLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/auth_twitter.php"><img src="<?php echo AppURL; ?>/imgs/link.svg" style="width:24px;"> Twitterと連携する</a>
  </li>
<?php }

    if(empty($pawooAccessToken)) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/auth_pawoo.php"><img src="<?php echo AppURL; ?>/imgs/link.svg" style="width:24px;"> Pawooと連携する</a>
  </li>
<?php }?>

<?php if($twitterLogin || !empty($pawooAccessToken)) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/logout.php"><img src="<?php echo AppURL; ?>/imgs/release.svg" style="width:24px;"> アプリと連携解除</a>
  </li>
<?php }?>
</ul>

<h3>検索</h3>
<div style="width:75vw;max-width:1024px;margin:auto;">
		<form target="_blank"
			action="//www.yaruox.jp/twimado/timeline/search.php" method="GET">
			<div class="msr_text_05">
				<label>検索キーワード</label>
				<input id="q" type="text" name="q" value="" placeholder="FGO" style="width:75vw;max-width:1024px;">
			</div>
			<p>検索対象</p>
			<div class="form_parts">
    			<div>
    				<input type="radio" name="domain" value="twitter">Twitter 
    			</div>
    			　
    			<div>
    				<input type="radio" name="domain" value="pawoo">Pawoo
    			</div>
    			　
    			<div>
    				<input type="radio" name="domain" value="twitterpawoo" checked>両方
    			</div>
    		</div>
			<br>
			<div class="form_parts">
    			<div class="msr_chack_05">
    				<input id="msr_05_chack01" type="checkbox" name="hs" value="false">
    				<label for="msr_05_chack01">センシティブな画像を表示</label>
    			</div>
    			<div class="msr_chack_05">
    				<input id="msr_05_chack02" type="checkbox" name="thumb" value="false" checked>
    				<label for="msr_05_chack02">元画像を表示</label>
    			</div>
			</div>
			<div class="form_parts">
    			<p class="msr_sendbtn_05">
    				<input type="submit" value="キーワード検索">
    			</p>
    			<p class="msr_sendbtn_05">
    				<input type="submit"
    					onclick="getElementById('q').value='#'+getElementById('q').value"
    					value="ハッシュ検索">
    			</p>
			</div>
		</form>
	</div>
<?php 
if(!empty($lists) && !isset($lists->errors)) {
    ?>
<h3>マイリスト</h3>
<ul class="mylist">
    <?php
    foreach ($lists as $list) {
    ?>
    <li><a href="http://www.yaruox.jp/twimado/timeline/list.php?domain=twitter&id=<?php echo $list->id;?>&name=<?php echo $list->name;?>&hs=false&thumb=false" target="<?php echo $target;?>"><?php echo $list->name;?></a></li>
    <?php 
    }
    ?>
</ul>
    <?php
}
?>
<h3>Twitterトレンド</h3>
<ul class="trend">
<?php 
foreach ($trends[0]->trends as $word) {
    ?>
    <li><a href="http://www.yaruox.jp/twimado/timeline/search.php?q=<?php echo $word->query;?>&=<?php echo $word->query;?>&hs=false&thumb=false" target="<?php echo $target;?>"><?php echo $word->name;?></a></li>
    <?php 
}
?>
</ul>
</body>
</html>