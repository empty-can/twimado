<?php
require_once ("init.php");

$woeid = "1118370"; // Tokyo
$api = 'trends/place';

$params = array(
    "id" => $woeid
);
$domain = "";

$trends = getTwitterConnection("", "")->get($api, $params);

$userInfo = getSessionParam("twitter_user_info", "");
// myVarDump($userInfo);

$twitterLogin = (!empty(getSessionParam("twitter_access_token", ""))
    && !empty(getSessionParam("twitter_access_token_secret", "")));

$lists = array();
if($twitterLogin) {
    $domain .= "twitter";
    $api = 'lists/list';
    $params = array(
        "screen_name" => $userInfo->screen_name
    );
    setSessionParam("twitter_id", $userInfo->id);
    
    $twitter_access_token = getSessionParam("twitter_access_token", "");
    $twitter_access_token_secret = getSessionParam("twitter_access_token_secret", "");
    //  echo "twitter_access_token:".$twitter_access_token."<br>\r\n";
    //  echo "twitter_access_token_secret:".$twitter_access_token_secret."<br>\r\n";
    
    setTokens($userInfo->id, $twitter_access_token, $twitter_access_token_secret);
    $lists = getTwitterConnection($twitter_access_token, $twitter_access_token_secret)->get($api, $params);
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
    $domain .= "pawoo";
//     echo $pawooAccount["id"];
    setTokens($pawooAccount["id"], $pawooAccessToken, "");
}

?>
<html>
  <head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<!--[if IE]>
	    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<title>二次絵絶対拡散するサイト</title>
    <link rel="stylesheet" type="text/css" href="/twimado/css/common.css?2019-04-14_22:35:35" />
    <link rel="stylesheet" type="text/css" href="/twimado/css/top.css?2019-04-14_22:35:35" />
  </head>
  <body>
  <?php 
  if(isset($userInfo->name))
      echo "Twitterログインしてます:$userInfo->name<br>\r\n";
      
  if(!empty($pawooAccessToken))
      echo "Pawooログインしてます:".$pawooAccount['display_name']."@".$pawooAccount['username']."<br>\r\n";
  ?>
<h3>アプリ連携</h3>
<ul class="breadcrumb">
<?php if(!$twitterLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/auth_twitter.php">Twitterと連携する</a>
  </li>
<?php }

    if(empty($pawooAccessToken)) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/auth_pawoo.php">Pawooと連携する</a>
  </li>
<?php }?>

<?php if($twitterLogin || !empty($pawooAccessToken)) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/logout.php">アプリと連携解除</a>
  </li>
<?php }?>
</ul>
<h3>タイムライン</h3>
<ul class="breadcrumb">
  <?php if($twitterLogin || $pawooLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/home.php?domain=<?php echo $domain;?>&hs=false&thumb=false" target="$target"><img src="<?php echo AppURL; ?>/imgs/home_64.svg" style="width:24px;"> ホームTL</a>
  </li>
  <?php } ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?domain=twitter&hs=true&thumb=false" target="$target">公式TL（Twitter）</a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?domain=pawoo&hs=false&thumb=false" target="$target">公式TL（Pawoo）</a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?hs=false&thumb=false" target="$target">公式TL（Twitter＆Pawoo）</a>
  </li>
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
    <li><a href="http://www.yaruox.jp/twimado/timeline/list.php?domain=twitter&id=<?php echo $list->id;?>&name=<?php echo $list->name;?>&hs=false&thumb=false" target="$target"><?php echo $list->name;?></a></li>
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
    <li><a href="http://www.yaruox.jp/twimado/timeline/search.php?q=<?php echo $word->query;?>&=<?php echo $word->query;?>&なめhs=false&thumb=false" target="$target"><?php echo $word->name;?></a></li>
    <?php 
}
?>
</ul>
</body>
</html>