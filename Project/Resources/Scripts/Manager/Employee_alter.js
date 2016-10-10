/**
 * Created by qyqy on 2016-9-21.
 */
function checkIsEmpty(){
	var $code   = $('#code');
	var $name   = $('#name');
	var $selected_position = $('#selected_position');
	var $selected_department = $('#selected_department');
	var $mobile = $('#mobile');
	if($code.val() == ''){
		AlterObject.object.toast.toast("工号不能为空");
		$code.focus();
		return false;
	}
	if($name.val() == ''){
		AlterObject.object.toast.toast("姓名不能为空");
		$name.focus();
		return false;
	}
	if($selected_position.text() == ''){
		AlterObject.object.toast.toast("职位不能为空");
		$selected_position.focus();
		return false;
	}
	if($selected_department.text() == ''){
		AlterObject.object.toast.toast("部门不能为空");
		$selected_department.focus();
		return false;
	}
	if($mobile.val() == ''){
		AlterObject.object.toast.toast("手机号不能为空");
		$mobile.focus();
		return false;
	}
	return true;
}
