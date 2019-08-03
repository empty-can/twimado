{if $mutter.isRe}
	{if (($mutter.account.id == $mutter.retweeter.id))}
		{if ((($mutter.time-$mutter.originalDate)/86400)<1.0)}
		 	<div class="mutter retweet owner recent">
		{else}
		 	<div class="mutter retweet owner">
		{/if}
	{else}
 		<div class="mutter retweet">
	{/if}
{else}
 	<div class="mutter">
{/if}
		{if $hs && $mutter.sensitive}
		<div class="sensitive">
		 	<br>
		 	このメディアにはセンシティブな内容が含まれている可能性があり云々<br>
		 	<br>
		</div>
		{else}
		<div id="tweet_media{$mutter.id}" class="tweet_media">
			<div class="media_box">
				<div id="imgs_wrapper{$mutter.id}" class="imgs_wrapper">
		{if $thumb}
			{foreach from=$mutter.media item=medium}
		        	<div class="img_wrapper">
		        	    {if $mutter.isImg}
		            	<img src="{$medium.thumb}" alt="{$medium.url}"/>
		        	    {else if $mutter.isVideo}
		        	    <video src="{$medium.url}" poster="{$medium.thumb}" preload="metadata" controls ></video>
		        	    {else if $mutter.isObject}
		        	    <object data="{$medium.url}" type="image/png"></object>
		        	    {/if}
					</div>
			{/foreach}
		{else}
			{foreach from=$mutter.media item=medium}
		        	<div class="img_wrapper">
		        	    {if $mutter.isImg}
		            	<img src="{$medium.url}" />
		        	    {else if $mutter.isVideo}
		        	    <video src="{$medium.url}" poster="{$medium.thumb}" preload="metadata" controls ></video>
		        	    {else if $mutter.isObject}
		        	    <object data="{$medium.url}" type="image/png"></object>
		        	    {/if}
					</div>
			{/foreach}
		{/if}
				</div>
			</div>
		</div>
		{/if}
		{if count($mutter.media) gt 1}
	    <div class="scroll">
		{for $var=1 to count($mutter.media)}　◀{/for}
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
	    				<a href="{$app_url}/timeline/user.php?domain={$mutter.domain}&target_id={$mutter.account.id}&hs={var_export($hs)}&thumb={var_export($thumb)}" target="_blank">
		    				{$mutter.account.displayName}
	    				</a>
		    			<span class="account_name">@{$mutter.account.accountName}</span>
	    			</div>
	    			<div class="date">
	    				{date4timeline($mutter.originalDate)}
	    			</div>
	    			<div class="rt">
	    			{if $mutter.isRe}
	    				<img class="repeat" src="{$app_url}/imgs/retwieet.svg">
	    				<a href="{$app_url}/timeline/user.php?domain={$mutter.domain}&target_id={$mutter.retweeter.id}&hs={var_export($hs)}&thumb={var_export($thumb)}" target="_blank">
		    				{$mutter.retweeter.displayName}
	    				</a>
	    				({date4timeline($mutter.date)})<br>
	    			{/if}
	    			</div>
	    			<div class="clear"></div>
	    		</div>
	    		<div class="text">
	    			<!--
	    			{$mutter.id}<br>
	    			{$mutter.originalId}<br>
	    			 -->
			    	{$mutter.text}
	    		</div>
	    	<div class="mutter_menu" style="display:flex;justify-content:space-around;align-items:center;widht:100%;height:32px;">
	    		<div>
	    			{if $mutter.retweeted}
	    			<span id="rt_icon_{$mutter.id}" class="rtoff" onclick="rt(this,'{$mutter.id}','{$mutter.domain}','undo');">
	    				&#x1f502;
	    			</span>
	    			<input type="hidden" id="rt_{$mutter.id}" value="on">
	    			{else}
	    			<span id="rt_icon_{$mutter.id}" class="rton" onclick="rt(this,'{$mutter.id}','{$mutter.domain}','do');">
	    				&#x1f501;
	    			</span>
	    			<input type="hidden" id="rt_{$mutter.id}" value="off">
	    			{/if}
	    			<span id="rt_count_{$mutter.id}">{$mutter.reCount}</span>
	    		</div>
	    		<div>
	    			{if $mutter.favorited}
	    			<span id="fav_icon_{$mutter.id}" class="favoff" onclick="fav(this,'{$mutter.id}','{$mutter.domain}','undo');">
	    				&#x1f493;
	    			</span>
	    			<input type="hidden" id="fav_{$mutter.id}" value="on">
	    			{else}
	    			<span id="fav_icon_{$mutter.id}" class="favon" onclick="fav(this,'{$mutter.id}','{$mutter.domain}','do');">
	    				&#x2661;
	    			</span>
	    			<input type="hidden" id="fav_{$mutter.id}" value="off">
	    			{/if}
	    			<span id="fav_count_{$mutter.id}">{$mutter.favCount}</span>
	    		</div>
	    		<!-- div onclick="showMyList();">&#x2b50;</div>
	    		<div onclick="confirm('しおり機能は未実装です');">&#x1f516;</div -->
	    	</div>
	    	</div>
	    	<div class="clear"></div>
	    </div>
		<hr>
    </div>