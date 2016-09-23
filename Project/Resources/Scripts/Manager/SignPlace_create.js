/**
 * Created by 1195 on 2016-9-23.
 */

function checkSignPlaceCreate(){
	var $mid = $('#selected_mid');
	var $name = $('#name');
	var $director_id = $('#selected_director_id');
	var $sign_director_id = $('#selected_sign_director_id');
	if($mid.text() == ''){
		SignPlaceObject.object.toast.toast("会议名称不能为空");
		$mid.focus();
		return false;
	}

	if($mid.text() == ''){
		SignPlaceObject.object.toast.toast("工号不能为空");
		$mid.focus();
		return false;
	}

	if($name.val() == ''){
		SignPlaceObject.object.toast.toast("签到点名称不能为空");
		$name.focus();
		return false;
	}

	if($director_id.text() == ''){
		SignPlaceObject.object.toast.toast("负责人不能为空");
		$director_id.focus();
		return false;
	}
	if($sign_director_id.text() == ''){
		SignPlaceObject.object.toast.toast("签到负责人不能为空");
		$sign_director_id.focus();
		return false;
	}
	return true;

};