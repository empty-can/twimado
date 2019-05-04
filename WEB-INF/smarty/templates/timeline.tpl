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
		<div id="home"><a href="/"><img src="{$app_url}/imgs/home_64.svg"></a></div>
		<div onclick="switchShowTweet();" ontouch="switchShowTweet();"><img id="toggleRetweet" src="{$app_url}/imgs/retwieet.svg"></div>
		<div onclick="switchScroll();" ontouch="switchScroll();">
			<img id="horizontal" style="display:none;" src="{$app_url}/imgs/yoko.svg">
			<img id="vertical" src="{$app_url}/imgs/tate.svg">
		</div>
	</div>
</div>
<div id="mylist" class="myList">
<form>
{foreach from=$mylists item=list}
	<input type="checkbox" name="mylist[]" value="{$list->id}">{$list->name}<br>
{/foreach}
  <br>
  <input type="button" onclick="hideMyList()" value="登録">
</form>
</div>
{include file='parts/footer.tpl'}