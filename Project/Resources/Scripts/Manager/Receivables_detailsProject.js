/**
 * Created by 1195 on 2016-10-19.
 */

var ThisObject = {
	bindEvent           :function(){
		var self = this;
		// 选择代金券
		$('.coupon_list a').on('click', function(){
			var id = $(this).attr('data-id');
			if($(this).hasClass('active')){
				$(this).removeClass('active');
			}else{
				$(this).addClass('active');
			}
		});
		// 添加收款代金券选择
		$('#add_receivables .coupon_list a').on('click', function(){
			self.eachAddReceivables();
		});
		self.eachAddReceivables();
		// 添加修改代金券选择
		$('#alter_receivables .coupon_list a').on('click', function(){
			self.eachAlterReceivables();
		});
		self.eachAlterReceivables();
		$('.clear-marginleft').on('click', function(){
			$('#price').val(0);
		});
		// 删除收款记录
		$('.delete_btn').on('click', function(e){
			$('#delete_receivables').modal('show');
			var id = $(this).parent('.btn-group').attr('data-id');
			$('#delete_receivables').find('input[name=id]').val(id);
		});
		$('.modify_btn').on('click', function(){
			$('#alter_modal').modal('show');
			var main_id = $(this).attr('data-main-id');
			var sub_id  = $(this).attr('data-sub-id');
			$('#alter_modal').find('#main_id').val(main_id);
			$('#alter_modal').find('#sub_id').val(sub_id);
			Common.ajax({
				data    :{requestType:'get_single_receivables', main_id:main_id, sub_id:sub_id},
				callback:function(r){
					console.log(r);
					$('#alter_modal').find('#other_name').val(r.main.project_name);
					/*ManageObject.object.otherName.setHtml(r.main.project_name);
					 ManageObject.object.otherName.setValue(r.sub.pay_method);*/
					$('#alter_modal .source_type').find('option').eq(r.sub.type-1).prop('selected', true)
					ManageObject.object.payMethod.setHtml(r.sub.pay_method_name);
					ManageObject.object.payMethod.setValue(r.sub.pay_method);
					ManageObject.object.posMachine.setHtml(r.sub.pos_machine_name);
					ManageObject.object.posMachine.setValue(r.sub.pos_machine);
					$('#alter_modal').find('#receivables_time1').val(r.main.time);
					$('#alter_modal').find('#comment').val(r.sub.comment);
				}
			})
		});
		//导入excel
		$('#excel_file').on('change', function(){
			//ManageObject.object.loading.loading();
			getIframeData();
		});
	},
	unbindEvent         :function(){
		$('.coupon_list a').off('click');
	},
	eachAddReceivables  :function(){
		var arr = [];
		$('#add_receivables .coupon_list a.active').each(function(){
			var id = $(this).attr('data-id');
			arr.push(id)
		});
		$('#add_receivables').find('input[name=coupon_code]').val(arr);
	},
	eachAlterReceivables:function(){
		var arr = [];
		$('#alter_receivables .coupon_list a.active').each(function(){
			var id = $(this).attr('data-id');
			arr.push(id)
		});
		$('#alter_receivables').find('input[name=coupon_code]').val(arr);
	}
};
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 显示结果数
	$('.table_length').find('select').on('change', function(){
		var number = $(this).find('option:selected').text();
		var url    = url_object.setUrlParam('_page_count', number);
		url        = url_object.delUrlParam('p', url);
		location.replace(url);
	});
	// 页面显示列表数下拉框默认值处理
	(function(){
		var page_count = url_object.getUrlParam('_page_count');
		$('.table_length').find('select option').each(function(){
			var num = $(this).text();
			if(page_count == num){
				$('.table_length').find('select option:selected').removeProp('selected').removeAttr('selected');
				$(this).prop('selected', true).attr('selected', true);
				return true;
			}
		});
	})();
	$('.rece_item .header').on('click', function(){
		if($(this).parents('.rece_item').find('.rece_body').hasClass('hide')){
			$(this).parents('.rece_item').find('.rece_body').removeClass('hide');
			$(this).find('.plus').removeClass('glyphicon-plus').addClass('glyphicon-minus');
		}else{
			$(this).parents('.rece_item').find('.rece_body').addClass('hide');
			$(this).find('.plus').removeClass('glyphicon-minus').addClass('glyphicon-plus');
		}
	});
	var quasar_script          = document.getElementById('quasar_script');
	var url_object             = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	var $add_receivables_modal = $('#add_receivables');
	$('.add_btn ').on('click', function(){
		$(this).parents('.form-group').after(ThisObject.receivablesTemp);
		Common.ajax({
			data    :{requestType:'get_rece_list'},
			callback:function(r){
				console.log(r);
			}
		})
		$('.delete_rece_btn').on('click', function(){
			$(this).parents('.form-group').remove();
		});
	});
	$('.delete_rece_btn').on('click', function(){
		$(this).parents('.form-group').remove();
	});
	ThisObject.bindEvent();
	$('.cancel').on('click', function(e){
		e.stopPropagation();
		var id = $(this).parent('.header').attr('data-id');
		$('#cancel_modal').find('input[name=id]').val(id);
		$('#cancel_modal').modal('show');
	})
	var cur_order_column= $('#default_order_column').val();
	var cur_order_method= $('#default_order_method').val();
	$('.table_header span[data-column]').on('click', function(){
		var order_column = $(this).attr('data-column');
		var order_method = url_object.getUrlParam('_orderMethod');
		var new_url      = url_object.setUrlParam('_orderColumn', order_column);
		if(cur_order_column == order_column){
			if(order_method == 'desc') order_method = 'asc';
			else if(order_method == 'asc') order_method = 'desc';
			else order_method = 'desc';
		}
		else order_method = 'asc';
		new_url = url_object.setUrlParam('_orderMethod', order_method, new_url);
		location.replace(new_url);
	}).each(function(){
		var order_column = $(this).attr('data-column');
		if(order_column == cur_order_column){
			var column_word = $(this).text();
			var method_word = cur_order_method == 'desc' ? '▼' : '▲';
			$(this).css('background', '#EFEFEF').text(column_word+method_word);
		}
	});
});

// 收款导入excel
function getIframeData(){
	var data = new FormData($('#file_form')[0]);
	console.log(data);
	$.ajax({
		url        :'',
		type       :'POST',
		data       :data,
		dataType   :'JSON',
		cache      :false,
		processData:false,
		contentType:false
	}).done(function(data){
		console.log(data);
		if(data.status){
			ManageObject.object.toast.toast(data.message);
			location.reload(); // 刷新本页面
		}else{
			ManageObject.object.toast.toast('导入失败，请按照Excel模板格式填写。');
		}
	});
	return false;
}
