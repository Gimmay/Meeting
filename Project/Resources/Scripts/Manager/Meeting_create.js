/**
 * Created by qyqy on 2016-9-13.
 */

$(function(){
	$('#updateBackground').on('change',function(){
		$('input[name=requestType]').val('upload_image');
		$('#submit_logo').trigger('click');
	});
});

function  checkIsEmpty(){
	var $name = $('#meeting_name');
	var $host = $('#meeting_host');
	var $plan = $('#meeting_plan');
	var $start_time = $('#meeting_start_time');
	var $end_time = $('#meeting_end_time');
	var $director_id = $('#selected_director_id');
	var $contacts_1_id = $('#selected_contacts_1_id');
	var $contacts_2_id = $('#selected_contacts_2_id');
	if($name.val() == ''){
		CreateObject.object.toast.toast("会议名称不能为空");
		$name.focus();
		return false;
	}
	if($host.val() == ''){
		CreateObject.object.toast.toast("主办方不能为空");
		$host.focus();
		return false;
	}
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
	if($director_id.text() == ''){
		CreateObject.object.toast.toast("负责人不能为空");
		$director_id.focus();
		return false;
	}
/*	if($contacts_1_id.text() == ''){
		CreateObject.object.toast.toast("负责人不能为空");
		$contacts_1_id.focus();
		return false;
	}
	if($contacts_2_id.text() == ''){
		CreateObject.object.toast.toast("负责人不能为空");
		$contacts_2_id.focus();
		return false;
	}*/
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
			$('#logo_src').attr('src',data.imageUrl);

		}else{
			CreateObject.object.toast.toast("Logo上传失败");
		}
	});
	$('input[name=requestType]').val('create');
	return false;
}