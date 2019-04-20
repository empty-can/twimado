	<div id="bottom">
	</div>
{foreach from=$jss item=js}
    <script src="{AppContext}/js/{$js}.js?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}"></script>
{/foreach}
  <body/>
<html/>