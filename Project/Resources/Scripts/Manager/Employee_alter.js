/**
 * Created by qyqy on 2016-9-21.
 */

var employeeAlter = {
		checkIsEmpty:function(){
			var $code   = $('#code');
			var $name   = $('#name');
			//var $password = $('#password');
			var $mobile = $('#mobile');
			if($code.val() == ''){
				ManageObject.object.toast.toast("工号不能为空");
				$code.focus();
				return false;
			}
			if($name.val() == ''){
				ManageObject.object.toast.toast("姓名不能为空");
				$name.focus();
				return false;
			}
			/*	if($password.val() == ''){
			 ManageObject.object.toast.toast("密码不能为空");
			 $password.focus();
			 return false;
			 }*/
			if($mobile.val() == ''){
				ManageObject.object.toast.toast("手机号不能为空");
				$mobile.focus();
				return false;
			}else{
				var mobile = new Common.RegExpClass();
				if(mobile.isMobile($mobile.val())){
					alert('格式正确');
				}else{
					alert('格式不正确');
				}
			}
			return true;
		},
}