/**
 * Created by 1195 on 2016-9-29.
 */

function checkIsEmpty(){
	var $name   = $('#name');
	var $mobile = $('#mobile');
	var $club   = $('#club');
	if($name.val() == ''){
		AlterObject.object.toast.toast("姓名不能为空");
		$name.focus();
		return false;
	}
	if($mobile.val() == ''){
		AlterObject.object.toast.toast("手机号不能为空");
		$mobile.focus();
		return false;
	}
	if($club.val() == ''){
		AlterObject.object.toast.toast("会所名称不能为空");
		$club.focus();
		return false;
	}
	return true;
}