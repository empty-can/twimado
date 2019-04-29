{include file='parts/header.tpl'}
{$message}
{if !$login}
<form action="./" method="post">
<input type="text" name="account" maxlength="64" placeholder="アカウント名" value="{$account}" />
<input type="password" name="password" maxlength="64" placeholder="パスワード" /><br>
<button type="submit" name="button" value="login">ログイン</button>
<button type="submit" name="button" value="register">上記でアカウント登録</button>
</form>
{else}
<h3 id="title" style="width:100%;text-align:center;">ログイン中アカウント</h3>
{$account}
{/if}
{if $login}
<h3 id="title" style="width:100%;text-align:center;">連携済みアカウント</h3>
  <br>
  {if isset($twitterLoginAccount)}
  Twitter:
  <img src="{$twitterLoginAccount.profile_image_url_https}" style="width:30px;">:
  <a href="https://twitter.com/" target="{$target}">{$twitterLoginAccount.name}@{$twitterLoginAccount.screen_name}</a>
  <br>
  {/if}
  {if !empty($pawooLoginAccount)}
  Pawoo:
  <img src="{$pawooLoginAccount.avatar_static}" style="width:30px;">:
  <a href="https://pawoo.net/" target="{$target}">{$pawooLoginAccount.display_name}@{$pawooLoginAccount.username}</a>
  <br>
  {/if}
{/if}
<h3>連携できるアプリ</h3>
<ul class="breadcrumb">
{if empty($twitterLoginAccount)}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/auth/auth_twitter.php"><img src="{$AppURL}/imgs/link.svg" style="width:24px;"> Twitterと連携する</a>
  </li>
{/if}
{if empty($pawooLoginAccount)}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/auth/auth_pawoo.php"><img src="{$AppURL}/imgs/link.svg" style="width:24px;"> Pawooと連携する</a>
  </li>
{/if}
{if $login}
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/auth/logout.php"><img src="{$AppURL}/imgs/exit.svg" style="width:24px;"> ログアウトする</a>
  </li>
  <!-- li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}/auth/logout.php"><img src="{$AppURL}/imgs/release.svg" style="width:24px;"> アプリと連携解除</a>
  </li -->
{/if}
</ul>
<ul class="breadcrumb">
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}"><img src="{$AppURL}/imgs/home_64.svg" style="width:24px;"> トップページへ</a>
  </li>
</ul>
{include file='parts/footer.tpl'}