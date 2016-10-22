/**
 * Created by 1195 on 2016-10-19.
 */

var ThisObject = {
	aTemp:'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$name</a>',
}

$(function(){
	$('.coupon_list a').on('click',function(){
		var id= $(this).attr('data-id');
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
	});
	$('.coupon_list a').on('click',function(){
		var arr=[];
		$('.coupon_list a.active').each(function(){
			var id =$(this).attr('data-id');
			arr.push(id)
		})
		$('#add_receivables').find('input[name=coupon_code]').val(arr);
	});
})
