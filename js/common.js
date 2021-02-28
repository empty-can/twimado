var window_height=0;

$(window).on('load', function() {
	//画面高さ取得
	window_height = $(window).height();
});

/**
 *
 * @returns
 */
function toggleParam(key) {

	$.ajax({
		url : '//www.suki.pics/api/toggleParam.php?key='+encodeURI(key),
		type : "GET",
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
		}
	});
}


function fav(self, target_id, domain, method) {
	// return ;
	url = '//www.suki.pics/api/post/fav.php?id='+target_id+'&domain='+domain;
	toggle(self, url, domain, '#fav_icon_'+target_id, '#fav_count_'+target_id, '#fav_'+target_id, '💓', '♡', 'favon', 'favoff');
}

function rt(self, target_id, domain, method) {
	// return ;
	url = '//www.suki.pics/api/post/rt.php?id='+target_id+'&domain='+domain;
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

function changeParentHeight(iframe){
	// console.log('this');
	// console.log(this);
	//console.log('iframe');
	//console.log(iframe);
	//console.log('scrollHeight');
	//iframe.style.height = iframe.contentWindow.document.body.scrollHeight + "px";
	
	setInterval(function () {
		console.log('B');
		console.log('scrollHeight');
		console.log(iframe.contentWindow.document.body.scrollHeight);
		iframe.style.height = iframe.contentWindow.document.body.scrollHeight + "px";
	}, 1000);
}

function resizeIFrameParent(iframe_id) {
	// 子画面の要素を取得
	var iframe = document.getElementById(iframe_id);

	 
	// 親画面 iframe の高さを変更するイベント
	// 1. 子画面の読み込み完了時点で処理を行う
	// iframe.contentWindow.onload = function(){
	// 	console.log('A');
	// 	changeParentHeight(iframe);
	// };
	 
	// 2. 子画面のウィンドウサイズ変更が完了した時点で処理を行う
	// iframe.addEventListener('onload', function(){
	// 	resizeIFrameParent(iframe)
	// }, false);
	// iframe.contentWindow.addEventListener('onload', function(){
	// 	resizeIFrameParent(iframe)
	// }, false);
	iframe.contentWindow.onresize = changeParentHeight(iframe);
	// console.log(iframe.contentWindow.onload);
	
	// var timer = 0;
	//iframe.contentWindow.onresize = function () {
	//	changeParentHeight(iframe);
	  // if (timer > 0) {
	  //   clearTimeout(timer);
	  // }
	  // timer = setTimeout(function () {
	// 	console.log('B');
	  //   changeParentHeight(elm);
	  // }, 100);
	//};
}