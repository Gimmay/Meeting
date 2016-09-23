/**
 * Created by 1195 on 2016-9-22.
 */

$(function(){
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click',function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
});