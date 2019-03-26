    <div class="mutter">
		{if $hidden_sensitive && $mutter.sensitive}
		<div class="sensitive">
		 	🔞
		</div>
		{else}
		<div id="tweet_media{$mutter.id}" class="tweet_media">
			<div class="media_box">
				<div id="imgs_wrapper{$mutter.id}" class="imgs_wrapper">
		{foreach from=$mutter.mediaURLs item=mediaURL}
		        	<div class="img_wrapper">
		                <img src="{$mediaURL}">
					</div>
		{/foreach}
				</div>
			</div>
		</div>
		{/if}
		{if count($mutter.mediaURLs) gt 1}
	    <div class="scroll">
		{for $var=1 to count($mutter.mediaURLs)}
		　◀
		{/for}
		</div>
		{/if}
	    <div class="info">
	    	<div class="icon left">
	    		<img src="{$mutter.account.profileImage}" style="width:32px">
	    		<img src="{$mutter.providerIcon}" style="width:32px">
	    	</div>
	    	<div class="profile left">
	    		<div class="account">
	    			<div class="name left">
	    				{$mutter.account.displayName}
	    				<span class="account_name">@{$mutter.account.accountName}</span>
	    			</div>
	    			<div class="date left">
	    				{$mutter.originalDate}
	    			</div>
	    			<div class="clear"></div>
	    		</div>
	    		<div class="text">
			    	{$mutter.text}<br>
			   		<a href="{$mutter.mutterURL}" target="_blank">投稿元</a>
	    		</div>
	    	</div>
	    	<div class="clear"></div>
	    </div>
			<hr>
    </div>