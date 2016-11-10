/**
 * Created by 1195 on 2016-9-29.
 */

function checkIsEmpty(){
	var $name   = $('#name');
	var $mobile = $('#mobile');
	var $unit   = $('#unit');
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
	if($unit.val() == ''){
		AlterObject.object.toast.toast("单位名称不能为空");
		$unit.focus();
		return false;
	}
	return true;
}