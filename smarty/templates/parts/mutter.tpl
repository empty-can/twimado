	<div class="mutter">
 		{if !$mutter.isRe}{/if}
		{if $hs && $mutter.sensitive}
		<div class="sensitive">
		 	🔞
		</div>
		{else}
		<div id="tweet_media{$mutter.id}" class="tweet_media">
			<div class="media_box">
				<div id="imgs_wrapper{$mutter.id}" class="imgs_wrapper">
		{foreach from=$mutter.thumbnailURLs item=mediaURL}
		        	<div class="img_wrapper">
		        		{generateMediaLinkTag($mediaURL)}
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
	    		<a href="{$mutter.mutterURL}" target="_blank"><img src="{$mutter.providerIcon}" style="width:32px"></a>
	    	</div>
	    	
	    	<div class="profile left">
	    		<div class="account">
	    			<div class="name">
	    				<a href="{$app_url}user_timeline/?domain={$mutter.domain}&id={$mutter.account.id}&hs={var_export($hs)}" target="_blank">
		    				{$mutter.account.displayName}
	    				</a>
		    			<span class="account_name">@{$mutter.account.accountName}</span>
	    			</div>
	    			<div class="date">
	    				{date4timeline($mutter.originalDate)}
	    			</div>
	    			<div class="rt">
	    			{if $mutter.isRe}
	    				<img src="{$app_url}imgs/repeat-64.png">
	    				<a href="{$app_url}user_timeline/?domain={$mutter.domain}&id={$mutter.retweeter.id}&hs={var_export($hs)}" target="_blank">
		    				{$mutter.retweeter.displayName}
	    				</a>
	    				({date4timeline($mutter.date)})<br>
	    			{/if}
	    			</div>
	    			<div class="clear"></div>
	    		</div>
	    		<div class="text">
			    	{$mutter.text}
	    		</div>
	    	</div>
	    	<div class="clear"></div>
	    </div>
			<hr>
    </div>