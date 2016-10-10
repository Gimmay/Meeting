
function checkIsEmpty(){
	var $name   = $('#name');
	var $mobile = $('#mobile');
	var $club = $('#club');
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
	if($club.val() == ''){
		CreateObject.object.toast.toast("会所名称不能为空");
		$club.focus();
		return false;
	}
	return true;
}