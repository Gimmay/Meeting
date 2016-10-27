/**
 * Created by qyqy on 2016-9-22.
 */

$(function(){
	$('#updateBackground').on('change',function(){
		$('input[name=requestType]').val('upload_image');
		$('#submit_logo').trigger('click');
	});
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click',function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	$('.btn_save_hotel').on('click',function(){
		var arr=[],hotel=[];
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			var hotel_name = $(this).parents('tr').find('.hotel_name').text();
			arr.push(id);
			hotel.push(hotel_name);
		});
		$('#choose_hotel').modal('hide')
		$('input[name=hotel]').val(arr);
		$('.c_hotel').removeClass('hide');
		$('.c_hotel').find('span').text(hotel);
	})
});

function  checkIsEmpty(){
	var $name = $('#meeting_name');
	var $host = $('#meeting_host');
	var $start_time = $('#meeting_start_time');
	var $end_time = $('#meeting_end_time');
	var $director_id = $('#selected_director_id');
	var $contacts_1_id = $('#selected_contacts_1_id');
	var $contacts_2_id = $('#selected_contacts_2_id');

	if($name.val() == ''){
		AlterObject.object.toast.toast("会议名称不能为空");
		$name.focus();
		return false;
	}
	if($host.val() == ''){
		AlterObject.object.toast.toast("主办方不能为空");
		$host.focus();
		return false;
	}
	if($start_time.val() == ''){
		AlterObject.object.toast.toast("开始时间不能为空");
		$start_time.focus();
		return false;
	}
	if($end_time.val() == ''){
		AlterObject.object.toast.toast("结束时间不能为空");
		$end_time.focus();
		return false;
	}
	if($director_id.text() == ''){
		AlterObject.object.toast.toast("负责人不能为空");
		$director_id.focus();
		return false;
	}
/*	if($contacts_1_id.text() == ''){
		AlterObject.object.toast.toast("联系人不能为空");
		$contacts_1_id.focus();
		return false;
	}
	if($contacts_2_id.text() == ''){
		AlterObject.object.toast.toast("联系人2不能为空");
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
			AlterObject.object.toast.toast("Logo上传成功");
			$('input[name=logo]').val(data.imageUrl);
			$('.logo_not').hide();
			$('.logo_wp img').attr('src',data.imageUrl);

		}else{
			AlterObject.object.toast.toast("Logo上传失败");
		}
	});
	$('input[name=requestType]').val('alter');
	return false;
}
