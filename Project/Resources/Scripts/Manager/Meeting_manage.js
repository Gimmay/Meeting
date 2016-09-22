/**
 * Created by qyqy on 2016-9-12.
 */
$(function(){
	var $meeting_name = $('#meeting_name');
	var $meeting_status = $('#meeting_status');
	var $meeting_type = $('#meeting_type');
	var $meeting_host = $('#meeting_host');
	var $meeting_plan = $('#meeting_plan');
	var $meeting_place = $('#meeting_place');
	var $meeting_start_time = $('#meeting_start_time');
	var $meeting_end_time = $('#meeting_end_time');
	var $director_id = $('#director_id');
	var $contacts_1_id = $('#contacts_1_id');
	var $contacts_2_id = $('#contacts_2_id');
	var $brief = $('#brief');
	var $logo = $('#logo');
	var $comment = $('#comment');

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
			console.log(newStr);
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
});


