	<div id="bottom" style="text-align:right;">
		<!-- div style="width:100%;text-align: right;">
			<img src="{AppURL}/imgs/seal_rapid.gif">
		</div -->
		<!-- span id="ss_gmo_img_wrapper_100-50_image_ja">
			<a href="https://jp.globalsign.com/" target="_blank" rel="nofollow">
				<img alt="SSL　GMOグローバルサインのサイトシール" border="0" id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_100-50_ja.gif">
			</a>
		</span -->
	</div>
    <script src="{AppURL}/js/jquery-3.3.1.min.js"></script>
    <script src="{AppURL}/js/common.js?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}"></script>
{foreach from=$jss item=js}
    <script src="{AppURL}/js/{$js}.js?{$smarty.now|date_format:'%Y-%m-%d_%H:%M:%S'}"></script>
{/foreach}
	<br>
	<!-- script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gmogs_image_100-50_ja.js" defer="defer"></script -->
	<div style="width:100%;text-align:center;color:gray;">&copy SukiPics</div>
  </body>
</html>