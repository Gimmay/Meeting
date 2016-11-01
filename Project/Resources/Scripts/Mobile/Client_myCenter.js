/**
 * Created by qyqy on 2016-9-29.
 */
//
// $(function(){
// 	var body_h = $('body').height();
// /*	$('#mb_wrap').css('minHeight', body_h);*/
// 	// 监听签到状态
// 	setInterval(function(){
// 		Common.ajax({
// 			data    :{requestType:'myCenter:check_sign'},
// 			async   :false,
// 			callback:function(r){
// 				var old_status = parseInt(MyCenterObject.config.signStatus);
// 				var new_status = parseInt(r.data);
// 				if(old_status != new_status){
// 					switch(new_status){
// 						case 0:
// 						case 2:
// 							MyCenterObject.object.toast.toast('已经取消签到', 1);
// 							$('.sign_type').removeClass('signed').addClass('unsigned').text('未签到');
// 							break;
// 						case 1:
// 							MyCenterObject.object.toast.toast('签到成功', 1);
// 							$('.sign_type').removeClass('unsigned').addClass('signed').text('已签到');
// 							break;
// 					}
// 					MyCenterObject.config.signStatus = r.data;
// 				}
// 			}
// 		})
// 	}, 2000);
// });
