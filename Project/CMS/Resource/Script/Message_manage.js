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
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#choose_message_modal').find('input[name=id]').val(id);
	});
	// 删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_message_modal').find('input[name=id]').val(id);
	});
	// 批量删除
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '', i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_message_modal').find('.sAmount').text(i);
		str = str.substr(0, str.length-1);
		if(str != ''){
			$('#batch_delete_message_modal').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择模板！');
		}
		var $object = $('#batch_delete_message_modal');
		$object.find('input[name=id]').val(str);
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
	// 点击过滤标签-微信
	$('#filter_btn_wechat').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'wechat');
		location.replace(new_url);
	});
	(function(){
		var type = url_object.getUrlParam('type');
		if(type == 'wechat'){
			$('.nav_tab').find('.pull-right').hide();
		}
	})()
});