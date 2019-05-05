	<div id="bottom">
		<div style="width:100%;text-align: right;">
			<img src="{AppURL}/imgs/seal_rapid.gif">
		</div>
	</div>
    <script src="https://www.suki.pics/js/jquery-3.3.1.min.js"></script>
    <script src="https://www.suki.pics/js/common.js?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}"></script>
{foreach from=$jss item=js}
    <script src="{AppURL}/js/{$js}.js?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}"></script>
{/foreach}
	<br>
  </body>
</html>