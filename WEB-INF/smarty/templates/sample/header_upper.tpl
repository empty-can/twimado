<!DOCTYPE html>
<html>
  <head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />

	<meta content="{AppURL}/favicon.png" itemprop="image">
	<link rel="shortcut icon" href="{AppURL}/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="icon" href="{AppURL}/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="apple-touch-icon" href="{AppURL}/favicon.ico" type="image/vnd.microsoft.icon">

	<!--[if IE]>
	    <script src="{AppURL}/js/old_ie/3.7.2/html5shiv.min.js"></script>
	    <script src="{AppURL}/js/old_ie/1.4.2/respond.min.js"></script>
	<![endif]-->
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="{AppURL}/css/common.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
{foreach from=$csss item=css}
    <link rel="stylesheet" type="text/css" href="{AppURL}/css/{$css}.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
    <link rel="stylesheet" type="text/css" href="{AppURL}/css/{$css}_m.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
    <link rel="stylesheet" type="text/css" href="{AppURL}/css/{$css}_pc.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
{/foreach}


