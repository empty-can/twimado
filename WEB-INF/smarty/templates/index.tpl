{include file='parts/header.tpl'}
<div id="content">
	<div style="width: 100%; max-width: 768px; margin: 1vh auto" class="flx jcsb">
		<div>
			{if $twitterLogin}
			<div style="margin-right: 10px;">
				<a href="https://twitter.com/" target="{$target}" alt="Twitter:{$twitterLoginAccount.name}@{$twitterLoginAccount.screen_name}">
					<img src="{$twitterLoginAccount.profile_image_url_https}"　style="width: 42px;" class="circle">
				</a>
			</div>
			{/if}
		</div>
		<div>
			<form target="_blank" style="display: block; margin-right: 0px;" action="{$AppURL}/timeline/search.php?domain=twitter" method="GET">
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
	<div id="catchcopy" style="text-align: center; padding-top: 1em;">
		<div class="flx aic jcc" style="width: 100%; margin: auto;">
			<div>Twitter画像・動画が閲覧できるサイト - β版</div>
		</div>
	</div>
	<div style="width: 100%;">

		{if !$twitterLogin}
		<div style="text-align: center; color: white;">
			<a class="flx aic jcc" style="color: inherit;" href="{$AppURL}/auth/auth_twitter.php">
				<span class="twitter-login" style="">Twitterでログインする</span>
			</a>
		</div>
		{else}
		<ul class="gnav" style="max-width: 768px; position: relative; z-index: 200;">
			<li>
				<a href="/">Top</a>
			</li>
			<li>
				<a>リスト</a>
				<ul>
					{foreach from=$lists item=list}
					<li>
						<a href="/timeline/list.php?list_id={$list->id_str}&name={$list->name}&domain=twitter&mo=true&thumb=false">{$list->name}</a>
					</li>
					{/foreach}
				</ul>
			</li>
			<li>
				<a href="/timeline/home.php">Home</a>
			</li>
			<li>
				<a href="{$AppURL}/auth/logout.php">ログアウト</a>
			</li>
		</ul>
		{/if}
		<br>
		<h2 style="width: 95%; max-width: 768px; margin: auto;">&#x1f9e1; いいねランキング</h2>
		<div class="flx jcsa acfs acfs" style="width: 95%; max-width: 768px; margin: auto; position: relative; z-index: 100;">

			<!-- div class="iine" style="{if ($t=='n')}background-color:skyblue;color:white;{/if}">
				{if ($t!='n')}<a href="/?t=n">{/if}新着{if ($t!='n')}</a>{/if}
			</div -->
			<div class="iine" style="{if ($t=='t'||$t=='')}background-color:skyblue;color:white;{/if}">
				{if ($t!='t')}<a href="/?t=t">{/if}今日{if ($t!='t')}</a>{/if}
			</div>
			<div class="iine" style="{if ($t=='w')}background-color:skyblue;color:white;{/if}">
				{if ($t!='w')}<a href="/">{/if}今週{if ($t!='w')}</a>{/if}
			</div>
			<div class="iine" style="{if ($t=='m')}background-color:skyblue;color:white;{/if}">
				{if ($t!='m')}<a href="/?t=m">{/if}今月{if ($t!='m')}</a>{/if}
			</div>
			<div class="iine" style="{if ($t=='y')}background-color:skyblue;color:white;{/if}">
				{if ($t!='y')}<a href="/?t=y">{/if}今年{if ($t!='y')}</a>{/if}
			</div>
			<div class="iine" style="{if is_numeric($t)}background-color:skyblue;color:white;{/if}">
				<div>
					<a>{if (is_numeric($t) && $t<date('Y'))}{$t}年{else}去年以前{/if}</a>
					<ul>
						{for $year=1 to 10}
						<li>
							<a href="/?t={date('Y')-$year}">{date('Y')-$year}年</a>
						</li>
						{/for}
					</ul>
				</div>
			</div>
		</div>
		<br>
		<div class="flx jcsa acfs acfs" style="width: 100%;">
			<form class="flx jcsa acfs acfs" style="display: block; margin: auto; padding: 0px;" action="{$AppURL}/" method="GET">
				<input type="date" name="from" value="{$from}">
				<input type="date" name="to" value="{$to}">
				<button type="submit">期間指定</button>
			</form>
		</div>
	</div>
</div>
<div id="ranking" class="flx fww jcc acfs">
	{foreach from=$tweets item=tweet}
		{if ((isset($twitterLogin) && !empty($twitterLogin)) || !$tweet['sensitive'])}
	<div class="tweet_box">
			{assign var=embedHtml value=''}
			{if (!isset($tweet["media"]) || !isset($tweet["media"][0]))}
				<iframe scrolling="no" frameborder="0" loading="lazy" src="/api/twitter/oembed.php?id={$tweet['id']}&account={$tweet['account']['accountName']}&maxwidth=250"></iframe>
			{else}
		<div class="tweet">
			<div class="account_info">
				<div class="pfofile_image">
					<a href="/timeline/user.php?domain=twitter&target_id={$tweet['account']['id']}&thumb=true" target="_blank">
						<img width="36px" src="{$tweet['account']['profileImage']}">
					</a>
				</div>
				<div class="name">
					<div class="display_name">
						<a href="/timeline/user.php?domain=twitter&target_id={$tweet['account']['id']}&thumb=true" target="_blank">
							{$tweet['account']['displayName']}
						</a>
					</div>
					<div class="account_name">
						<a href="/timeline/user.php?domain=twitter&target_id={$tweet['account']['id']}&thumb=true" target="_blank">
							@{$tweet['account']['accountName']}
						</a>
					</div>
				</div>
				<div class="account_icon">
					<a href="https://twitter.com/{$tweet['account']['accountName']}/status/{$tweet['id']}" target="_blank">
						<img width="36px" src="https://abs.twimg.com/responsive-web/web/icon-default.604e2486a34a2f6e1.png">
					</a>
				</div>
			</div>
			<div class="text">
					{$tweet['text']}
			</div>
			<div class="media_box">
				<div class="media">
					<a class="popup-modal" href="#inline-wrap-{$tweet['id']}" target="_blank">
				{if $tweet['isVideo']}
					{foreach from=$tweet['media'] item=media}
						<video src="{$media['url']}" poster="{$media['thumb']}" preload="metadata" controls>
						</video>
					{/foreach}
				{else}
					{foreach from=$tweet['media'] item=media}
						<img src="{$media['thumb']}">
					{/foreach}
				{/if}
					</a>
				</div>
				<div id="inline-wrap-{$tweet['id']}" style="max-width: 80vw; overflow: auto; margin: auto; background-color: black;" class="mfp-hide">
				{if !empty($embedHtml)}
					<scrolling="no" frameborder="0" loading="lazy" src="/api/twitter/oembed.php?id={$tweet['id']}&account={$tweet['account']['accountName']}&maxwidth=250"></iframe>
				{else}
					<div style="max-width: 80%; background-color: white; padding: 1px; margin: auto auto 2px auto;">
						<div style="width: 100%; overflow: hidden; white-space: nowrap;">
							<a href="/timeline/user.php?domain=twitter&target_id={$tweet['account']['id']}&thumb=true" target="_blank">
								<img src="{$tweet['account']['profileImage']}">{$tweet['account']['displayName']}
							</a>
							{date4timeline($tweet['date'])}
						</div>
					{$tweet['text']}
					</div>
					{if $tweet['isVideo']}
						{foreach from=$tweet['media'] item=media}
					<video style="max-width: 80vw; max-height: 90vh; margin: auto; display: block;" src="{$media['url']}" poster="{$media['thumb']}" preload="metadata" controls>
					</video>
					<br>
						{/foreach}
					{else}
						{foreach from=$tweet['media'] item=media}
					<img style="max-width: 80vw; max-height: 90vh; margin: auto; display: block;" src="{$media['url']}">
					<br>
						{/foreach}
					{/if}
					<div class="mutter_menu flx fww jcsa aic" style="width: 100%; background-color: white;">
						<div>
					{if $tweet['favorited']}
							<span id="fav_icon_{$tweet['id']}" class="favoff" onclick="fav(this,'{$tweet['id']}','{$tweet['domain']}','undo');">
								&#x1f493;
								<span id="fav_count_{$tweet['id']}">
									{$tweet['favCount']}
								</span>
							</span>
							<input type="hidden" id="fav_{$tweet['id']}" value="on">
					{else}
							<span id="fav_icon_{$tweet['id']}" class="favon"　onclick="fav(this,'{$tweet['id']}','{$tweet['domain']}','do');">
								 &#x2661;
								<span id="fav_count_{$tweet['id']}">
								 {$tweet['favCount']}
								</span>
							</span>
							<input type="hidden" id="fav_{$tweet['id']}"　value="off">
					{/if}
						</div>
						<div>
					{if $tweet['retweeted']}
							<span id="rt_icon_{$tweet['id']}" class="rtoff" onclick="rt(this,'{$tweet['id']}','{$tweet['domain']}','undo');">
							 &#x1f502;
							 	<span id="rt_count_{$tweet['id']}">
									{$tweet['reCount']}
								</span>
							</span>
							<input type="hidden" id="rt_{$tweet['id']}" value="on">
					{else}
							<span id="rt_icon_{$tweet['id']}" class="rton"　onclick="rt(this,'{$tweet['id']}','{$tweet['domain']}','do');">
							 	 &#x1f501;
							  	<span id="rt_count_{$tweet['id']}">
							  		 {$tweet['reCount']}
							 	</span>
							</span>
							<input type="hidden" id="rt_{$tweet['id']}"　value="off">
					{/if}
						</div>
						<div>
							<a href="https://twitter.com/{$tweet['account']['accountName']}/status/{$tweet['id']}" target="_blank">
								<img src="https://abs.twimg.com/responsive-web/web/icon-default.604e2486a34a2f6e1.png"　style="width: 2em;">
							</a>
						</div>
					</div>
				</div>
				{/if}
			</div>
		</div>
		<div class="mutter_menu flx fww jcsa aic">
			{date4timeline($tweet['date'])}
			<div>
				{if $tweet['favorited']}
				<span id="fav_icon_{$tweet['id']}" class="favoff" onclick="fav(this,'{$tweet['id']}','{$tweet['domain']}','undo');">
					 &#x1f493;
					<span id="fav_count_{$tweet['id']}">
						{$tweet['favCount']}
					</span>
				</span>
				<input type="hidden" id="fav_{$tweet['id']}" value="on">
				{else}
				<span id="fav_icon_{$tweet['id']}" class="favon"　onclick="fav(this,'{$tweet['id']}','{$tweet['domain']}','do');">
					 &#x2661;
				 	<span id="fav_count_{$tweet['id']}">
				 		 {$tweet['favCount']}
				 	</span>
				 </span>
				 <input type="hidden" id="fav_{$tweet['id']}"　value="off">
				{/if}
			</div>
			<div>
				{if $tweet['retweeted']}
				<span id="rt_icon_{$tweet['id']}" class="rtoff" onclick="rt(this,'{$tweet['id']}','{$tweet['domain']}','undo');">
					 &#x1f502;
					 <span id="rt_count_{$tweet['id']}">
						{$tweet['reCount']}
					</span>
				</span>
				<input type="hidden" id="rt_{$tweet['id']}" value="on">
				{else}
				<span id="rt_icon_{$tweet['id']}" class="rton"　onclick="rt(this,'{$tweet['id']}','{$tweet['domain']}','do');">
					 &#x1f501;
					 <span id="rt_count_{$tweet['id']}">
					 	 {$tweet['reCount']}
					 </span>
				</span>
				<input type="hidden" id="rt_{$tweet['id']}"　value="off">
				{/if}
			</div>
			<div>
				<a href="https://twitter.com/{$tweet['account']['accountName']}/status/{$tweet['id']}" target="_blank">
					<img src="https://abs.twimg.com/responsive-web/web/icon-default.604e2486a34a2f6e1.png"　style="width: 1.2em">
				</a>
			</div>
		</div>
		{/if}
	</div>
	{/if} {/foreach}
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
<div class="img circle" style="width: 100%; text-align: center; color: gray;">
	<a href="{$AppURL}/auth/auth_twitter.php"> <img src="{$AppURL}/imgs/auth_twitter.png" style="width: 20px;">
	</a>
	<br>
	Twitterアカウントと連携する
</div>
{/if}
</div>
{include file='parts/footer.tpl'}
