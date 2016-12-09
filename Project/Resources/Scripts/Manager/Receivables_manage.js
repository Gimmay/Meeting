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
		$('#print').css('left', 0);
		Common.ajax({
			data    :{requestType:'get_receivables', id:id},
			callback:function(r){
				console.log(r);
				var htm = '';
				$.each(r, function(index, value){
					console.log(value);
					$('#print').find('.client_name').text(value.client_name);
					$('#print').find('.unit').text(value.unit);
					// 判断项目类型
					var project_type;
					console.log(value.type);
					switch(parseInt(value.type)){
						case 1:
							project_type = '门票';
							break;
						case 2:
							project_type = '代金券';
							break;
						case 3:
							project_type = '产品';
							break;
						case 4:
							project_type = '其他';
							break;
						case 5:
							project_type = '定金';
							break;
						case 6:
							project_type = '课程费';
							break;
						case 7:
							project_type = '产品费';
							break;
						case 8:
							project_type = '场餐费';
							break;
					}
					console.log(project_type);
					// 判断会前 会中 会后 收款
					var source_type;
					if(value.source_type == 1){
						source_type = '会前收款';
					}else if(value.source_type == 2){
						source_type = '会中收款';
					}else if(value.source_type == 3){
						source_type = '会后收款';
					}
					// 收款时间
					var time = Common.getLocalTime(value.time);
					htm += ThisObject.receivablesTemp.replace('$projectType', project_type)
									 .replace('$projectName', value.name)
									 .replace('$payMethod', value.method_name).replace('$pos', value.pos_name)
									 .replace('$receivablesType', source_type)
									 .replace('$employeeName', value.employee_name)
									 .replace('$time', time).replace('$place', value.place)
									 .replace('$price', value.price)
				});
				$('.receivables_body').html(htm);
			}
		});
	});
});

