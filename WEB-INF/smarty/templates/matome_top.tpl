{include file='parts/header.tpl'}
    {foreach from=$matomeList item=matome}
<ul>
	<li><a href="/matome/timeline.php?matome_id={$matome.id}">{$matome.title}</a></li>
</ul>
    {/foreach}
{include file='parts/footer.tpl'}