/**
 * Created by 1195 on 2016-11-5.
 */
var ThisScript = {
	bindEvent:function(){
		var self = this;
		$('#mb_footer .auditing').on('click', function(){
			var cid = $('input#h_cid').val();
			var mid = $('input#h_mid').val();
			console.log(cid+'+'+mid);
			if($(this).hasClass('not')){
				// 取消审核
				ManageObject.object.loading.loading();
				Common.ajax({
					data    :{requestType:'cancel_auditing', cid:cid, mid:mid},
					callback:function(data){
						ManageObject.object.loading.complete();
						console.log(data);
						if(data.status){
							$('#mb_footer .auditing').removeClass('not').find('span').text('审核');
							$('#mb_footer .sign').hide();
						}
					}
				});
			}else{
				// 审核
				ManageObject.object.loading.loading();
				Common.ajax({
					data    :{requestType:'auditing', cid:cid, mid:mid},
					callback:function(data){
						ManageObject.object.loading.complete();
						console.log(data.status);
						if(data.status){
							ManageObject.object.toast.toast('审核成功！');
							$('#mb_footer .auditing').addClass('not').find('span').text('取消审核');
							$('#mb_footer .sign').show();
						}
					}
				});
			}
		});
		$('#mb_footer .sign').on('click', function(){
			var cid = $('input#h_cid').val();
			var mid = $('input#m_cid').val();
			if($(this).hasClass('not')){
				// 取消签到
				ManageObject.object.loading.loading();
				Common.ajax({
					data    :{requestType:'cancel_sign', cid:cid, mid:mid},
					callback:function(data){
						ManageObject.object.loading.complete();
						if(data.status){
							$('#mb_footer .sign').removeClass('not').find('span').text('签到');
						}
					}
				});
			}else{
				// 签到
				ManageObject.object.loading.loading();
				Common.ajax({
					data    :{requestType:'sign', cid:cid, mid:mid},
					callback:function(data){
						ManageObject.object.loading.complete();
						console.log(data);
						if(data.status){
							ManageObject.object.toast.toast('签到成功！');
							$('#mb_footer .sign').addClass('not').find('span').text('取消签到');
						}
					}
				});
			}
		});
	}
};
$(function(){
	ThisScript.bindEvent();
});