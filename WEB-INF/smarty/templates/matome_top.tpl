{include file='parts/header.tpl'}
	{$i=(int)0}
    {foreach from=$matomeInfo item=info}
    <div>
	{if ($i != $info['user_info']['id'])}
		{$i = $info['user_info']['id']}
		{$info['user_info']['name']}@{$info['user_info']['screen_name']}<br>
	{/if}
    	{foreach from=$info['matome'] item=matome}
		　　　　<a href="/matome/timeline.php?matome_id={$matome['id']}">{$matome['title']}</a>（{$matome['total']}）<br>
   		{/foreach}<br>
    </div>
    {/foreach}
{include file='parts/footer.tpl'}