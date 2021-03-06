/**
 * Created by qyqy on 2016-9-21.
 */
function checkIsEmpty(){
	var $name   = $('#name');
	var $mobile = $('#mobile');
	if($name.val() == ''){
		ManageObject.object.toast.toast("姓名不能为空");
		$name.focus();
		return false;
	}
	if($mobile.val() == ''){
		ManageObject.object.toast.toast("手机号不能为空");
		$mobile.focus();
		return false;
	}
	return true;
}
$(function(){
	$('#btn-save').on('click', function(){
		if(checkIsEmpty()){
			ManageObject.object.loading.loading();
			var data = $('#form').serialize();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, 1);
						ManageObject.object.toast.onQuasarHidden(function(){
							location.href = r.nextPage;
						})
					}else{
						ManageObject.object.toast.toast(r.message, 2);
					}
				}
			});
		}
	});
});