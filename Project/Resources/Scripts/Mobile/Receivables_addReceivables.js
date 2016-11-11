/**
 * Created by qyqy on 2016-11-10.
 */

$(function(){
	$('.coupon_list a').on('click', function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
		var arr = [];
		$('.coupon_list a.active').each(function(){
			var id = $(this).attr('data-id');
			arr.push(id);
		});
		$('input[name=coupon_id]').val(arr);
	});
});
