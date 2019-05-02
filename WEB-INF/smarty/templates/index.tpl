{include file='parts/header.tpl'}
<div id="menu" class="flx jcc jcsb aic">
	<div>
{if !empty($account)}
		<div>
			<h4 id="title" style="width: 100%; text-align: left;">ログイン中:{$account}</h4>
		</div>
		<div class="flx">
	{if $twitterLogin}
			<div style="margin-right: 1vw;">
				 <a href="https://twitter.com/" target="{$target}"
					alt="Twitter:{$twitterLoginAccount.name}@{$twitterLoginAccount.screen_name}">
					<img src="{$twitterLoginAccount.profile_image_url_https}"
					style="width: 24px;" class="circle">
				</a>
			</div>
	{/if}
	{if $pawooLogin}
			<div style="margin-right: 1vw;">
				 <a href="https://pawoo.net/" target="{$target}"
					alt="Pawoo:{$pawooLoginAccount.username}@{$pawooLoginAccount.username}">
					<img src="{$pawooLoginAccount.avatar_static}" style="width: 24px;"
					class="circle">
				</a>
			</div>
	{/if}
		</div>
{/if}
	</div>
	<div>
{if empty($account)}
		<div style="margin-bottom: 10px;">
			<form action="/auth/" method="post">
				<input type="text" name="account" size="12" maxlength="64" placeholder="アカウント名" value="{$account}" />
				<input type="password" size="12" name="password" maxlength="64" placeholder="パスワード" />
				<button type="submit" name="button" value="login">ログイン</button>
				<button type="submit" name="button" value="register">登録</button>
			    &nbsp; 
			</form>
			<span style="color:red;">{$message}</span>
		</div>
{else}
		<div class="flx jcfe">
			<a style="display:block;" href="{$AppURL}/auth/logout.php">
				<img src="{$AppURL}/imgs/exit.svg" style="background-color: lightgray; width: 30px; padding: 2px;">
			</a> 
		</div>
{/if}
		<div class="flx jcfe">
			<div>
		{if $hs=='true'}
					<input type="checkbox" name="hs" value="false" onchange="toggleParam('hs');" checked>
		{else}
			 		<input type="checkbox" name="hs" value="false" onchange="toggleParam('hs');">
		{/if}
			セーフサーチ
			</div>
			&nbsp; 
			<div>
		{if $thumb=='false'}
				<input type="checkbox" name="thumb" value="false" onchange="toggleParam('thumb');" checked>
		{else}
				<input type="checkbox" name="thumb" value="false"　onchange="toggleParam('thumb');">
		{/if}高画質
			&nbsp; 
			</div>
		</div>
	</div>
</div>




<div id="content">
<h1>検索</h1>
<div class="flex_parent">
<div class="icon">
	<div class="img circle">
		<a href="https://www.suki.pics/timeline/search.php?q=%23FGO" target="{$target}">
			<img src="https://pbs.twimg.com/profile_images/1034364986041163776/tRqcymzd_400x400.jpg">
		</a>
	</div>
	<div class="description">#FGO</div>
</div>
<div class="icon">
	<div class="img circle">
		<a href="https://www.suki.pics/timeline/search.php?q=%23艦これ" target="{$target}">
			<img src="https://pbs.twimg.com/profile_images/1123411827604377601/DOj9K64n_400x400.png">
		</a>
	</div>
	<div class="description">#艦これ</div>
</div>
<div class="icon">
	<div class="img circle">
		<a href="https://www.suki.pics/timeline/search.php?q=%23アズレン" target="{$target}">
			<img src="https://pbs.twimg.com/profile_images/864408527640514560/i-1Y1zSK_400x400.jpg">
		</a>
	</div>
	<div class="description">#アズレン</div>
</div>
<div class="icon">
	<div class="img circle">
		<a href="https://www.suki.pics/timeline/search.php?q=%23アイマス" target="{$target}">
			<img src="https://pbs.twimg.com/profile_images/980698945797017601/UI0LycLM_400x400.jpg">
		</a>
	</div>
	<div class="description">#アイマス</div>
</div>
</div>
<h2>検索ボックス</h2>
<div style="width:100%;margin:auto;">
		<form target="_blank" style="width:100%;padding:0px;"
			action="{$AppURL}/timeline/search.php" method="GET">
			<div class="msr_text_05">
				<label></label>
				<input id="q" type="text" name="q" value="" placeholder="検索キーワード:例 FGO" style="display:block;width:100%;max-width:256px;margin:auto;">
			</div>
			<div class="form_parts">
    			<div>
    				<input type="radio" name="domain" value="twitter">Twitter 
    			</div>
    			　
    			<div>
    				<input type="radio" name="domain" value="pawoo">Pawoo
    			</div>
    			　
    			<div>
    				<input type="radio" name="domain" value="twitterpawoo" checked>両方
    			</div>
    		</div>
			<div class="form_parts">
				<button type="submit" >キーワード</button>
				&nbsp; 
				<button type="submit" onclick="getElementById('q').value='#'+getElementById('q').value">ハッシュタグ</button>
			</div>
		</form>
	</div>


{if !($twitterLogin && $pawooLogin)}
<h1>アプリ連携</h1>
<div class="flex_parent">
<div class="icon">
{if !$twitterLogin}
	<div class="img circle">
		<a href="{$AppURL}/auth/auth_twitter.php">
			<img src="{$AppURL}/imgs/link.svg">
		</a>
	</div>
	<div class="description">Twitterと連携</div>
{/if}
</div>
<div class="icon">
{if !$pawooLogin}
	<div class="img circle">
		<a href="{$AppURL}/auth/auth_pawoo.php">
			<img src="{$AppURL}/imgs/link.svg">
		</a>
	</div>
	<div class="description">Pawooと連携</div>
{/if}
</div>
</div>
{/if}


<h1>タイムライン</h1>
<div class="flex_parent">
  {if $twitterLogin && $pawooLogin}
<div class="icon">
	<div class="img circle">
		<a href="{$AppURL}/timeline/home.php?domain=twitterpawoo" target="{$target}">
			<img src="{$AppURL}/imgs/home_64.svg">
		</a>
	</div>
	<div class="description">統合ホームTL</div>
</div>
  {/if}
  {if $twitterLogin}
<div class="icon">
	<div class="img circle">
	
		<a href="{$AppURL}/timeline/home.php?domain=twitter" target="{$target}">
			<img src=https://i1.wp.com/marsfallpodcast.com/wp-content/uploads/2017/09/Twitter-Download-PNG.png>
		</a>
	</div>
	<div class="description">ホームTL</div>
</div>
  {/if}
  {if $pawooLogin}
<div class="icon">
	<div class="img circle">
		<a href="{$AppURL}/timeline/home.php?domain=twitterpawoo" target="{$target}">
			<img src="https://static-s.aa-cdn.net/img/ios/1229070679/a551f2dfa37f77de3618b058dcd90b0e?v=1">
		</a>
	</div>
	<div class="description">ホームTL</div>
</div>
  {/if}
<div class="icon">
	<div class="img circle">
		<a href="{$AppURL}/timeline/local.php?domain=pawoo" target="{$target}">
			<img src="https://static-s.aa-cdn.net/img/ios/1229070679/a551f2dfa37f77de3618b058dcd90b0e?v=1">
		</a>
	</div>
	<div class="description">ローカルTL</div>
</div>
</div>


<h1>公開リスト</h1>
<div class="flex_parent">
<div class="icon">
	<div class="img circle">
		<a href="{$AppURL}/timeline/list.php?domain=twitter&twitter_list=1120163652441481217&name=マンガ家" target="{$target}">
			<img src="{$AppURL}/imgs/list.svg">
		</a>
	</div>
	<div class="description">マンガ家</div>
</div>
<div class="icon">
	<div class="img circle">
		<a href="https://www.suki.pics/timeline/list.php?domain=twitter&list_id=1120163800961826816&name=イラストレーター" target="{$target}">
			<img src="{$AppURL}/imgs/list.svg">
		</a>
	</div>
	<div class="description">イラストレーター</div>
</div>
<div class="icon">
	<div class="img circle">
		<a href="https://www.suki.pics/timeline/list.php?domain=twitter&list_id=1120165702248198144&name=おもしろ系" target="{$target}">
			<img src="{$AppURL}/imgs/list.svg">
		</a>
	</div>
	<div class="description">おもしろ系</div>
</div>
</div>

{if !empty($lists) && !isset($lists->errors)}
<h1>Twitter マイリスト</h1>
<div class="flex_parent">
    {foreach from=$lists item=list}
<div class="icon">
	<div class="img circle">
		<a href="{$AppURL}/timeline/list.php?domain=twitter&list_id={$list->id}&name={$list->name}" target="{$target}">
			<img src="{$AppURL}/imgs/list.svg">
		</a>
	</div>
	<div class="description">{$list->name}</div>
</div>
	{/foreach}
</div>
{/if}
<br>
<h1>Twitterトレンド</h1>
<div class="trend_parent">
    {foreach from=$trends[0]->trends item=word}
<div class="trend">
<a href="{$AppURL}/timeline/search.php?domain=twitter&q={$word->query}&hs=false&thumb=true" target="{$target}">{$word->name}</a>
</div>
	{/foreach}
</div>
</div>
{include file='parts/footer.tpl'}