/**
 * Created by qyqy on 2016-9-29.
 */

$(function(){
	$('.sign_btn.sign').on('touchend', function(){
		var elem = this;
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'manage:sign'}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						try{
							//noinspection JSUnresolvedVariable
							WeixinJSBridge.call('closeWindow');
						}catch(ReferenceError){
							window.close();
							window.location.href="about:blank";
						}
					});
					ManageObject.object.toast.toast("签到成功！", 1);
				}else ManageObject.object.toast.toast(data.message);
			}
		})
	});
	$('.sign_btn.anti_sign').on('touchend', function(){
		var elem = this;
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'manage:anti_sign'}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						try{
							//noinspection JSUnresolvedVariable
							WeixinJSBridge.call('closeWindow');
						}catch(ReferenceError){
							window.close();
							window.location.href="about:blank";
						}
					});
					ManageObject.object.toast.toast("取消签到成功！", 1);
					$(elem).html('签到').removeClass('anti_sign').addClass('sign');
				}else ManageObject.object.toast.toast(data.message);
			}
		})
	})
});
