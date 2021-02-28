<!DOCTYPE html>
<html>
  <head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<meta name="robots" content="noindex,nofollow">

{if !empty($twitter_card)}
	<meta name="twitter:card" content="summary_large_image" >
{/if}
    <meta name="twitter:site" content="Suki Pics：{$title}" >
    <meta property="og:url" content="{$smarty.server.REQUEST_URI}" >
    <meta property="og:title" content="{$title}" >
    <meta property="og:description" content="Suki Pics：Twitter画像特化の検索サイト" >
{if !empty($og_image)}
	<meta property="twitter:image" content="https://www.suki.pics/media/{$og_image}" >
	<meta property="og:image" content="https://www.suki.pics/media/{$og_image}" >
{/if}
    <meta name="twitter:site" content="@illust_seaker" >
    <meta name="twitter:creator" content="@illust_seaker" >
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Suki Pics" >

	<meta content="{AppURL}/favicon.png" itemprop="image">
	<link rel="shortcut icon" href="{AppURL}/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="icon" href="{AppURL}/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="apple-touch-icon" href="{AppURL}/favicon.ico" type="image/vnd.microsoft.icon">

	<!--[if IE]>
	    <script src="{AppURL}/js/old_ie/3.7.2/html5shiv.min.js"></script>
	    <script src="{AppURL}/js/old_ie/1.4.2/respond.min.js"></script>
	<![endif]-->
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="{$AppURL}/css/magnific-popup.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="{AppURL}/js/jquery.magnific-popup.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{$AppURL}/css/common.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
    <!-- link rel="stylesheet" type="text/css" href="{$AppURL}/css/common.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" / -->
{foreach from=$csss item=css}
    <link rel="stylesheet" type="text/css" href="{$AppURL}/css/{$css}.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
    <link rel="stylesheet" type="text/css" href="{$AppURL}/css/{$css}_m.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
    <link rel="stylesheet" type="text/css" href="{$AppURL}/css/{$css}_pc.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
{/foreach}
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139119333-1"></script>
	<script>
	{literal}
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-139119333-1');
	{/literal}
	$(document).ready(function() {
		$(function () {
			$('.popup-modal').magnificPopup({
				type: 'inline',
				preloader: false,
				showCloseBtn: true,
				closeOnContentClick: false,
				closeOnBgClick: true,
				showCloseBtn: true,
				enableEscapeKey: true,
				fixedBgPos: false
			});
			$(document).on('click', '.popup-modal-dismiss', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});
		});
	});
	</script>
    </head>
  <body>
	<script type="text/javascript">
	{if isset($embedded_js)}
	{$embedded_js}
	{/if}
	{if isset($embedded_js_params)}
	{$embedded_js_params}
	{/if}
	{if isset($embedded_mutters)}
	{$embedded_mutters}
	{/if}
	</script>


