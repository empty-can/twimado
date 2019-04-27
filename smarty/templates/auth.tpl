{include file='parts/header.tpl'}
{if isset($userInfo->name)||!empty($pawooAccessToken)}
<h3 id="title" style="width:100%;text-align:center;">ログイン済アカウント</h3>
  <br>
  {if isset($userInfo->name)}
  <img src="{$userInfo->profile_image_url_https}" style="width:30px;">:<a href="https://twitter.com/" target="{$target}">{$userInfo->name}</a><br>
  {/if}
  {if !empty($pawooAccessToken)}
  <img src="{$pawooAccount.avatar}" style="width:30px;">:<a href="https://pawoo.net/" target="{$target}">{$pawooAccount.display_name}@{$pawooAccount.username}</a><br>
  {/if}
{/if}
<h3>連携できるアプリ</h3>
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
<br>
<ul class="breadcrumb">
  <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
  	<a href="{$AppURL}"><img src="{$AppURL}/imgs/home_64.svg" style="width:24px;"> トップページへ</a>
  </li>
</ul>
{include file='parts/footer.tpl'}