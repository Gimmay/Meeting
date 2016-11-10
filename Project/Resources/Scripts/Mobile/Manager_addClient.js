/**
 * Created by qyqy on 2016-11-7.
 */
$(function(){
	$('#create').on('touchend', function(){
		var formData = $('#form').serialize();
		Common.ajax({
			data    :formData,
			callback:function(data){
				console.log(data);
			},
		})
	});
	
});
function checkIsEmpty(){
	var $name   = $('input[name=name]');
	var $unit   = $('input[name=unit]');
	var $mobile = $('input[name=mobile]');
	if($name.val() == ''){
		ThisObject.object.toast.toast("姓名不能为空");
		$name.focus();
		return false;
	}
	if($mobile.val() == ''){
		ThisObject.object.toast.toast("手机不能为空");
		$mobile.focus();
		return false;
	}
	if($unit.val() == ''){
		ThisObject.object.toast.toast("单位名称不能为空");
		$unit.focus();
		return false;
	}
	return true;
}


