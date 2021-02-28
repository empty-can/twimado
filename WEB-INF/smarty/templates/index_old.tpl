{include file='parts/header.tpl'}
<div id="content">
	<div style="width:100%;max-width:768px;margin:1vh auto" class="flx jcsb">
		<div>
	{if $twitterLogin}
			<div style="margin-right: 10px;">
				 <a href="https://twitter.com/" target="{$target}"
					alt="Twitter:{$twitterLoginAccount.name}@{$twitterLoginAccount.screen_name}">
					<img src="{$twitterLoginAccount.profile_image_url_https}"
					style="width: 42px;" class="circle">
				</a>
			</div>
	{/if}
		</div>
		<div>
			<form target="_blank"
					style="display:block;margin-right:0px;"
					action="{$AppURL}/timeline/search.php?domain=twitter" method="GET">
					<div class="flx fww jcfe aic acfs">
					<div>
						<div style="margin: 10px 10px 0 0;">
							<input type="text" name="q" size="36" placeholder="&#x1f50d; 検索キーワード例：FGO あずまきよひこ" value="">
						</div>
					</div>
					<div class="flx jcfe aic acfs">
						<div style="margin: 5px 10px 0 0;">
							<button type="submit" name="searchType" value="normal">キーワード</button>
						</div>
						<div style="margin: 5px 10px 0 0;">
							<button type="submit" name="searchType" value="hash">タグ</button>
						</div>
						<div style="margin: 5px 10px 0 0;">
							<button type="submit" name="searchType" value="account">アカウント</button>
						</div>
					</div>
					</div>
			</form>
		</div>
	</div>
	<div id="catchcopy" style="text-align:center;padding-top:1em;">
		<div class="flx aic jcc" style="width:100%; margin:auto;">
		<div>
			Twitter画像・動画が閲覧できるサイト - β版
		</div>
		</div>
	</div>
<div style="width:100%;">

{if !$twitterLogin}
<div style="text-align:center;color:white;">
	<a class="flx aic jcc" style="color:inherit;" href="{$AppURL}/auth/auth_twitter.php">
	    <span class="twitter-login" style="">Twitterでログインする</span>
	</a>
</div>
{else}
	<ul class="gnav" style="max-width:768px;position:relative;z-index:200;">
	    <li><a href="/">Top</a></li>
	    <li>
	    	<a>リスト</a>
	    	<ul>
	    	{foreach from=$lists item=list}
	            <li><a href="/timeline/list.php?list_id={$list->id_str}&name={$list->name}&domain=twitter&mo=true&thumb=false&hs=false">{$list->name}</a></li>
	    	{/foreach}
	        </ul>
	    </li>
	    <li><a href="/timeline/home.php">Home</a></li>
	    <li><a href="{$AppURL}/auth/logout.php">ログアウト</a></li>
	</ul>
{/if}
	<!-- div id="ckeck_box" class="flx fww jcc aic acc" style="margin: 3px 3px 0px 0px">
		<!-- div style="margin:5px;">
	{if !isset($mo) || empty($mo)}
			<input type="checkbox" name="mo" value="false" onchange="toggleParam('mo');" checked>
	{else if $mo=='true'}
			<input type="checkbox" name="mo" value="false" onchange="toggleParam('mo');" checked>
	{else}
			<input type="checkbox" name="mo" value="false" onchange="toggleParam('mo');">
	{/if}
		メディアのみ
		</div>
		&nbsp;
		<div style="margin:5px;">
	{if $hs=='true'}
			<input type="checkbox" name="hs" value="false" onchange="toggleParam('hs');" checked>
	{else}
			<input type="checkbox" name="hs" value="false" onchange="toggleParam('hs');">
	{/if}
		セーフフィルタ
		</div>
		&nbsp;
		<div style="margin:5px;">
	{if $thumb=='false'}
			<input type="checkbox" name="thumb" value="false" onchange="toggleParam('thumb');" checked>
	{else}
			<input type="checkbox" name="thumb" value="false" onchange="toggleParam('thumb');">
	{/if}高画質
		&nbsp;
		</div>
	</div>

	<form target="_blank" style="width: 100%; padding: 0px;"
		action="{$AppURL}/timeline/search.php" method="GET">
		{if $mo=='true'}<input type="hidden" name="f" value="twimg">{/if}
		<input type="hidden" name="domain" value="twitter">
		<div class="msr_text_05">
			<label></label> <input id="q" type="text" name="q" value=""
				placeholder="検索キーワード:例 FGO"
				style="display: block; width: 100%; max-width: 256px; margin: auto;">
		</div>
		<div id="ckeck_box" class="flx fww jcc aic acc" style="margin: 3px 3px 0px 0px">
			<div style="margin:0px 5px;">
				<button type="submit" name="searchType" value="normal"> キーワード検索 </button>
			</div>
			<div style="margin:0px 5px;">
				<button type="submit" name="searchType" value="hash"> &num;タグ検索 </button>
			</div>
{if $twitterLogin}
			<div style="margin:0px 5px;">
				<button type="submit" name="searchType" value="account"> アカウント検索 </button>
			</div>
{/if}
			<div class="form_parts">
		</div>
	</form -->
<br>
	<h2 style="width:95%;max-width:768px;margin:auto;">&#x1f9e1; いいねランキング</h2>
<div class="flx jcsa acfs acfs" style="width:95%;max-width:768px;margin:auto;position:relative;z-index:100;">

	<!-- div class="iine" style="{if ($t=='n')}background-color:skyblue;color:white;{/if}">{if ($t!='n')}<a href="/?t=n">{/if}新着{if ($t!='n')}</a>{/if}</div -->
	<div class="iine" style="{if ($t=='t')}background-color:skyblue;color:white;{/if}">{if ($t!='t')}<a href="/?t=t">{/if}今日{if ($t!='t')}</a>{/if}</div>
	<div class="iine" style="{if ($t=='w')}background-color:skyblue;color:white;{/if}">{if ($t!='w')}<a href="/"    >{/if}今週{if ($t!='w')}</a>{/if}</div>
	<div class="iine" style="{if ($t=='m')}background-color:skyblue;color:white;{/if}">{if ($t!='m')}<a href="/?t=m">{/if}今月{if ($t!='m')}</a>{/if}</div>
	<div class="iine" style="{if ($t=='y')}background-color:skyblue;color:white;{/if}">{if ($t!='y')}<a href="/?t=y">{/if}今年{if ($t!='y')}</a>{/if}</div>
	<div class="iine" style="{if (is_numeric($t) && $t<date('Y'))}background-color:skyblue;color:white;{/if}">
		<div>
			<a>{if (is_numeric($t) && $t<date('Y'))}{$t}年{else}去年以前{/if}</a>
			<ul>
	    	{for $year=1 to 10}
	            <li><a href="/?t={date('Y')-$year}">{date('Y')-$year}年</a></li>
	    	{/for}
	        </ul>
        </div>
	</div>
</div>
<br>
<div class="flx jcsa acfs acfs" style="width:100%;">
	<form class="flx jcsa acfs acfs" style="display:block;margin:auto;padding: 0px;" action="{$AppURL}/" method="GET">
	{if is_numeric($t)}
		<input type="date" name="from" value="{$t}-01-01">
		<input type="date" name="to" value="{$t}-12-31">
	{else}
		<input type="date" name="from" value="{date('Y-m-d')}">
		<input type="date" name="to" value="{date('Y-m-d')}">
	{/if}
		<button type="submit">期間指定</button>
	</form>
</div>
</div>
</div>
	<div class="flx fww jcsa acfs acfs" style="width:100%;margin:auto;">
	{foreach from=$tweets item=tweet}
	{if ((isset($twitterLogin) && !empty($twitterLogin)) || !$tweet['sensitive'])}
		<div style="width:20%;max-width:192px;margin:5px;overflow:hidden;">
			<div style="width:100%;white-space:nowrap;">
				<a href="/timeline/user.php?domain=twitter&target_id={$tweet['account']['id']}&hs=true&thumb=true" target="_blank"><img width="10%" src="{$tweet['account']['profileImage']}">{$tweet['account']['displayName']}</a> {date4timeline($tweet['date'])}
			</div>
			<div style="width:100%;height:2em;margin-bottom:0.5em;overflow:hidden;white-space:nowrap;">
			{$tweet['text']}
			</div>
			<div style="width:100%;height:25vh;overflow:hidden;">
				<a class="popup-modal" href="#inline-wrap-{$tweet['id']}" target="_blank">
				<div style="max-width:80%;margin:auto;max-height:85vh;overflow:hidden;">
					{if $tweet['isVideo']}
						{foreach from=$tweet['media'] item=media}
						<video style="max-width:100%;max-height:80vh;margin:auto;display:block;border:solid 1px gray;border-radius:5px;" src="{$media['url']}" poster="{$media['thumb']}" preload="metadata" controls></video><br>
						{/foreach}
					{else}
						{foreach from=$tweet['media'] item=media}
						<img style="max-width:100%;max-height:80vh;margin:auto;display:block;border:solid 1px gray;border-radius:5px;" src="{$media['url']}"><br>
						{/foreach}
					{/if}
				</div>
				</a>
				<div id="inline-wrap-{$tweet['id']}" style="max-width:80vw;overflow:auto;margin:auto;background-color:black;" class="mfp-hide">
					{if $tweet['isVideo']}
						{foreach from=$tweet['media'] item=media}
						<video style="max-width:80vw;max-height:90vh;margin:auto;display:block;" src="{$media['url']}" poster="{$media['thumb']}" preload="metadata" controls></video><br>
						{/foreach}
					{else}
						{foreach from=$tweet['media'] item=media}
						<img style="max-width:80vw;max-height:90vh;margin:auto;display:block;" src="{$media['url']}"><br>
						{/foreach}
					{/if}
				</div>
			</div>
			<div class="mutter_menu" style="display: flex; justify-content: space-around; align-items: center; width: 100%; height: 32px; margin: 10px 0px 5px 0px;">
				<div>
				{if $tweet['retweeted']}
					<span id="rt_icon_{$tweet['id']}" class="rtoff" onclick="rt(this,'{$tweet['id']}','{$tweet['domain']}','undo');"> &#x1f502; <span id="rt_count_{$tweet['id']}"> {$tweet['reCount']} </span></span>
	 				<input type="hidden" id="rt_{$tweet['id']}" value="on">
	 			{else}
	 				<span id="rt_icon_{$tweet['id']}" class="rton" onclick="rt(this,'{$tweet['id']}','{$tweet['domain']}','do');"> &#x1f501; <span id="rt_count_{$tweet['id']}"> {$tweet['reCount']} </span></span>
	 				<input type="hidden" id="rt_{$tweet['id']}" value="off">
	 			{/if}
				</div>
				<div>
				{if $tweet['favorited']}
					<span id="fav_icon_{$tweet['id']}" class="favoff" onclick="fav(this,'{$tweet['id']}','{$tweet['domain']}','undo');"> &#x1f493; <span id="fav_count_{$tweet['id']}"> {$tweet['favCount']} </span></span>
					<input type="hidden" id="fav_{$tweet['id']}" value="on">
				{else}
					<span id="fav_icon_{$tweet['id']}" class="favon" onclick="fav(this,'{$tweet['id']}','{$tweet['domain']}','do');"> &#x2661; <span id="fav_count_{$tweet['id']}"> {$tweet['favCount']} </span></span>
					<input type="hidden" id="fav_{$tweet['id']}" value="off">
				{/if}
				</div>
				<div>
					<a href="https://twitter.com/{$tweet['account']['accountName']}/status/{$tweet['id']}" target="_blank"> <img src="https://abs.twimg.com/responsive-web/web/icon-default.604e2486a34a2f6e1.png"
						style="width: 32px">
					</a>
				</div>
			</div>
			<hr>
		</div>
	{/if}
	{/foreach}
	</div>
</div>
<br>
<!--
<h2>検索サンプル</h2>
<br>
<br>
<div class="flex_parent">
	{foreach from=$searchList item=list}
	<div class="icon">
		<div class="img circle">
			<a href="{$list.link}&f=twimg&domain=twitter" target="{$target}">
				<img src="{$list.img}">
			</a>
		</div>
		<div class="description">
			{$list.desc}
		</div>
	</div>
	{/foreach}
</div>
-->
<br>
<br>
<br>
<!--
{if $twitterLogin}
<h1>Twitterトレンドから検索</h1>
{if !empty($trends) && isset($trends[0]->trends)}
<div class="trend_parent">
    {foreach from=$trends[0]->trends item=word}
	<div class="trend">
	<a href="{$AppURL}/timeline/search.php?domain=twitter&q={$word->query}{if $mo=='true'}&f=twimg{/if}" target="{$target}">{$word->name}</a>
	</div>
	{/foreach}
</div>
{else}
Twitterトレンドが取得できませんでした。
{$message}
{/if}
{/if}
 -->
{if !$twitterLogin}
<div class="img circle" style="width:100%;text-align:center;color:gray;">
	<a href="{$AppURL}/auth/auth_twitter.php">
		<img src="{$AppURL}/imgs/auth_twitter.png" style="width:20px;">
	</a>
	<br>
	Twitterアカウントと連携する
</div>
{/if}
</div>
{include file='parts/footer.tpl'}