/**
 * Created by qyqy on 2016-9-29.
 */

$(function(){
	$('#sign').on('touchend',function(){
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'sign'},async:false,callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					ManageObject.object.toast.toast("签到成功！");
					$('#sign').html('取消签到').attr('id','anti_sign')
					location.reload(true);
				}else ManageObject.object.toast.toast(data.message);
			}
		})
	})
	$('#anti_sign').on('touchend',function(){
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'anti_sign'},async:false,callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					ManageObject.object.toast.toast("取消签到成功！");
					$('#anti_sign').html('签到').attr('id','sign');
					location.reload(true);
				}else ManageObject.object.toast.toast(data.message);
			}
		})
	})
});
