<!DOCTYPE html>
<html>
  <head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	
	<meta content="//www.suki.pics/twimado/favicon.png" itemprop="image">
	<link rel="shortcut icon" href="//www.suki.pics/twimado/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="icon" href="//www.suki.pics/twimado/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="apple-touch-icon" href="//www.suki.pics/twimado/favicon.ico" type="image/vnd.microsoft.icon">
	
	<!--[if IE]>
	    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="{AppContext}/css/common.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M'}" />
{foreach from=$csss item=css}
    <link rel="stylesheet" type="text/css" href="{AppContext}/css/{$css}.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M'}" />
    <link rel="stylesheet" type="text/css" href="{AppContext}/css/{$css}_m.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M'}" />
    <link rel="stylesheet" type="text/css" href="{AppContext}/css/{$css}_pc.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M'}" />
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
	</script>
	
  
