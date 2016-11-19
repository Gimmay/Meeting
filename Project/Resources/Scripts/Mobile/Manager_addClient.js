/**
 * Created by qyqy on 2016-11-7.
 */
$(function(){
	$('.submit button').on('touchend', function(){
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
		var data = $('#form').serialize();
		ThisObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ThisObject.object.loading.complete();
				if(r.status){
					ThisObject.object.toast.toast("创建成功");
					window.location.href = document.referrer
				}else{
					ThisObject.object.toast.toast("创建失败");
				}
			}
		})
	});
});

