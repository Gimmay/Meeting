/**
 * Created by 1195 on 2016-9-23.
 */

function checkIsEmpty(){
	var $mid = $('#selected_mid');
	var $name = $('#name');
	var $director_id = $('#selected_director_id');
	var $sign_director_id = $('#selected_sign_director_id');
	
	if($name.val() == ''){
		CreateObject.object.toast.toast("签到点名称不能为空");
		$name.focus();
		return false;
	}

	if($director_id.text() == ''){
		CreateObject.object.toast.toast("负责人不能为空");
		$director_id.focus();
		return false;
	}
	if($sign_director_id.text() == ''){
		CreateObject.object.toast.toast("签到负责人不能为空");
		$sign_director_id.focus();
		return false;
	}
	return true;

};