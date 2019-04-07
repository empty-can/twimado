var mutterQueue = [];	// APIから取得したツイート情報を一旦バッファする変数
var wait = false;
var wait_time = 1000;

/**
 * 画面の下まで来たらツイートを表示する関数
 * 
 * @returns
 */
setInterval( function() {
	var bottom = document.getElementById("bottom");
	
	if(bottom != null) {
		var rect =bottom.getBoundingClientRect().top;

		var mutterQueueLength = mutterQueue.length;
		
		if(window_height*4>rect) {
//			console.log('in');
//			console.log(mutterQueueLength);
			if(mutterQueueLength <= 0)
				getMutter();

			for(var i=0; mutterQueue.length>0 && i<count; i++) {
				$('#timeline').append(mutterQueue.shift());

//				if(showRT) {
//				showReTweet();
//				} else {
//				hideReTweet();
//				}

				console.log(mutterQueue.length);
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
	
	url=api+'?';
	
	for (key in params) {
		url = url+key+'=' + params[key]+'&';
	}
	
	url = url.slice( 0, -1) ;
	
	console.log(url);

	wait = true;
	$.ajax({
		url : url,
		type : "GET",
		dataType:"json",
//		data : {'params' : params},
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
			console.log(response['mutters']);

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
//			$('#timeline').after('<p>'+response['max_id']+'</p>');
			
			mutters_num = Object.keys(response['mutters']).length;

			if(mutters_num<=0) {
//				bottom.style.fontSize = '1em';
//				bottom.innerHTML = '最後まで来ました。';
//				console.log("最後まで来ました");
//				wait = true;
				wait = false;
			} else {

//				var timeline = response['timeline'];
//				var tweetNum = timeline.length;
				keys = Object.keys(response['mutters']);
				
				keys.sort(function(a,b){
					if( a > b ) return -1;
					if( a < b ) return 1;
					return 0;
				});
				
				console.log(keys);
				for (var i = 0; i < mutters_num; i++) {
					tmp = response['mutters'][keys[i]];
					mutterQueue.push(tmp);
				}
				
				console.log("取得できました");
				wait = false;
			}
		}
	});
}
