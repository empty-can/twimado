<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'pawoo');
$param->setInitialValue('count', '20');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);

$target_id = $param->getValue('target_id', '');
// if (empty($users)) {
$user_ids = "";
$mydb = new MyDB();

$sql = "SELECT id FROM creator ORDER BY id ASC";

$results = $mydb->select($sql);

foreach ($results as $row) {
    $user_ids .= $row['id'] . ',';
}

$mydb->close();

if (! empty($user_ids)) {
    $user_ids = substr($user_ids, 0, - 1);
}
// }

$api = 'users/lookup'; // アクセスするAPI

$param = new Parameters();
$param->setParam('user_id', $user_ids);

$account = Account;

$tokens = getTwitterTokens($account, "", true);
if ($tokens->isEmpty()) {
    echo "認証情報が取得できませんでした。";
}

// APIアクセス
$users = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);

// myVarDump($users);
$creators = array();
foreach ($users as $users) {
    $creators[$users->id_str]['screen_name'] = $users->screen_name;
    $creators[$users->id_str]['description'] = $users->description;
    $creators[$users->id_str]['name'] = $users->name;
    $creators[$users->id_str]['profile_image_url'] = $users->profile_image_url;
}
// myVarDump($users);
// myVarDump($matomeList);
$creatorList = getAllCreators();

foreach ($creatorList as $creator) {
    $creator_id = $creator['id'];
    $creators[$creator_id]['user_id'] = $creator_id;
    // var_dump($creator_id);
    $matomeList = getMatomeList($creator_id, 'twitter');

    foreach ($matomeList as $matome) {
        $creators[$creator_id]['matome'][] = $matome;
    }
    // var_dump($matomeList);
}
// myVarDump($creators);

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
<link rel="stylesheet" type="text/css" href="//www.suki.pics/css/matome/kuragebunch.css?2019-08-04_14:04:14" />
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
<body id="page-kuragebunch-top" class="">
	<div class="top-contents">
		<div class="latest-update">
			<div class="latest-update-list">
				<ul class="series-items">
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=4&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/senpaigauzaikouhai.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/senpaigauzaikouhai.jpg"
										alt="先輩がうざい後輩の話" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<span class="label-new">NEW</span>
									<a href="/timeline/user.php?domain=twitter&target_id=shiromanta1020&hs=false&thumb=false">
										<h4>先輩がうざい後輩の話</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/1102952308856348672/5uAs6sMp_normal.jpg" style="width:32px">
        									しろまんた
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=4&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=4" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id7=&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/sarariman4.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/sarariman4.jpg"
										alt="サラリーマンが異世界に行ったら四天王になった話" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<span class="label-new">NEW</span>
									<a href="/timeline/user.php?domain=twitter&target_id=poppoyakiya&hs=false&thumb=false">
										<h4>サラリーマンが異世界に行ったら四天王になった話</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/1007585726215540737/hawdTwrH_normal.jpg" style="width:32px">
        									社畜漫画家ベニガシラ
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=7&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=7" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=26&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/youjoshacho.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/youjoshacho.jpg"
										alt="幼女社長" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<span class="label-new">NEW</span>
									<a href="/timeline/user.php?domain=twitter&target_id=fuxxxxxroxxka&hs=false&thumb=false">
    									<h4>幼女社長</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/1179114597409705984/lS-vf_Bd_normal.jpg" style="width:32px">
        									藤井おでこ
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=26&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=26" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=5&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/gokushufudo.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/gokushufudo.jpg"
										alt="極主夫道" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<span class="label-new">NEW</span>
									<a href="//www.suki.pics/timeline/user.php?domain=twitter&target_id=kousuke_oono&hs=false&thumb=false">
    									<h4>極主夫道</h4>
    									<h5><img src="//pbs.twimg.com/profile_images/978329031174062082/5-uGEEsu_normal.jpg" style="width:32px">
    									おおのこうすけ</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=5&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=5" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=&12asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/daemon_core.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/daemon_core.jpg"
										alt="輝け！デーモンコア君" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="/timeline/user.php?domain=twitter&target_id=purinharumaki&hs=false&thumb=false">
										<h4>輝け！デーモンコア君</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/980483358999289856/B9Ml3o4h_normal.jpg" style="width:32px">
        									からめる
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=12&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=12" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=16&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/nokinwatoson.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/nokinwatoson.jpg"
										alt="脳筋ワトソン intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="/timeline/user.php?domain=twitter&target_id=sugaaanuma&hs=false&thumb=false">
										<h4>脳筋ワトソン</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/852564019/twitter_normal.jpg" style="width:32px">
        									すがぬまたつや
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=16&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=16" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=27&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/katajigokuguruma.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/katajigokuguruma.jpg"
										alt="肩地獄車" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="/timeline/user.php?domain=twitter&target_id=fuxxxxxroxxka&hs=false&thumb=false">
										<h4>肩地獄車</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/1179114597409705984/lS-vf_Bd_normal.jpg" style="width:32px">
        									藤井おでこ
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=27&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=27" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=8&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/darepan.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/darepan.jpg"
										alt="誰が何と言おうとガルパンⅣコマ" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="">
    									<h4>誰が何と言おうとガルパンⅣコマ</h4>
    									<h5><img src="//pbs.twimg.com/profile_images/1150038863500328960/5PLTMn0B_normal.png" style="width:32px">	こひのれ</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=8&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=8" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=10&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/anemanta.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/anemanta.jpg"
										alt="あねまんたとしろまんた" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="/timeline/user.php?domain=twitter&target_id=shiromanta1020&hs=false&thumb=false">
    									<h4>あねまんたとしろまんた</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/1102952308856348672/5uAs6sMp_normal.jpg" style="width:32px">
        									しろまんた
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=10&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=10" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=17&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/kodokunoijime.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/kodokunoijime.jpg"
										alt="孤独のイジメ" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="/timeline/user.php?domain=twitter&target_id=sugaaanuma&hs=false&thumb=false">
    									<h4>孤独のイジメ</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/852564019/twitter_normal.jpg" style="width:32px">
        									すがぬまたつや
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=17&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=17" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=2&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/inochi_shinigami.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/inochi_shinigami.jpg"
										alt="命を救った死神の話" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="/timeline/user.php?domain=twitter&target_id=poppoyakiya&hs=false&thumb=false">
    									<h4>命を救った死神の話</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/1007585726215540737/hawdTwrH_normal.jpg" style="width:32px">
        									社畜漫画家ベニガシラ
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=2&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=2" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=1&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/wakagashira.jpg"
										data-width="720" data-height="1000"
										data-src="/images/piece/wakagashira.jpg"
										alt="美少女同人作家と若頭" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
									<a href="/timeline/user.php?domain=twitter&target_id=poppoyakiya&hs=false&thumb=false">
    									<h4>美少女同人作家と若頭</h4>
    									<h5>
        									<img src="https://pbs.twimg.com/profile_images/1007585726215540737/hawdTwrH_normal.jpg" style="width:32px">
        									社畜漫画家ベニガシラ
    									</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=1&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=1" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li>
					<!-- li class="series-items-box">
						<div class="series-items-box-content">
							<a href="/matome/timeline.php?matome_id=&asc=0" class="episode-link">
								<div class="episode-link-thumb">
									<img class=""
										src="/images/piece/"
										data-width="720" data-height="1000"
										data-src="/images/piece/"
										alt="作品名" intrinsicsize="720x1000" loading="lazy" srcset="">
								</div>
								<div class="episode-link-title" style="margin-top:1vh;">
    								<span class="label-new">NEW</span>
									<a href="">
    									<h4>作品名</h4>
    									<h5><img src="" style="width:32px">作者</h5>
									</a>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=&asc=0" class="episode-link">
								<div class="episode-link-latest">
									<span>最新話から読む</span>
								</div>
							</a>
							<a href="/matome/timeline.php?matome_id=" class="series-link">
								<span>最初から読む</span>
							</a>
						</div>
					</li -->
				</ul>
			</div>
		</div>
	</div>
	<script type="text/javascript" id="">$(function(){$(".update-history-list .series-items-box:nth-child(1)").before('\x3cli class\x3d"series-items-box"\x3e\x3cdiv class\x3d"series-items-box-content"\x3e\x3ca href\x3d"/info/8pgogo/11-selection" class\x3d"episode-link"\x3e\x3cspan class\x3d"label-new"\x3eNEW\x3c/span\x3e\x3cdiv class\x3d"episode-link-title"\x3e\x3ch4\x3e8P@GOGO!\u7b2c11\u56de\u9078\u8003\u901a\u904e\u4f5c\u767a\u8868!!\x3c/h4\x3e\x3ch5\x3e\u6295\u7968\u671f\u95939/27\u301c10/11\x3c/h5\x3e\x3c/div\x3e\x3cdiv class\x3d"episode-link-thumb"\x3e\x3cimg src\x3d"https://cdn-ak.f.st-hatena.com/images/fotolife/k/kuragebunch/20191010/20191010181321.jpg" alt\x3d"8P@GOGO!\u7b2c11\u56de\u9078\u8003\u901a\u904e\u4f5c\u767a\u8868!!"\x3e\x3c/div\x3e\x3cdiv class\x3d"episode-link-oneshot"\x3e\x3cspan\x3e\u3053\u306e\u8a71\u3092\u8aad\u3080\x3c/span\x3e\x3c/div\x3e\x3c/a\x3e\x3cdiv class\x3d"next-update-container"\x3e\x3c/div\x3e\x3c/div\x3e\x3c/li\x3e')});</script>
</body>
</html>