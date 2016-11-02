/**
 * Created by 1195 on 2016-11-1.
 */
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	var str_object = new Quasar.StringClass();
	$('#sign_btn').on('click', function(){
		Common.ajax({
			data:{requestType:'myMeeting:sign'},
			callback:function(r){
				if(r.status){
					ThisObject.object.toast.onQuasarHidden(function(){
						location.href = url_object.setUrlParam('token', str_object.makeRandomString(6));
					});
					ThisObject.object.toast.toast('签到成功');
				}
				else ThisObject.object.toast.toast(r.message);
			}
		})
	});
});