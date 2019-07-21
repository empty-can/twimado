	<div id="bottom">
		<div style="width:100%;text-align: right;">
			<img src="{AppURL}/imgs/seal_rapid.gif">
		</div>
	</div>
    <script src="{AppURL}/js/jquery-3.3.1.min.js"></script>
    <script src="{AppURL}/js/common.js?{$smarty.now|date_format:'%Y-%m-%d_%H'}"></script>
{foreach from=$jss item=js}
    <script src="{AppURL}/js/{$js}.js?{$smarty.now|date_format:'%Y-%m-%d_%Haaa'}"></script>
{/foreach}
	<br>
  </body>
</html>