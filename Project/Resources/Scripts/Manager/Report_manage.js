/**
 * Created by qyqy on 2016-11-26.
 */


$(function(){
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_report').find('input[name=id]').val(id);
	});
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '', i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_report').find('.sAmount').text(i);
		str = str.substr(0, str.length-1);
		if(str != ''){
			$('#batch_delete_report').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		$('#batch_delete_report').find('input[name=id]').val(str);
	});
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
});