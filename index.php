<?php
require_once ("init.php");

$woeid = "1118370"; // Tokyo
$api = 'trends/place';

$params = array(
    "id" => $woeid
);

$result = getTwitterConnection("", "")->get($api, $params);

// myVarDump($result[0]->trends);

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
<h3>タイムライン</h3>
<ul class="breadcrumb">
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
<h3>Twitterトレンド</h3>
<ul class="trend">
<?php 
foreach ($result[0]->trends as $word) {
    ?>
    <li><a href="http://www.yaruox.jp/twimado/search/?q=<?php echo $word->query;?>&hs=false&thumb=false" target="$target"><?php echo $word->name;?></a></li>
    <?php 
}
?>
</ul>
</body>
</html>