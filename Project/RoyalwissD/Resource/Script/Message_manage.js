/**
 * Created by 1195 on 2016-9-29.
 */
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 选择
	$('.choose_btn').on('click', function(){
		var $this_modal = $('#choose_modal');
		var id          = $(this).attr('data-id');
		$this_modal.find('input[name=id]').val(id);
		$this_modal.find('.btn-save').on('click', function(){
			var data = $this_modal.find('form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, 1);
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(r.message, 2);
					}
				}
			})
		});
	});
	// 点击过滤标签-全部
	$('#filter_btn_all').on('click', function(){
		var new_url = url_object.delUrlParam('type');
		location.replace(new_url);
	});
	// 点击过滤标签-短信
	$('#filter_btn_sms').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'sms');
		location.replace(new_url);
	});
	// 点击过滤标签-微信企业号
	$('#filter_btn_wechat_enterprise').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'wechatEnterprise');
		location.replace(new_url);
	});
	// 点击过滤标签-微信公众号
	$('#filter_btn_wechat_official').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'wechatOfficial');
		location.replace(new_url);
	});
	// 点击过滤标签-邮箱
	$('#filter_btn_email').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'email');
		location.replace(new_url);
	});
	(function(){
		var type = url_object.getUrlParam('type');
		if(type == 'wechat'){
			$('.nav_tab').find('.pull-right').hide();
		}
	})();
	//遍历选择按钮
	$('.choose_btn').on('click', function(){
		var id = $(this).parent().attr('data-id');
		$('.checkbox .icheck_f').iCheck('uncheck')
		if($('input[data-message-id='+id+']').length>0){
			$('input[data-message-id='+id+']').each(function(){
				var action_id = $(this).attr('data-action-id')
				$('.checkbox .icheck_f').each(function(){
					var val = $(this).val();
					if(action_id == val){
						$(this).iCheck('check')
					}
				})
			});
		}
	});
});