//var mutterQueue = [];	// APIから取得したツイート情報を一旦バッファする変数
//var mutterIds = [];		// APIから取得したツイートのID一覧
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

			if(response['mutters']['-1'] !== undefined) {
				console.log('-1'+response['mutters']['-1']);
				$("#bottom_message").html(response['mutters']['-1']);
			}
			
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
					for(key in response['mutters']) {
//						console.log(mutterIds);
						if(!mutterIds.includes(key)) {
							mutterIds.push(key);
							mutterQueue.push(response['mutters'][key]);
						} else {
							console.log('ちょうふく');
						}
					}
					
//					for (var i = 0; i < mutters_num; i++) {
//						tmp = response['mutters'][keys[i]];
//						
//						if(!mutterIds.includes(keys[i])) {
//							mutterIds.push(keys[i]);
//							mutterQueue.push(tmp);
//						} else {
//							console.log('ちょうふく');
//						}
//						console.log(tmp);
	//					if(hist.indexOf(keys[i])==-1) {
	//						hist.push(keys[i]);
	//					} else {
	//						console.log('ちょうふく');
	//					}
//					}
					
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

function fav(self, target_id, domain, method) {
	url = 'http://www.suki.pics/api/post/fav.php?id='+target_id+'&domain='+domain;
	toggle(self, url, domain, '#fav_icon_'+target_id, '#fav_count_'+target_id, '#fav_'+target_id, '💓', '♡', 'favon', 'favoff');
}

function rt(self, target_id, domain, method) {
	url = 'http://www.suki.pics/api/post/rt.php?id='+target_id+'&domain='+domain;
	toggle(self, url, domain, '#rt_icon_'+target_id, '#rt_count_'+target_id, '#rt_'+target_id, '🔂', '🔁', 'rton', 'rtoff');
}

function toggle(self, target_url, domain, icon_id, counter_id,toggle_id, on_char, off_char, on_class, off_class) {
	
//	if(domain=='pawoo' && !confirm('Pawoo はサービスの制約上、本アプリで操作のキャンセルを行えません。\r\n操作を実行しますか？'))
//		return;
	
	if($(toggle_id).val()=='on') {
		target_url = target_url+'&method=undo';
	} else if($(toggle_id).val()=='off') {
		target_url = target_url+'&method=do';
	}
	
	$.ajax({
		url : target_url,
		type : "GET",
		dataType:"json",
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			console.log("ajax通信に失敗しました");
			console.log(XMLHttpRequest);
			console.log(textStatus);
			console.log(errorThrown);
			
			console.log("ajax通信に失敗しました\r\ntextStatus:"+textStatus+"\r\nerrorThrown:"+errorThrown+"\r\nXMLHttpRequest:"+XMLHttpRequest);
		},
		success : function(response) {
			console.log("ajax通信に成功しました");
			console.log(response);
			console.log(response['error']);

			if(response==false) {
				alert('操作が失敗しました。');
			} else if(response['error']!=undefined) {
				alert(response['error']);
			} else {
				if(!isNaN($(counter_id).html()))
					target_count = parseInt($(counter_id).html(), 10);
				else
					target_count = $(counter_id).html();
				
				if($(toggle_id).val()=='on') {
					$(toggle_id).val('off');
					self.innerHTML=off_char;
					$(icon_id).removeClass(on_class);
					$(icon_id).addClass(off_class);

					if(!isNaN(target_count))
						$(counter_id).html(target_count - 1);
				} else if($(toggle_id).val()=='off') {
					$(toggle_id).val('on');
					self.innerHTML=on_char;
					$(icon_id).removeClass(off_class);
					$(icon_id).addClass(on_class);
					
					if(!isNaN(target_count))
						$(counter_id).html(target_count + 1);
				}
			}
		}
	});
}