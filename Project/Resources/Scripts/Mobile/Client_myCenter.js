/**
 * Created by qyqy on 2016-9-29.
 */

$(function(){
	var body_h = $('body').height();
	$('#mb_wrap').css('minHeight', body_h);
	// 监听签到状态
	setInterval(function(){
		Common.ajax({
			data    :{requestType:'check_sign'},
			async   :false,
			callback:function(data){
				var old_status = parseInt(MyCenterObject.config.signStatus);
				var new_status = parseInt(data);
				if(old_status != new_status){
					switch(new_status){
						case 0:
							MyCenterObject.object.toast.toast('已经取消签到', 1);
							break;
						case 1:
							MyCenterObject.object.toast.toast('签到成功', 1);
							break;
					}
					MyCenterObject.config.signStatus = data;
				}
			}
		})
	}, 2000);
});
