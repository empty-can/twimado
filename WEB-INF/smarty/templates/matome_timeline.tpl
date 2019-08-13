{include file='parts/header.tpl'}
<h3 id="title" style="width:100%;text-align:center;">{$title}</h3>
<div id="timeline">
{foreach from=$mutters item=mutter}
	{$mutter}
{/foreach}
</div>
<div id="bottom_message" style="text-align: center;"></div>
<div id="top_menu">
	<div id="timeline_menu">
		<div id="home"><a href="{$AppURL}/"><img src="{$app_url}/imgs/home_64.svg"></a></div>
		<div onclick="switchShowTweet();" ontouch="switchShowTweet();"><img id="toggleRetweet" src="{$AppURL}/imgs/retwieet.svg"></div>
		<div onclick="switchScroll();" ontouch="switchScroll();">
			<img id="horizontal" style="display:none;" src="{$AppURL}/imgs/yoko.svg">
			<img id="vertical" src="{$AppURL}/imgs/tate.svg">
		</div>
	</div>
</div>
<div id="operation" class="operation" style="">
<form>
{foreach from=$matomeInfo item=info}
	<input type="checkbox" name="matome[]" value="{$info['id']}">{$info['title']}<br>
{/foreach}
  <br>
  <input type="button" onclick="hideMyList()" value="登録">
</form>
</div>
<div id="goods" class="goods">
</div>
<div id="affiliate">
{$matomeInfo.affiliate}
</div>
<div id="matomeList" class="none" style="width:100%;padding:1vw;position:fixed; bottom:25vh;background-color:azure;">
<form id="regMatome" action="/api/matomeAPI.php" method="GET" style="font-size:large;text-align:right;">
{foreach from=$matomeList item=matome}
	{$matome['title']}<input type="radio" name="matome" value="{$matome['id']}"><br>　
{/foreach}
<input id="tweet_id" type="hidden" name="tweet_id" value="">
<input id="domain" type="hidden" name="domain" value="">
<input id="action" type="hidden" name="action" value="">
<input type="button" value="キャンセル" onclick="hideMatomeList();">
<input type="button" name="button" value="削除" onclick="if(!confirm('削除してよいですか？')) {
	return false;
} else {
	delMatome();
	hideMatomeList();
}">
<input type="button" name="button" value="登録" onclick="regMatome();hideMatomeList();">
</form>
<script>
function showMatomeList(tweet_id, domain) {
	$('#matomeList').removeClass('none');
	$('#matomeList').addClass('display');
	$('#tweet_id').val(tweet_id);
	$('#domain').val(domain);
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

// a指定したツイッターIDから始まるTLにジャンプ（暫定実装）
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