/**
 * Created by qyqy on 2016-9-13.
 */

$(function(){
	$('#updateBackground').on('change', function(){
		$('input[name=requestType]').val('upload_logo');
		$('#submit_logo').trigger('click');
	});
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 提交表单数据
	$('#form .btn-save').on('click', function(){
		if(isEmpty()){
			var data = $('#form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, 1);
						ManageObject.object.toast.onQuasarHidden(function(){
							location.href = r.nextPage;
						});
					}else{
						ManageObject.object.toast.toast(r.message, 2);
					}
				}
			});
		}
	});
});
function isEmpty(){
	var status = true;
	$('.form-group').each(function(){
		if($(this).find('input').hasClass('necessary')){
			console.log('1');
			var text = $(this).find('label ').text();
			text     = text.substring(0, text.length-1); //去掉最后一个字符
			text     = text.substring(1, text.length); //去掉第一个一个字符
			if($(this).find('.form-control').val() == ''){
				ManageObject.object.toast.toast(text+"不能为空");
				$(this).find('.form-control').focus();
				status = false;
				return false;
			}
		}
	});
	return !!status;
}
function upLoadLogo(){
	var data = new FormData($('#form')[0]);
	$.ajax({
		url        :'',
		type       :'POST',
		data       :data,
		dataType   :'JSON',
		cache      :false,
		processData:false,
		contentType:false
	}).done(function(data){
		if(data.status){
			ManageObject.object.toast.toast(data.message);
			$('input[name=logo]').val(data.data.filePath);
			$('.logo_not').hide();
			$('.logo_wp img').attr('src', data.data.filePath);
		}else{
			ManageObject.object.toast.toast(data.message);
		}
	});
	$('input[name=requestType]').val('modify');
	return false;
}