/**
 * Created by 1195 on 2016-9-29.
 */
$(function(){

	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});

	// 删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_message_modal').find('input[name=id]').val(id);
	});

	// 批量删除
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '',i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_message_modal').find('.sAmount').text(i);
		str = str.substr(0, str.length-1);
		if(str!=''){
			$('#batch_delete_message_modal').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择模板！');
		}
		var $object = $('#batch_delete_message_modal');
		$object.find('input[name=id]').val(str);
	});
})