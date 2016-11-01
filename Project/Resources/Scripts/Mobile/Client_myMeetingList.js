/**
 * Created by qyqy on 2016-11-1.
 */

$(function(){
	$('.nav a').on('click', function(){
		$('.nav a').removeClass('active');
		$(this).addClass('active');
		var index = $(this).index();
		$('.list').addClass('hide').eq(index).removeClass('hide');
	})
});
