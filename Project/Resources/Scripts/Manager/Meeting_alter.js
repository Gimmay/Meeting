/**
 * Created by qyqy on 2016-9-22.
 */
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