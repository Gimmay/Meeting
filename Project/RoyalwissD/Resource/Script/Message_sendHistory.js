/**
 * Created by Iceman on 2017-4-7.
 */

$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
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
		if(type == 'sms'){
			$('.c_header').removeClass('hide');
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
