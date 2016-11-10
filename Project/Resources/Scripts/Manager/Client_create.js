
function checkIsEmpty(){
	var $name   = $('#name');
	var $mobile = $('#mobile');
	var $unit = $('#unit');
	if($name.val() == ''){
		CreateObject.object.toast.toast("姓名不能为空");
		$name.focus();
		return false;
	}
	if($mobile.val() == ''){
		CreateObject.object.toast.toast("手机号不能为空");
		$mobile.focus();
		return false;
	}
	if($unit.val() == ''){
		CreateObject.object.toast.toast("单位名称不能为空");
		$unit.focus();
		return false;
	}
	return true;
}