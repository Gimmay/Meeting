/**
 * Created by qyqy on 2016-9-13.
 */

function  checkMeetingCreate(){
	var $name = $('#meeting_name');
	var $status = $('#meeting_status');
	var $type = $('#meeting_type');
	var $host = $('#meeting_host');
	var $plan = $('#meeting_plan');
	var $place = $('#meeting_place');
	var $start_time = $('#meeting_start_time');
	var $end_time = $('#meeting_end_time');
	var $director_id = $('#selected_director_id');
	var $contacts_1_id = $('#selected_contacts_1_id');
	var $contacts_2_id = $('#selected_contacts_2_id');
	var $brief = $('#brief');
	var $logo = $('#logo');
	var $comment = $('#comment');

	if($name.val() == ''){
		MeetingCreateObject.object.toast.toast("会议名称不能为空");
		$name.focus();
		return false;
	}
	if($host.val() == ''){
		MeetingCreateObject.object.toast.toast("主办方不能为空");
		$host.focus();
		return false;
	}
	if($plan.val() == ''){
		MeetingCreateObject.object.toast.toast("策划方不能为空");
		$plan.focus();
		return false;
	}
	if($start_time.val() == ''){
		MeetingCreateObject.object.toast.toast("开始时间不能为空");
		$start_time.focus();
		return false;
	}
	if($end_time.val() == ''){
		MeetingCreateObject.object.toast.toast("结束时间不能为空");
		$end_time.focus();
		return false;
	}
	if($director_id.text() == ''){
		MeetingCreateObject.object.toast.toast("负责人不能为空");
		$director_id.focus();
		return false;
	}
	if($contacts_1_id.text() == ''){
		MeetingCreateObject.object.toast.toast("联系人不能为空");
		$contacts_1_id.focus();
		return false;
	}
	if($contacts_2_id.text() == ''){
		MeetingCreateObject.object.toast.toast("联系人2不能为空");
		$contacts_2_id.focus();
		return false;
	}
	if($brief.val() == ''){
		MeetingCreateObject.object.toast.toast("开始时间不能为空");
		$brief.focus();
		return false;
	}
	if($logo.val() == ''){
		MeetingCreateObject.object.toast.toast("LOGO图片地址不能为空");
		$logo.focus();
		return false;
	}
};

$(function(){

});