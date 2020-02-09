{include file='parts/header.tpl'}
<div id="content">

<h1>検索ボックス</h1>
<div style="width: 100%; margin: auto;">
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

	<form style="width: 100%; padding: 0px;"
		action="{$AppURL}/timeline/search.php" method="GET">
		<div class="msr_text_05">
			<label></label> <input id="q" type="text" name="q" value="{$q}"
				placeholder="検索キーワード:例 FGO"
				style="display: block; width: 100%; max-width: 256px; margin: auto;">
		</div>
		<div id="ckeck_box" class="flx fww jcc aic acc" style="margin: 3px 3px 0px 0px">
			<div style="margin:0px 5px;">
				<button type="submit" name="searchType" value="normal"> キーワード検索 </button>
			</div>
			<div style="margin:0px 5px;">
				<button type="submit" name="searchType" value="hash"> タグ検索 </button>
			</div>
			<div style="margin:0px 5px;">
				<button type="submit" name="searchType" value="account"> アカウント検索 </button>
			</div>
			<div class="form_parts">
		</div>
	</form>
</div>
<br>
<h1>候補アカウント</h1>
{if !empty($accounts)}
<div class="trend_parent">
    {foreach from=$accounts item=account}
    <div style="width:100%; margin: 10px auto;">
    	<div class="flx aifs acfs">
    		<div style="vertical-align:top;">
    			<a href="//www.suki.pics/timeline/user.php?domain=twitter&target_id={htmlentities($account->id)}&name={htmlentities($account->name)}" target="_blank">
    				<img alt="{htmlentities($account->name)}" src="{$account->profile_image_url_https}" style="border-radius: 50%;">
				</a>
			</div>
    		<div style="padding-left:10px;">
				<a href="//www.suki.pics/timeline/user.php?domain=twitter&target_id={htmlentities($account->id)}&name={htmlentities($account->name)}" target="_blank">
					{htmlentities($account->name)}@{htmlentities($account->screen_name)}
				</a>
				<br>
				{htmlentities($account->description)}
				<div style="margin-top:5px;">
				フォロワー数：{htmlentities($account->followers_count)}　フレンズ数：{htmlentities($account->friends_count)}
				</div>
			</div>
		</div>
	</div>
	{/foreach}
</div>
{else if}
アカウントが見つかりません。
{/if}
</div>
{include file='parts/footer.tpl'}