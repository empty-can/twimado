{include file='parts/header.tpl'}
<div id="content">
{if $twitterLogin}
	<br>
	<div class="flx jcsb" style="width:100%; margin:auto;">
	<div style="margin-right: 3px;">
		 <a href="https://twitter.com/" target="{$target}"
			alt="Twitter:{$twitterLoginAccount.name}@{$twitterLoginAccount.screen_name}">
			<img src="{$twitterLoginAccount.profile_image_url_https}"
			style="width: 24px;" class="circle">
		</a>
	</div>
	&nbsp;
	<div>
		<a style="display:block;" href="{$AppURL}/auth/logout.php">
			<img src="{$AppURL}/imgs/logout.svg" style="background-color: lightgray; width: 30px; padding: 2px;" alt="ログアウト">
		</a>
	</div>
</div>
{/if}
<div id="catchcopy" style="text-align:center;">
	<br>
	イラスト、写真、動画
	<br>
	見つけよう、SukiPics
	<br>
	<br>
</div>
<div style="width: 100%; margin: auto;">
	<h1>検索ボックス</h1>
	<div id="ckeck_box" class="flx fww jcc aic acc" style="margin: 3px 3px 0px 0px">
		<!-- div style="margin:5px;">
	{if !isset($mo) || empty($mo)}
			<input type="checkbox" name="mo" value="false" onchange="toggleParam('mo');" checked>
	{else if $mo=='true'}
			<input type="checkbox" name="mo" value="false" onchange="toggleParam('mo');" checked>
	{else}
			<input type="checkbox" name="mo" value="false" onchange="toggleParam('mo');">
	{/if}
		メディアのみ
		</div -->
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
	</form>
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
</div>
<br>
{if !$twitterLogin}
<div class="img circle" style="width:100%;text-align:center;color:gray;">
	<a href="{$AppURL}/auth/auth_twitter.php">
		<img src="{$AppURL}/imgs/auth_twitter.png" style="width:30px;">
	</a>
	<br>
	Twitterアカウントと連携する
</div>
{/if}
{include file='parts/footer.tpl'}