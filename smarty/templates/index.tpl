{include file='parts/header.tpl'}
<!-- div class="form-wrapper">
  <h1>アカウント作成フォーム</h1>
  <form method="POST" action="/auth/auth.php">
    <div class="form-item">
      <label for="account name"></label>
      <input type="text" name="name" required="required" placeholder="お好きな名前"></input>
    </div>
    <div class="form-item">
      <label for="password"></label>
      <input type="password" name="password" required="required" placeholder="パスワード"></input>
    </div>
    <div class="button-panel">
      <input type="submit" class="button" title="Sign In" value="Sign In"></input>
    </div>
  </form>
</div -->
<h4 id="title" style="width:100%;text-align:right;">ログイン中アカウント</h4>
<div style="width:100%;text-align:right;">
  {if isset($userInfo->name)}
  <a href="https://twitter.com/" target="{$target}">{$userInfo->name}</a><img src="{$userInfo->profile_image_url_https}" style="width:30px;"><br>
  {/if}
  {if !empty($pawooAccessToken)}
  <a href="https://pawoo.net/" target="{$target}">{$pawooAccount.display_name}@{$pawooAccount.username}</a><img src="{$pawooAccount.avatar}" style="width:30px;"><br>
  {/if}
</div>
<h3>タイムライン</h3>
<ul class="breadcrumb">
  {if $twitterLogin && $pawooLogin}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/home.php?domain=twitterpawoo&hs=false&thumb=true" target="{$target}"><img src="{$AppURL}/imgs/home_64.svg" style="width:24px;"> ホームTL</a>
  </li>
  {/if}
  {if $twitterLogin}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/home.php?domain=twitter&hs=false&thumb=true" target="{$target}"><img src="{$AppURL}/imgs/home_64.svg" style="width:24px;"> ホームTL(Twitter)</a>
  </li>
  {/if}
  {if $pawooLogin}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/home.php?domain=pawoo&hs=false&thumb=true" target="{$target}"><img src="{$AppURL}/imgs/home_64.svg" style="width:24px;"> ホームTL(Pawoo)</a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/local.php?domain=pawoo&hs=false&thumb=true" target="{$target}">ローカルTL(Pawoo)</a>
  </li>
  {/if}
</ul>
<br>
<ul class="breadcrumb">
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/?domain=twitter&hs=false&thumb=true&twitter_list=1120163652441481217" target="{$target}">マンガ家TL</a>
  </li>
  <!-- li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/user.php?domain=twitter&id=2656042465&hs=true&thumb=true" target="{$target}"><img src="https://pbs.twimg.com/profile_images/751972552789020672/1Ml7URFU_normal.jpg" style="width:30px;"> 横島botTL </a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/?domain=pawoo&hs=true&thumb=true" target="{$target}">Pawoo TL </a>
  </li>
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/timeline/?hs=true&thumb=true" target="{$target}">Twitter＆Pawoo TL</a>
  </li -->
</ul>
<h3>アプリ連携</h3>
<ul class="breadcrumb">
{if !$twitterLogin}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/auth/auth_twitter.php"><img src="{$AppURL}/imgs/link.svg" style="width:24px;"> Twitterと連携する</a>
  </li>
{/if}
{if empty($pawooAccessToken)}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/auth/auth_pawoo.php"><img src="{$AppURL}/imgs/link.svg" style="width:24px;"> Pawooと連携する</a>
  </li>
{/if}
{if $twitterLogin || !empty($pawooAccessToken)}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/auth/logout.php"><img src="{$AppURL}/imgs/release.svg" style="width:24px;"> アプリと連携解除</a>
  </li>
  {/if}
</ul>

<h3>検索</h3>
<div style="width:80%;margin:auto;">
		<form target="_blank"
			action="{$AppURL}/timeline/search.php" method="GET">
			<div class="msr_text_05">
				<label>検索キーワード</label>
				<input id="q" type="text" name="q" value="" placeholder="FGO" style="width:100%;">
			</div>
			<p>検索対象</p>
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
			<br>
			<div class="form_parts">
    			<div class="msr_chack_05">
    				<input id="msr_05_chack01" type="checkbox" name="hs" value="false">
    				<label for="msr_05_chack01">大きなお友達</label>
    			</div>
    			<div class="msr_chack_05">
    				<input id="msr_05_chack02" type="checkbox" name="thumb" value="false" checked>
    				<label for="msr_05_chack02">元画像を表示</label>
    			</div>
			</div>
			<div class="form_parts">
    			<p class="msr_sendbtn_05">
    				<input type="submit" value="キーワード検索">
    			</p>
    			<p class="msr_sendbtn_05">
    				<input type="submit"
    					onclick="getElementById('q').value='#'+getElementById('q').value"
    					value="ハッシュ検索">
    			</p>
			</div>
		</form>
	</div>
{if !empty($lists) && !isset($lists->errors)}
<h3>マイリスト</h3>
<ul class="mylist">
    {foreach from=$lists item=list}
    <li><a href="{$AppURL}/timeline/list.php?domain=twitter&id={$list->id}&name={$list->name}&hs=false&thumb=true" target="{$target}">{$list->name}</a></li>
	{/foreach}
</ul>
  {/if}
<h3>Twitterトレンド</h3>
<ul class="trend">
    {foreach from=$trends[0]->trends item=word}
    <li><a href="{$AppURL}/timeline/search.php?domain=twitter&q={$word->query}&hs=false&thumb=true" target="{$target}">{$word->name}</a></li>
	{/foreach}
</ul>
{include file='parts/footer.tpl'}