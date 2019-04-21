<?php
require_once ("init.php");

$woeid = "1118370"; // Tokyo
$api = 'trends/place';

$params = array(
    "id" => $woeid
);

$result = getTwitterConnection("", "")->get($api, $params);

// myVarDump($result[0]->trends);
// echo "twitter_access_token:".getSessionParam("twitter_access_token", "")."<br>\r\n";
// echo "twitter_access_token_secret:".getSessionParam("twitter_access_token_secret", "")."<br>\r\n";

$userInfo = getSessionParam("twitter_user_info", "");

$twitterLogin = (!empty(getSessionParam("twitter_access_token", ""))
    && !empty(getSessionParam("twitter_access_token_secret", "")));

$lists = array();
if($twitterLogin) {
    $api = 'lists/list';
    $params = array(
        "screen_name" => $userInfo->screen_name
    );
    $lists = getTwitterConnection("", "")->get($api, $params);
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
      echo "ログインしてます:$userInfo->name<br>\r\n";
  ?>
<h3>アプリ連携</h3>
<ul class="breadcrumb">
<?php if(!$twitterLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/twitter.php">Twitterと連携する</a>
  </li>
<?php } else { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/logout.php">Twitterと連携解除</a>
  </li>
<?php }?>
  <!-- li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/auth/pawoo.php">Pawoo</a>
  </li -->
</ul>
<h3>タイムライン</h3>
<ul class="breadcrumb">
  <?php if($twitterLogin) { ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/home.php?hs=false&thumb=false" target="$target"><img src="<?php echo AppURL; ?>/imgs/home_64.svg" style="width:24px;"> TwitterホームTL</a>
  </li>
  <?php } ?>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?domain=twitter&hs=true&thumb=false" target="$target">Twitter（安全）</a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?domain=pawoo&hs=false&thumb=false" target="$target">Pawoo</a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="//www.yaruox.jp/twimado/timeline/?hs=false&thumb=false" target="$target">Twitter＆Pawoo</a>
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
if(!empty($lists)) {
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
foreach ($result[0]->trends as $word) {
    ?>
    <li><a href="http://www.yaruox.jp/twimado/timeline/search.php?q=<?php echo $word->query;?>&=<?php echo $word->query;?>&なめhs=false&thumb=false" target="$target"><?php echo $word->name;?></a></li>
    <?php 
}
?>
</ul>
</body>
</html>