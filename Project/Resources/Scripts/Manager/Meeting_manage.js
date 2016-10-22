/**
 * Created by qyqy on 2016-9-12.
 */
$(function(){

	// 单个会议删除
	$('.delete_btn').on('click',function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_meeting').find('input[name=id]').val(id);
	});

	// 批量删除会议
	$('.batch_delete_btn_confirm').on('click',function(){
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
		}
		if(str!=''){
			$('#batch_delete_meeting').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择会议！');
		}
		$('#batch_delete_meeting').find('input[name=id]').val(newStr);
	});

	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click',function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 选择消息模板做提交获取数据
	$('.mes_btn').on('click', function(){
		var mid = $(this).parent().attr('data-id');
		$('#choose_message').find('input[name=id]').attr('value', mid).val(mid);
		Common.ajax({
			data:{requestType:'get_message_temp', id:mid},
			callback:function(data){
				console.log(data);
				ManageObject.object.signMessageSelect.setValue(0);
				ManageObject.object.signMessageSelect.setHtml('');
				ManageObject.object.antiSignMessageSelect.setValue(0);
				ManageObject.object.antiSignMessageSelect.setHtml('');
				ManageObject.object.receivablesMessageSelect.setValue(0);
				ManageObject.object.receivablesMessageSelect.setHtml('');
				for(var i = 0; i<data.length; i++){
					switch(parseInt(data[i]['assign_type'])){
						case 1:
							ManageObject.object.signMessageSelect.setValue(data[i]['message_id']);
							ManageObject.object.signMessageSelect.setHtml(data[i]['name']);
							break;
						case 2:
							ManageObject.object.antiSignMessageSelect.setValue(data[i]['message_id']);
							ManageObject.object.antiSignMessageSelect.setHtml(data[i]['name']);
							break;
						case 3:
							ManageObject.object.receivablesMessageSelect.setValue(data[i]['message_id']);
							ManageObject.object.receivablesMessageSelect.setHtml(data[i]['name']);
							break;
					}
				}
			}
		});
	});

	// 预览
	$('.mes_preview_btn').on('click',function(){
		var id =$(this).parents('.form-group').find('input[type=hidden]').val();
		Common.ajax({
			data:{requestType:'get_message', id: id},
			callback:function(data){
				console.log(data);
				$('.mes_preview').show().text(data.data.context);
			}
		});
	});
	
});


/*
function checkIsEmpty(){
	var $selected_sign_mes   = $('#selected_sign_mes');
	var $selected_unti_sign_mes = $('#selected_unti_sign_mes');
	var $selected_receivables_mes = $('#selected_receivables_mes');
	if($selected_sign_mes.text() == ''){
		ManageObject.object.toast.toast("请选择签到消息模板");
		$selected_sign_mes.focus();
		return false;
	}
	if($selected_unti_sign_mes.text() == ''){
		ManageObject.object.toast.toast("请选择取消签到消息模板");
		$selected_unti_sign_mes.focus();
		return false;
	}
	if($selected_receivables_mes.text() == ''){
		ManageObject.object.toast.toast("请选择收款消息模板");
		$selected_receivables_mes.focus();
		return false;
	}
	return true;
}
*/

