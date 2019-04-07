{include file='parts/header.tpl'}
<h3 style="width:100%;text-align:center;">{$title}</h3>
<div id="timeline">
{foreach from=$mutters item=mutter}
	{$mutter}
{/foreach}
</div>
{include file='parts/footer.tpl'}