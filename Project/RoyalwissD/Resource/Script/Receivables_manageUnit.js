/**
 * Created by qyqy on 2016-12-7.
 */
var ThisObject = {
	optTemp          :'<option value="$numOpt">$name</option>',
	tableTemp        :'<tr>\n\t<td class="check_item_excel"><input type="checkbox" class="icheck_excel" value="$id"></td>\n\t<td>$num</td><td class="excel_name" data-id="$id">$value</td>\n\t<td>\n\t\t<select name="client_info" id="" class="form-control select_h">\n\t\t\t$opt\n\t\t</select>\n\t</td>\n</tr>',
	uploadInterval   :null,
	signBntTemp      :'<a class="btn btn-default btn-sm" href="javascript:void(0)" role="button" data-id="$id">$signName</a>',
	signActiveBntTemp:'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$signName</a>',
	receivablesTemp  :'<tr>\n\t<td class="project_type">$projectType</td>\n\t<td class="project_name">$projectName</td>\n\t<td class="pay_method">$payMethod</td>\n\t<td class="pos">$pos</td>\n\t<td class="receivables_type">$receivablesType</td>\n\t<td class="employee_name">$employeeName</td>\n\t<td class="time">$time</td>\n\t<td class="place">$place</td>\n\t<td class="price">$price</td>\n</tr>'
};
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 显示结果数
	$('.table_length').find('select').on('change', function(){
		var number = $(this).find('option:selected').text();
		var url    = url_object.setUrlParam('_page_count', number);
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
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 已收款客户列表
	$('.check_receivables').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('receivables');
		if(param == 1){
			var new_url = link.delUrlParam('receivables');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('receivables', 1);
			location.replace(reviewed_url);
		}
	});
	// 未收款客户列表
	$('.check_not_receivables').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('receivables');
		if(param == 0){
			var new_url = link.delUrlParam('receivables');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('receivables', 0);
			location.replace(reviewed_url);
		}
	});
	(function(){
		// 人员状态列表（签到\审核\收款）
		var mvc         = $('#quasar_script').attr('data-url-sys-param');
		var suffix      = $('#quasar_script').attr('data-page-suffix');
		var link        = new Quasar.UrlClass(1, mvc, suffix);
		var receivables = link.getUrlParam('receivables');
		if(receivables == 1) $('.check_receivables').find('.iradio_square-blue').addClass('checked');
		if(receivables == 0) $('.check_not_receivables').find('.iradio_square-blue').addClass('checked');
	})();
	// 打印 print
	$('.btn_print').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		Common.ajax({
			data    :{requestType:'manage:get_receivables', id:id},
			callback:function(r){
				console.log(r);
				var time = new Date();
				time     = time.format('yyyy年MM月dd日');
				$('#print').find('.time').text(time);
				$('#print').find('.unit').text(r.unit);
				$('#print').find('.client_name').text(r.client);
				$('#print').find('.project_type').text(r.receivables_type);
				$('#print').find('.price_capital').text(r.price_word);
				$('#print').find('.identifier').text(r.order_number);
				$('#print').find('.price').text(r.price);
				$('#print').find('.price').text(r.price);
				var str = '', i = 0;
				$.each(r.option.pay_method_list, function(index, value){
					if(index == 0){
						$('.type1').text(value.name);
						$('.price1').text(value.price);
					}else{
						str += ThisObject.trTemp.replace('$type', value.name).replace('$price', value.price);
					}
					i++;
				});
				$('.sign_tr').find('.rmb').attr('rowspan', i);
				$('.sign_tr').after(str);
				$("#print").printArea();
			}
		})
	});

	// 自定义列表字段控制
	//  点击field_checkbox label标签
	$('.field_checkbox').on('click', function(){
		if($(this).find('.icheckbox_flat-blue').hasClass('checked')){
			var t_code = $(this).find('.icheck_f').attr('data-code');
			$('#field_list .btn').each(function(){
				var m_code = $(this).attr('data-code');
				if(t_code == m_code){
					$(this).addClass('show').removeClass('hide');
					$(this).find('input.c_view').val(1);
				}
			});
		}else{
			var t_code = $(this).find('.icheck_f').attr('data-code');
			$('#field_list .btn').each(function(){
				var m_code = $(this).attr('data-code');
				if(t_code == m_code){
					$(this).addClass('hide').removeClass('show');
					$(this).find('input.c_view').val(0);
				}
			});
		}
	});
	// 点击图标
	$('.field_checkbox .iCheck-helper').on('click', function(e){
		e.stopPropagation();
		if($(this).parent('.icheckbox_flat-blue').hasClass('checked')){
			var t_code = $(this).parent().find('.icheck_f').attr('data-code');
			$('#field_list .btn').each(function(){
				var m_code = $(this).attr('data-code');
				if(t_code == m_code){
					$(this).addClass('show').removeClass('hide');
					$(this).find('input.c_view').val(1);
				}
			});
		}else{
			var t_code = $(this).parent().find('.icheck_f').attr('data-code');
			$('#field_list .btn').each(function(){
				var m_code = $(this).attr('data-code');
				if(t_code == m_code){
					$(this).addClass('hide').removeClass('show');
					$(this).find('input.c_view').val(0);
				}
			});
		}
	});
	$('#list_menu .btn-save').on('click', function(){
		var data = $('#list_menu form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(res){
				ManageObject.object.loading.complete();
				if(res.status){
					ManageObject.object.toast.toast(res.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					});
				}else{
				}
				ManageObject.object.toast.toast(res.message, '2');
			}
		})
	});
});

