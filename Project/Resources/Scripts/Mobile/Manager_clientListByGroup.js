/**
 * Created by 1195 on 2016-11-5.
 */
$(function(){
	$('.list_item .title').on('touchend', function(){
		if($(this).parent('.list_item').hasClass('open')){
			$(this).parent('.list_item').removeClass('open');
			$(this).parent('.list_item').find('.list_item_ul').hide();
			$(this).parent('.list_item').find('.icon').addClass('glyphicon-chevron-up')
				   .removeClass('glyphicon-chevron-down');
		}else{
			$(this).parent('.list_item').addClass('open');
			$(this).parent('.list_item').find('.list_item_ul').show();
			$(this).parent('.list_item').find('.icon').addClass('glyphicon-chevron-down')
				   .removeClass('glyphicon-chevron-up');
		}
	});
	$('.status').on('touchend', function(){
		var cid = $(this).parents('.client_item').attr('data-cid');
		if($(this).hasClass('unsigned')){
			Common.ajax({
				data    :{requestType:'sign', cid:cid},
				callback:function(r){
					console.log(r);
					if(r.status){
						ThisObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
						ThisObject.object.toast.toast('签到成功', 1);
						/*$(this).addClass('signed').removeClass('unsigned').text('取消签到');*/
					}else{
						ThisObject.object.toast.toast('签到失败');
					}
				}
			});
		}else if($(this).hasClass('signed')){
			Common.ajax({
				data    :{requestType:'cancel_sign', cid:cid},
				callback:function(r){
					console.log(r);
					if(r.status){
						ThisObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
						ThisObject.object.toast.toast('取消签到成功', 1);
						/*	$(this).addClass('unsigned').removeClass('signed').text('签到');*/
					}else{
						ThisObject.object.toast.toast('取消签到失败');
					}
				}
			});
		}
	});
});
