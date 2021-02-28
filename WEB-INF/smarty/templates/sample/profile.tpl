{include file='sample/header_upper.tpl'}
{include file='sample/header_lower.tpl'}
<div id="profile_banner">
	<img class="profile_banner" src="{$account->profile_banner_url}">
</div>
<div id="content">
	<div id="top" class="flx aife">
		<img class="profile_image" src="{str_replace('_normal', '_200x200', $account->profile_image_url_https)}">
		<div id="profile">
			<div id="name">{$account->name}</div>
			<div id="screen_name">@{$account->screen_name}</div>
			{$account->description}
			<div id="site">
				<a href="//ksuwabe.web.fc2.com/" target="_blank">&#x1f3e0; METAJAN</a>　
				<a href="" target="_blank">&#x1F4E7;</a>
			</div>
		</div>
	</div>
	<br>
	<hr>
	<br>
	<div id="link" class="flx aife">
		<div id="sns">
			<a href="https://www.pixiv.net/member.php?id=24517" target="_blank"><img class="site_icon" src="/imgs/sns/pixiv.png"></a>
		</div>
	</div>
	<div id="piece" class="flx">
			<a href="https://store.line.me/stickershop/author/107296" target="_blank"><img class="site_icon" src="/imgs/store/line.jpg"></a>
	</div>
	<div id="matome" class="flx">
	</div>
</div>
{include file='sample/footer.tpl'}