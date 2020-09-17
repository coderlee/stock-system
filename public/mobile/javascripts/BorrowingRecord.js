$(function () {
	// FastClick.attach(document.body);
	// 请求页面数据
	initDataTokens({
		url: 'wallet/hzhistory'
	}, function (res) {
		if (res.type == 'ok') {
			var list = res.message.data;
			var html = '';
			if (list.length > 0) {
				$('.nodata').hide();
				for (let i in list) {
					html += '<li class="flex">';
					html += '<p>' + list[i].info + '</p>';
					html += '<p>' + iTofixed(list[i].value,2) + '</p>';
					html += '<p>' + list[i].created_time + '</p>';
					html += '</li>';
				}
				$('.list').append(html);
			} else {
				$('.nodata').show()
			}
		}
	})
	$('.complete').click(function () {
		$('#mask1').show();
		$('#genre').animate({
			bottom: '0'
		}, 500);
	})

	$('#genre>ul>li>p').click(function () {
		$('#mask1').hide();
		$(this).addClass('p').siblings().removeClass('p');
		$('#genre').animate({
			bottom: '-40%'
		}, 500);
		$('.complete>span').html($(this).html());
	})
	$('.cancel').click(function () {
		$('#mask1').hide();
		$('#genre').animate({
			bottom: '-40%'
		}, 500);
	})
	$('input').blur(function () {
		setTimeout(function () {
			document.body.scrollTop = document.body.scrollHeight;
		}, 300);
	})
	$('select').change(function () {
		setTimeout(function () {
			document.body.scrollTop = document.body.scrollHeight;
		}, 300);
	})
})