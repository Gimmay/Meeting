/**
 * Created by 0967 on 2016-9-5.
 */

$(function(){
	//	$('#login_btn').on('mouseup touchend', function(e){
	//		var _this = this;
	//		setTimeout(function(){ // 判定是否是手机端的长按事件
	//			$(_this).off('login'); // 若是长按则什么都不做并不触发登入事件
	//			e.preventDefault();
	//		}, 500);
	//		$(_this).trigger('login'); // 不是长按则登入
	//		$(_this).on('login', function(){
	//			document.getElementById('password').value = $('#password').val().base64Encode();
	//			return true;
	//		});
	//	});
	//	$('#password, #username').on('keydown', function(e){
	//		if(e.keyCode == 13){
	//			document.getElementById('password').value = $('#password').val().base64Encode();
	//			return true;
	//		}
	//	});
	$('#form').on('submit', function(){
		document.getElementById('hidden_password').value = $('#password').val().base64Encode();
	});
	$('.password_wrap .see').on('touchstart mousedown', function(){
		$('#password').attr('type', 'text');
	}).on('touchend mouseup', function(){
		$('#password').attr('type', 'password');
	});
});