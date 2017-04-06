/**
 * Created by 70 on 2017-3-9.
 */
$(function(){

	$('.password_btn').on('click', function(){
		var old_password  = $('#old_password');
		var new_password  = $('#new_password');
		var new_password_2 = $('#new_password_2');

		if(new_password.val() == ''){
			ThisObject.object.toast.toast('新密码不能为空！');
			new_password.focus();
			return false;
		}
		if(new_password_2.val() == ''){
			ThisObject.object.toast.toast('确认密码不能为空！');
			new_password_2.focus();
			return false;
		}
		if(old_password.val() == new_password.val()){
			ThisObject.object.toast.toast('旧密码和新密码不能相同！');
			old_password.focus();
			return false;
		}
		if(new_password.val() != new_password_2.val()){
			ThisObject.object.toast.toast('两次输入密码不一致，请重新输入！');
			new_password.focus();
			return false;
		}
		old_password.val(old_password.val().base64Encode());
		new_password.val(new_password.val().base64Encode());
		new_password_2.val(new_password.val().base64Encode());
		return true;
	});
});
