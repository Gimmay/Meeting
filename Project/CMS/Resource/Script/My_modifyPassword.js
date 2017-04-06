/**
 * Created by 70 on 2017-3-9.
 */
$(function(){
	$(".password_btn").on("click", function(){
		var old_password   = $("#old_password").val();
		var new_password   = $("#new_password").val();
		var new_password_2 = $("#new_password_2").val();
		var reg = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,}$/;
		//		新密码功能判断条件
		if(!reg.test(new_password)){
			ThisObject.object.toast.toast("新密码至少6位，必须同时包含字母和数字！");
			return false;
		}else if(old_password == new_password){
			ThisObject.object.toast.toast("原密码跟新密码不能相同！");
			return false;
		}else if(new_password != new_password_2){
			ThisObject.object.toast.toast("新密码跟确认密码不一致！");
			return false;
		}
		// base64对新密码进行加密
		document.getElementById('old_password').value   = old_password.base64Encode();
		document.getElementById('new_password').value   = new_password.base64Encode();
		document.getElementById('new_password_2').value = new_password_2.base64Encode();
		return true;
	});
});
