/**
 * Created by 1195 on 2016-11-5.
 */
$(function(){
	$('.list_item .title').on('touchstart touchmove touchend', function(){
			switch(event.type) {
				case 'touchstart':
					falg = false;
					break;
				case 'touchmove':
					falg = true;
					break;
				case 'touchend':
					if( !falg ) {
						console.log('点击');
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
					} else {
						console.log('滑动');
					}
					break;
			}
	});
	$('.status').on('touchend', function(){
		var cid = $(this).parents('.client_item').attr('data-cid');
		var e   = $(this);
		if($(this).hasClass('unsigned')){
			Common.ajax({
				data    :{requestType:'sign', cid:cid},
				callback:function(r){
					console.log(r);
					if(r.status){
						var date = new Date(r.data.sign_time*1000);
						console.log(date);
						ThisObject.object.toast.toast('签到成功', 1);
						e.parents('.client_item').find('.sign_employee').text(r.data.sign_director);
						e.parents('.client_item').find('.sign_time').text(date.format('HH:mm:ss'));
						e.parents('.list_item').find('.current_number').text(r.data.sign_count);
						e.addClass('signed').removeClass('unsigned').text('取消签到');
						e.parent().find('.phone').addClass('hide');
					}else{
						ThisObject.object.toast.toast(r.message);
					}
				}
			});
		}else if($(this).hasClass('signed')){
			Common.ajax({
				data    :{requestType:'cancel_sign', cid:cid},
				callback:function(r){
					console.log(r);
					if(r.status){
						ThisObject.object.toast.toast('取消签到成功', 1);
						e.parents('.client_item').find('.sign_employee').text('');
						e.parents('.client_item').find('.sign_time').text('');
						e.parents('.list_item').find('.current_number').text(r.data.sign_count);
						e.addClass('unsigned').removeClass('signed').text('签到');
						e.parent().find('.phone').removeClass('hide');
					}else{
						ThisObject.object.toast.toast('取消签到失败');
					}
				}
			});
		}
	});
	$('.switch').on('touchend', function(){
		if($(this).hasClass('switch_o')){
			$(this).addClass('switch_c glyphicon-chevron-up').removeClass('switch_o glyphicon-chevron-down');
			$('.list_item').each(function(){
				$(this).removeClass('open');
				$(this).find('.list_item_ul').hide();
				$(this).find('.icon').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');
			});
		}else if($(this).hasClass('switch_c')){
			$(this).addClass('switch_o glyphicon-chevron-down').removeClass('switch_c glyphicon-chevron-up');
			$('.list_item').each(function(){
				$(this).addClass('open');
				$(this).find('.list_item_ul').show();
				$(this).find('.icon').addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
			});
		}
	});
});
