<!DOCTYPE html>
<html>
  <head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<!--[if IE]>
	    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="{AppContext}/css/common.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
{foreach from=$csss item=css}
    <link rel="stylesheet" type="text/css" href="{AppContext}/css/{$css}.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
    <link rel="stylesheet" type="text/css" href="{AppContext}/css/{$css}_m.css?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}" />
{/foreach}
    </head>
  <body>
	<script type="text/javascript">
	{$embedded_js}
	{$embedded_js_params}
	</script>
  
