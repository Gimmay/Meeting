/**
 * Created by qyqy on 2016-9-13.
 */

function  checkMeeting(){
	var $name = $('#meeting_name');
	var $status = $('#meeting_status');
	var $type = $('#meeting_type');
	var $host = $('#meeting_host');
	var $plan = $('#meeting_plan');
	var $place = $('#meeting_place');
	var $start_time = $('#meeting_start_time');
	var $end_time = $('#meeting_end_time');
	var $director_id = $('#director_id');
	var $contacts_1_id = $('#contacts_1_id');
	var $contacts_2_id = $('#contacts_2_id');
	var $brief = $('#brief');
	var $logo = $('#logo');
	var $comment = $('#comment');

	if($name.val() == ''){
		$name.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$name.parents('.form-group').removeClass('has-error');
	}
	if($status.val() == ''){
		$status.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$status.parents('.form-group').removeClass('has-error');
	}
	if($type.val() == ''){
		$type.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$type.parents('.form-group').removeClass('has-error');
	}
	if($host.val() == ''){
		$host.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$host.parents('.form-group').removeClass('has-error');
	}
	if($plan.val() == ''){
		$plan.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$plan.parents('.form-group').removeClass('has-error');
	}
	if($place.val() == ''){
		$place.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$place.parents('.form-group').removeClass('has-error');
	}
	if($start_time.val() == ''){
		$start_time.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$start_time.parents('.form-group').removeClass('has-error');
	}
	if($end_time.val() == ''){
		$end_time.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$end_time.parents('.form-group').removeClass('has-error');
	}
	if($director_id.val() == ''){
		$director_id.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$director_id.parents('.form-group').removeClass('has-error');
	}
	if($contacts_1_id.val() == ''){
		$contacts_1_id.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$contacts_1_id.parents('.form-group').removeClass('has-error');
	}
	if($contacts_2_id.val() == ''){
		$contacts_2_id.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$contacts_2_id.parents('.form-group').removeClass('has-error');
	}
	if($brief.val() == ''){
		$brief.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$brief.parents('.form-group').removeClass('has-error');
	}
	if($logo.val() == ''){
		$logo.parents('.form-group').addClass('has-error');
		return false;
	}else{
		$logo.parents('.form-group').removeClass('has-error');
	}
};