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

	// 单个员工删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_signPlace').find('input[name=id]').val(id);
	});

	// 批量删除员工
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '';
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+','
		});
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(str!=''){
			$('#batch_delete_signPlace').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择签到点！');
		}
		$('#batch_delete_signPlace').find('input[name=id]').val(newStr);
	});
});