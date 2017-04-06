/**
 * Created by qyqy on 2016-9-13.
 */

$(function(){
	$('#updateBackground').on('change', function(){
		$('input[name=requestType]').val('upload_image');
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
});
function checkIsEmpty(){
	var $name            = $('#meeting_name');
	//	var $host = $('#meeting_host');
	var $start_time      = $('#meeting_start_time');
	var $end_time        = $('#meeting_end_time');
	var $sign_start_time = $('#meeting_sign_start_time');
	var $sign_end_time   = $('#meeting_sign_end_time');
	//	var $director = $('#selected_director');
	if($name.val() == ''){
		CreateObject.object.toast.toast("会议名称不能为空");
		$name.focus();
		return false;
	}
	//	if($host.val() == ''){
	//		CreateObject.object.toast.toast("主办方不能为空");
	//		$host.focus();
	//		return false;
	//	}
	if($start_time.val() == ''){
		CreateObject.object.toast.toast("开始时间不能为空");
		$start_time.focus();
		return false;
	}
	if($end_time.val() == ''){
		CreateObject.object.toast.toast("结束时间不能为空");
		$end_time.focus();
		return false;
	}
	if($sign_start_time.val() == ''){
		CreateObject.object.toast.toast("签到开始时间不能为空");
		$start_time.focus();
		return false;
	}
	if($sign_end_time.val() == ''){
		CreateObject.object.toast.toast("签到结束时间不能为空");
		$end_time.focus();
		return false;
	}
	//	if($director.text() == ''){
	//		CreateObject.object.toast.toast("负责人不能为空");
	//		$director.focus();
	//		return false;
	//	}
	return true;
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
		console.log(data);
		if(data.status){
			CreateObject.object.toast.toast("Logo上传成功");
			$('.upload_prompt').text('上传成功！');
			$('.mes_preview_btn').removeClass('hide');
			$('input[name=logo]').val(data.imageUrl);
			$('.logo_not').hide();
			$('.logo_wp img').attr('src', data.imageUrl);
		}else{
			CreateObject.object.toast.toast("Logo上传失败");
		}
	});
	$('input[name=requestType]').val('create');
	return false;
}