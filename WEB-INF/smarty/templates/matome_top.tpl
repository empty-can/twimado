{include file='parts/header.tpl'}
	{$i=(int)0}
<div style="background-color:#cfcfcf;madding:2vw;display:flex;	flex-diretion: row;flex-wrap:wrap;justify-content: space-around;">
    {foreach from=$creators item=creator}
	{if (!empty($creator['matome']))}
	    <div id="{$creator['user_id']}" style="background-color:#cfcfcf;flex-basis:100%;">
		    <table>
		    	<tr>
			    	<td>
						<a href="/matome/matome.php?target_id={$creator['user_id']}&name={$creator['name']}&target_domain=twitter" target="_blank">
							<img alt="{$creator['description']}"
								src="{$creator['profile_image_url']}">
						</a>
			    	</td>
			    	<td>
				{if ($i != $creator['user_id']['id'])}
						{$i = $info['user_info']['id']}
						{$creator['name']}@{$info['user_info']['screen_name']}<br>
						{$creator['description']}
				{/if}
			    	</td>
		    	</tr>
		    	<tr>
			    	<td colspan="2">
				    	{foreach from=$creator['matome'] item=matome}
						・{$matome['title']}
						<br>
						　<a href="/matome/timeline.php?user_id={$creator['user_id']}&matome_id={$matome['matome_id']}&asc=0&edit=1" target="_blank">最新から</a>
						　<a href="/matome/timeline.php?user_id={$creator['user_id']}&matome_id={$matome['matome_id']}&asc=1&edit=1" target="_blank">最初から</a>
						　（{$matome['total']}）
						<br>
				   		{/foreach}
			    	</td>
		    	</tr>
		    </table>
		    <br>
		    <hr>
    	</div>
	{/if}
    {/foreach}
    </div>
<div class="clear"></div>
{include file='parts/footer.tpl'}