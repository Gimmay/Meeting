/**
 * Created by 0967 on 2016-9-5.
 */

$(function(){
	//	$('.login').on(function(){
	//		if($('#username').val() != ''){
	//			if($('#password').val() != ''){
	//				var data = {
	//					username:$('#username').val(),
	//					password:$('#password').val()
	//				};
	//				$.ajax({
	//					url    :'',
	//					type   :'post',
	//					data   :data,
	//					success:function(result){
	//					},
	//					error  :function(){
	//					}
	//				})
	//			}else{
	//				alert('密码不能为空！');
	//			}
	//		}else{
	//			alert('用户名不能为空！');
	//		}
	//	});
	$('.login').on('click', function(){
		var code          = $('#username').val();
		var $password_obj = $('#password');
		var password      = $password_obj.val();
		$password_obj.val(encodePassword(password, code));
		$('#form').submit();
	});
	$('#username, #password').on('keyup', function(e){
		if(e.keyCode == 13){
			var code          = $('#username').val();
			var $password_obj = $('#password');
			var password      = $password_obj.val();
			$password_obj.val(encodePassword(password, code));
			$('#form').submit();
		}
	});
	$('.password_wrap .see').mousedown(function(){
		$('#password').attr('type', 'text');
	});
	$('.password_wrap .see').mouseup(function(){
		$('#password').attr('type', 'password');
	});
});
function encodePassword(password, code){
	var temp_str = $.md5(password+code);
	var str1     = temp_str.substr(5, 16);
	temp_str     = $.md5(temp_str.substr(7, 13));
	var str2     = temp_str.substr(11, 16);
	temp_str     = $.md5(temp_str.split("").reverse().join(""));
	var str3     = temp_str.substr(13, 16);
	temp_str     = $.sha1(temp_str);
	var str4     = temp_str.substr(17, 16);
	temp_str     = (str1+str2+str3+str4).toUpperCase();
	return temp_str;
}