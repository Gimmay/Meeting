/**
 * Created by qyqy on 2016-10-26.
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
	// 单个删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_car ').find('input[name=id]').val(id);
	});
	// 批量删除
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_car').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_delete_car').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择！');
		}
		$('#batch_delete_car').find('input[name=id]').val(newStr);
	});
	// 修改
	$('.alter_btn').on('click', function(){
		var id           = $(this).parent('.btn-group').attr('data-id');
		var plate_number = $(this).parents('tr').find('.plate_number').text();
		var driver       = $(this).parents('tr').find('.driver').text();
		var type         = $(this).parents('tr').find('.type').text();
		var color        = $(this).parents('tr').find('.color').text();
		var model        = $(this).parents('tr').find('.model').text();
		var capacity     = $(this).parents('tr').find('.capacity').text();
		var client_type  = $(this).parents('tr').find('.client_type').text();
		var comment      = $(this).parents('tr').find('.comment').text();
		$('#alter_car ').find('input[name=id]').val(id);
		$('#alter_car').find('#plate_number_a').val(plate_number);
		$('#alter_car').find('#driver_a').val(driver);
		$('#alter_car').find('#color_a').val(color);
		$('#alter_car').find('#model_a').val(model);
		$('#alter_car').find('#capacity_a').val(capacity);
		$('#alter_car').find('#client_type_a').val(client_type);
		$('#alter_car').find('#comment_a').val(comment);
		Common.ajax({
			data    :{requestType:'get_car', id:id},
			callback:function(r){
				console.log(r);
			}
		});
		ManageObject.object.typeSelectAlter.setValue(type);
		ManageObject.object.typeSelectAlter.setHtml(type);
		ManageObject.object.driverSelectAlter.setValue(driver);
		ManageObject.object.driverSelectAlter.setHtml(driver);
		console.log();
	});
	// 点击过滤标签-全部
	$('#filter_btn_all').on('click', function(){
		var new_url = url_object.delUrlParam('type');
		location.replace(new_url);
	});
	// 点击过滤标签-使用中
	$('#filter_btn_using').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'using');
		location.replace(new_url);
	});
	// 点击过滤标签-未使用
	$('#filter_btn_not_use').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'not_use');
		location.replace(new_url);
	});
	// 点击过滤标签-已结束
	$('#filter_btn_finish').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'finish');
		location.replace(new_url);
	});
});