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
		url : 'https://www.suki.pics/api/toggleParam.php?key='+encodeURI(key),
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