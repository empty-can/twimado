{include file='parts/header.tpl'}
	{$i=(int)0}
<div style="background-color:#cfcfcf;madding:2vw;display:flex;	flex-diretion: row;flex-wrap:wrap;justify-content: space-around;">
    {foreach from=$creators item=creator}
	    <div style="background-color:#cfcfcf;flex-basis:100%;">
			{if ($i != $creator['user_id']['id'])}
				<a href="/matome/matome.php?target_id={$creator['user_id']}&name={$creator['name']}&target_domain=twitter" target="_blank">
					<img alt="{$creator['description']}"
						src="{$creator['profile_image_url']}">
				{$i = $info['user_info']['id']}
				{$creator['name']}@{$info['user_info']['screen_name']}<br>
				{$creator['descrption']}
				</a>
				<br>
			{/if}
	    	{foreach from=$creator['matome'] item=matome}
			　　　　<a href="/matome/timeline.php?matome_id={$matome['id']}" target="_blank">{$matome['title']}</a>（{$matome['total']}）<br>
	   		{/foreach}<br>
	    </div>
    {/foreach}
    </div>
<div class="clear"></div>
{include file='parts/footer.tpl'}