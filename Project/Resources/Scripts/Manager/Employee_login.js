/**
 * Created by 0967 on 2016-9-5.
 */

$(function(){
	$('.login').on(function(){
		if($('#username').val() != ''){
			if($('#password').val() != ''){
				var data = {
					username:$('#username').val(),
					password:$('#password').val()
				};
				$.ajax({
					url    :'',
					type   :'post',
					data   :data,
					success:function(result){
					},
					error  :function(){
					}
				})
			}else{
				alert('密码不能为空！');
			}
		}else{
			alert('用户名不能为空！');
		}
	});
});





