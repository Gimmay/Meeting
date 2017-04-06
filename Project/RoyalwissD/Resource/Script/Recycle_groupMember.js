
/**
 * Created by 1195 on 2016-12-20.
 */

$(function(){


	// 单个
	$('.recover_btn').on('click',function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#recover_groupMember').find('input[name=id]').val(id);
	});

	// 批量恢复
	$('.batch_recover_btn_confirm').on('click',function(){
		var str = '';
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str+=id+','
		});
		var s,newStr="";
		s = str.charAt(str.length-1);
		if(s==","){
			for(var i=0;i<str.length-1;i++){
				newStr+=str[i];
			}
			console.log(newStr);
		}
		$('#batch_recover_groupMember').find('input[name=id]').val(newStr);
	});

	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click',function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});

})