/**
 * Created by qyqy on 2016-9-23.
 */
function checkIsEmpty(){
	var $mid = $('#selected_mid');
	var $name = $('#name');
	var $director = $('#selected_director');
	var $sign_director = $('#selected_sign_director');

	if($name.val() == ''){
		AlterObject.object.toast.toast("签到点名称不能为空");
		$name.focus();
		return false;
	}

	if($director.text() == ''){
		AlterObject.object.toast.toast("负责人不能为空");
		$director.focus();
		return false;
	}
	if($sign_director.text() == ''){
		AlterObject.object.toast.toast("签到负责人不能为空");
		$sign_director.focus();
		return false;
	}
	return true;

};