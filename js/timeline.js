var mutterQueue = [];	// APIから取得したツイート情報を一旦バッファする変数
var mutterIds = [];		// APIから取得したツイートのID一覧
var hist = new Array(0);
var wait = false;
var wait_time = 1000;
var showRT = true;
var horizontalScroll = true;
var index = 0;


/**
 * 画面の下まで来たらツイートを表示する関数
 * 
 * @returns
 */
setInterval( function() {
	var bottom = document.getElementById("bottom");
	
	if(bottom != null) {
		var rect =bottom.getBoundingClientRect().top;
		
		if(window_height*4>rect) {
			console.log('in');

			if(mutterQueue.length <= 0)
				getMutter();

			for(var i=0; mutterQueue.length>0 && i<count; i++) {
				$('#timeline').append(mutterQueue.shift());

				console.log(mutterQueue.length);
				
				if(mutterQueue.length <= 0)
					getMutter();
			}
			
//			console.log(mutterQueueLength);
			
//			console.log(showRT);

			if(!showRT) {
				hideReTweet();
			}
			if(!horizontalScroll) {
				switch2Vertical();
			}
		}
	}
}, wait_time ) ;

/**
 * 投稿を取得する関数（Ajax）
 * 
 * @returns
 */
function getMutter() {
	
	if(wait==true)
		return;

	if(count<=0)
		return;

	//url=api+'?';
	url=api;
	bodyData = {};
	for (key in params) {
		bodyData[key] = params[key];
	}

	// confirm(bodyData);
	
	//if(ids!==undefined) {
	//	id = ids[index++]
	//	url += 'id='+id
		
	//	if(index>=ids.length)
	//		index=0;
	//} else {
	//	url = url.slice( 0, -1) ;
	//}
	
	
	console.log(url);
	
	$("#bottom_message").html('<br><img src="/imgs/reload.svg" width="48px">');

	wait = true;
	$.ajax({
		url : url,
		type : "POST",
		dataType:"json",
		data : bodyData,
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			console.log("ajax通信に失敗しました");
			console.log(XMLHttpRequest);
			console.log(textStatus);
			console.log(errorThrown);
			
			alert("ajax通信に失敗しました\r\ntextStatus:"+textStatus+"\r\nerrorThrown:"+errorThrown+"\r\nXMLHttpRequest:"+XMLHttpRequest);
			
			bottom.innerHTML = '';
			wait = true;
		},
		success : function(response) {
			console.log("ajax通信に成功しました");
			console.log(response);
//			console.log(response['mutters']);

			if(params['pawoo_oldest_id'] !== undefined) {
				params['pawoo_oldest_id'] = response['pawoo_oldest_id'];
				console.log('pawoo_oldest_id:'+response['pawoo_oldest_id']);
			}
			
			if(params['twitter_oldest_id'] !== undefined) {
				params['twitter_oldest_id'] = response['twitter_oldest_id'];
				console.log('twitter_oldest_id:'+response['twitter_oldest_id']);
			}
			
			if(params['oldest_id'] !== undefined) {
				params['oldest_id'] = response['oldest_id'];
				console.log('oldest_id:'+response['oldest_id']);
			}

			console.log('error:'+response['error']);
			
			if(response['error'] !== undefined && response['error'] != '') {
				$("#bottom_message").html("エラーが発生しました");
				wait = true;
				return;
			}
//			$('#timeline').after('<p>'+response['max_id']+'</p>');
			
			mutters_num = Object.keys(response['mutters']).length;

			if(isEnd(params['pawoo_oldest_id']) && isEnd(params['twitter_oldest_id']) && isEnd(params['oldest_id'])) {
//				bottom.style.fontSize = '1em';
//				bottom.innerHTML = '最後まで来ました。';
//				console.log("最後まで来ました");
//				wait = true;
				$("#bottom_message").html("最後まで来ました");
				
				wait = true;
			} else {

//				var timeline = response['timeline'];
//				var tweetNum = timeline.length;
				keys = Object.keys(response['mutters']);
				
				keys.sort(function(a,b){
					if( a > b ) return -1;
					if( a < b ) return 1;
					return 0;
				});

//				console.log(keys);
//				console.log(hist);
				if(mutters_num==0) {
					$("#bottom_message").html("最後まで来ました");
					wait = true;
				} else {
					for (var i = 0; i < mutters_num; i++) {
						tmp = response['mutters'][keys[i]];
						
						if(!mutterIds.includes(keys[i])) {
							mutterIds.push(keys[i]);
							mutterQueue.push(tmp);
						} else {
							console.log('ちょうふく');
						}
	//					console.log(keys[i]);
	//					if(hist.indexOf(keys[i])==-1) {
	//						hist.push(keys[i]);
	//					} else {
	//						console.log('ちょうふく');
	//					}
					}
					
					console.log("取得できました");
					wait = false;
				}
			}
		}
	});
}

/**
 * リツイートの表示非表示を切り替える
 * 
 * @returns
 */
function switchShowTweet() {
	if(showRT) {
		hideReTweet();
		showRT = false;
		$("#toggleRetweet").css("opacity", "0.5");
	} else {
		showReTweet();
		showRT = true;
		$("#toggleRetweet").css("opacity", "1.0");
	}
	
}

function showReTweet() {
	var all_retweets = document.getElementsByClassName('retweet');

	for (var i = 0; i < all_retweets.length; i++) {
		var tmp = all_retweets[i];
		tmp.style.display = 'block';
	}
}

function hideReTweet() {
	var all_retweets = document.getElementsByClassName('retweet');

	for (var i = 0; i < all_retweets.length; i++) {
		var tmp = all_retweets[i];
		tmp.style.display = 'none';
	}
}

/**
 * ツイートの横スクロールとかを切り替える
 * 
 * @returns
 */
function switchScroll() {
	if(horizontalScroll) {
		switch2Vertical();
		horizontalScroll = false;
		
		$('#horizontal').css('display','block');
		$('#vertical').css('display','none');
	} else {
		switch2Horizontal();
		horizontalScroll = true;
		
		$('#vertical').css('display','block');
		$('#horizontal').css('display','none');
	}
	
}

function switch2Horizontal() {
	var all_tweet_medias = $('.tweet_media_vertical');
	all_tweet_medias.removeClass('tweet_media_vertical');
	all_tweet_medias.addClass('tweet_media');

	var all_media_box = $('.media_box_vertical');
	all_media_box.removeClass('media_box_vertical');
	all_media_box.addClass('media_box');

	var all_imgs_wrapper = $('.imgs_wrapper_vertical');
	all_imgs_wrapper.removeClass('imgs_wrapper_vertical');
	all_imgs_wrapper.addClass('imgs_wrapper');

	var all_img_wrapper = $('.img_wrapper_vertical');
	all_img_wrapper.removeClass('img_wrapper_vertical');
	all_img_wrapper.addClass('img_wrapper');

	$('.scroll').css('display','block');
}

function switch2Vertical() {
	var all_tweet_medias = $('.tweet_media');
	all_tweet_medias.removeClass('tweet_media');
	all_tweet_medias.addClass('tweet_media_vertical');

	var all_media_box = $('.media_box');
	all_media_box.removeClass('media_box');
	all_media_box.addClass('media_box_vertical');

	var all_imgs_wrapper = $('.imgs_wrapper');
	all_imgs_wrapper.removeClass('imgs_wrapper');
	all_imgs_wrapper.addClass('imgs_wrapper_vertical');

	var all_img_wrapper = $('.img_wrapper');
	all_img_wrapper.removeClass('img_wrapper');
	all_img_wrapper.addClass('img_wrapper_vertical');
	
	$('.scroll').css('display','none');
}

function isEnd(params) {
	console.log(params);
	return (params == undefined || params == -1)
}

function showMyList() {
	$('#mylist').css('right','0');
}
function hideMyList() {
	$('#mylist').css('right','-340px');
}