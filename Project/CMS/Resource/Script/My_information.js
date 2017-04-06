/**
 * Created by 1195 on 2016-10-25.
 */
$(function(){
	$('#form .btn-save').on('click', function(){
		if(checkIsEmpty()){
			ManageObject.object.loading.loading();
			var data = $('form').serialize();
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
			})
		}
	});
});
function checkIsEmpty(){
	var $mobile = $('#mobile');
	if($mobile.val() == ''){
		ManageObject.object.toast.toast('手机号不能为空！');
		$mobile.focus();
		return false;
	}
	return true;
}
