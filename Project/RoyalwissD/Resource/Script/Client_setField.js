/**
 * Created by Iceman on 2017-3-10.
 */

$(function(){
	$('.all_check_excal').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item_excel').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item_excel').find('.icheckbox_square-green').removeClass('checked');
		}
	});
});
