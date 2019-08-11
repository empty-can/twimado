{include file='parts/header.tpl'}
<h3 id="title" style="width:100%;text-align:center;">{$title}</h3>
<div id="timeline">
{foreach from=$mutters item=mutter}
	{$mutter}
{/foreach}
</div>
<div id="bottom_message" style="text-align: center;"></div>
<div id="top_menu">
	<div id="timeline_menu">
		<div id="home"><a href="{$AppURL}/"><img src="{$app_url}/imgs/home_64.svg"></a></div>
		<div onclick="switchShowTweet();" ontouch="switchShowTweet();"><img id="toggleRetweet" src="{$AppURL}/imgs/retwieet.svg"></div>
		<div onclick="switchScroll();" ontouch="switchScroll();">
			<img id="horizontal" style="display:none;" src="{$AppURL}/imgs/yoko.svg">
			<img id="vertical" src="{$AppURL}/imgs/tate.svg">
		</div>
	</div>
</div>
<div id="operation" class="operation" style="">
<form>
{foreach from=$matomeInfo item=info}
	<input type="checkbox" name="matome[]" value="{$info['id']}">{$info['title']}<br>
{/foreach}
  <br>
  <input type="button" onclick="hideMyList()" value="登録">
</form>
</div>
<div id="goods" class="goods">
</div>
<div id="affiliate">
{$matomeInfo.affiliate}
</div>
{include file='parts/footer.tpl'}