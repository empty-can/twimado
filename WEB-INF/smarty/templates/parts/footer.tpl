	<div id="bottom">
	</div>
    <script src="https://www.suki.pics/js/jquery-3.3.1.min.js"></script>
{foreach from=$jss item=js}
    <script src="{AppURL}/js/{$js}.js?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}"></script>
{/foreach}
	<br>
	<div style="width:100%;text-align: right;">
		<img src="{AppURL}/imgs/seal_rapid.gif">
	</div>
  </body>
</html>