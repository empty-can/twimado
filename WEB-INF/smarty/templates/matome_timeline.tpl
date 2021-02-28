{include file='parts/header.tpl'}
<h3 id="title" style="width:100%;text-align:center;">{$title}</h3>
<div id="timeline" class="flx fww jcsa">
{foreach from=$mutters item=mutter}
	{$mutter}
{/foreach}
</div>
<div id="bottom_message" style="text-align: center;"></div>
<div id="top_menu">
	<div id="timeline_menu">
		<div id="home"><a href="{$AppURL}/"><img src="{$AppURL}/imgs/home_64.svg"></a></div>
		<div onclick="switchShowTweet();" ontouch="switchShowTweet();"><img id="toggleRetweet" src="{$AppURL}/imgs/retwieet.svg"></div>
		<div onclick="switchScroll();" ontouch="switchScroll();">
			<img id="horizontal" src="{$AppURL}/imgs/yoko.svg">
			<img id="vertical" style="display:none;" src="{$AppURL}/imgs/tate.svg">
		</div>
	</div>
</div>
<div id="operation" class="operation" style="display:none;">
	<form>
	{foreach from=$matomeInfo item=info}
		<input type="radio" name="matome[]" value="{$info['matome_id']}">{$info['title']}<br>
	{/foreach}
	  <br>
	  <input type="button" onclick="hideMyList()" value="登録">
	</form>
</div>
<div id="goods" class="goods">
</div>
{if !empty($matomeInfo.affiliate)}
<!-- div id="affi_bottom">
	<div id="affiliate">
		<label for="1">　書籍</label>
		<input type="checkbox" id="1"/>
		<div class="hidden_show flx jcc aic">
			{$matomeInfo.affiliate}
		</div>
	</div>
</div -->
{/if}
<div id="matomeList" class="none" style="width:80vw;max-height:70vh;margin-left:10vw;padding:5vw;position:fixed; bottom:10vh;background-color:azure;">
<form id="regMatome" action="/api/matomeAPI.php" method="GET" style="width:100%;font-size:large;">

<div style="max-width:80vw;max-height:60vh;overflow-y:scroll;">
<table style="table-layout: fixed;width:100%;">
{foreach from=$matomeList item=matome}
	<tr style="width:100%;max-width:80vw;"><td style="max-width:75vw;text-align:end;white-space:nowrap;overflow-x:hidden;text-overflow: ellipsis;margin-right:0;">{$matome['title_short']}</td><td style="width:5vw;"><input type="radio" name="matome" value="{$matome['matome_id']}"></tr>
{/foreach}
</table>
</div>

<input id="tweet_id" type="hidden" name="tweet_id" value="">
<input id="user_id" type="hidden" name="user_id" value="">
<input id="domain" type="hidden" name="domain" value="">
<input id="action" type="hidden" name="action" value="">

<div class="flx jcfe">
<input style="display:block;" type="button" value="キャンセル" onclick="hideMatomeList();">　
<input style="display:block;" type="button" name="button" value="削除" onclick="if(!confirm('削除してよいですか？')) {
	return false;
} else {
	delMatome();
	hideMatomeList();
}">　
<input style="display:block;" type="button" name="button" value="登録" onclick="regMatome();hideMatomeList();" autofocus>
</div>
</form>
<script>
function showMatomeList(tweet_id, user_id, domain) {

	if(edit==true) {
		$('#matomeList').removeClass('none');
		$('#matomeList').addClass('display');
		$('#tweet_id').val(tweet_id);
		$('#user_id').val(user_id);
		$('#domain').val(domain);
	}
}

function hideMatomeList() {
	$('#matomeList').removeClass('display');
	$('#matomeList').addClass('none');
	$('#tweet_id').val('');
	$('#domain').val('');
	$('#action').val('');
}

function regMatome() {
	$('#action').val('reg');
	$form = $('#regMatome');

    $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: $form.serialize(),

        complete: function(xhr, textStatus) {
        },

        success: function(result, textStatus, xhr) {
        	console.log(result);
        	if(result.length>50) {
            	alert('既に登録済みの画像の可能性があります。');
        	}
			hideMatomeList();
            console.log('OK');
        },

        error: function(xhr, textStatus, error) {
            console.log('NG...');
        }
    });
}

function delMatome() {
	$('#action').val('del');
	$form = $('#regMatome');
	var tweet_id = $('#tweet_id').val();

    $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: $form.serialize(),

        complete: function(xhr, textStatus) {
        },

        success: function(result, textStatus, xhr) {
        	console.log(result);
			hideMatomeList();
        	console.log('#mutter'+tweet_id);
			$('#mutter'+tweet_id).remove();
            console.log('OK');
        },

        error: function(xhr, textStatus, error) {
            console.log('NG...');
        }
    });
}

// 指定したツイッターIDから始まるTLにジャンプ（暫定実装）
function shiori(twitter_id) {
	var query = '{$smarty.server.REQUEST_URI}';
	query = query.replace(/&twitter_oldest_id=[0-9]*/, '');
	query = query.replace(/&twitter_latest_id=[0-9]*/, '');
	query += '&twitter_oldest_id='+twitter_id+'&twitter_latest_id='+twitter_id;
	open( query, '_blank' ) ;
}
</script>
</div>
{include file='parts/footer.tpl'}