/**
 * Created by qyqy on 2016-9-22.
 */
var ThisObject = {
	optTemp          :'<option value="$numOpt">$name</option>',
	tableTemp        :'<tr>\n\t<td class="check_item_excel"><input type="checkbox" class="icheck_excel" value="$id"></td>\n\t<td>$num</td><td class="excel_name" data-id="$id">$value</td>\n\t<td>\n\t\t<select name="client_info" id="" class="form-control select_h">\n\t\t\t$opt\n\t\t</select>\n\t</td>\n</tr>',
	uploadInterval   :null,
	signBntTemp      :'<a class="btn btn-default btn-sm" href="javascript:void(0)" role="button" data-id="$id">$signName</a>',
	signActiveBntTemp:'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$signName</a>',
	detailsLiTemp    :'<tr><td>#key#</td><td>#name#</td></tr>'
};
$(function(){
	//排序
	var cur_order_column = $('#default_order_column').val();
	var cur_order_method = $('#default_order_method').val();
	$('#tableExcel').find('th[data-column]').on('click', function(){
		var order_column = $(this).attr('data-column');
		var order_method = url_object.getUrlParam('orderMethod');
		var new_url      = url_object.setUrlParam('orderColumn', order_column);
		if(cur_order_column == order_column){
			if(order_method == 'desc') order_method = 'asc';
			else if(order_method == 'asc') order_method = 'desc';
			else order_method = 'desc';
		}
		else order_method = 'asc';
		new_url = url_object.setUrlParam('orderMethod', order_method, new_url);
		location.replace(new_url);
	}).each(function(){
		var order_column = $(this).attr('data-column');
		if(order_column == cur_order_column){
			var column_word = $(this).text();
			var method_word = cur_order_method == 'desc' ? '▼' : '▲';
			$(this).css('background', '#EFEFEF').text(column_word+method_word);
		}
	});
	var quasar_script = document.getElementById('quasar_script');
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	var $search_config_modal = $('#search_config_modal');
	/**
	 * 搜索功能
	 */
	// 搜索配置
	$search_config_modal.find('.btn-item .btn').on('click', function(){
		if($(this).parent().hasClass('active')){
			$(this).parent().removeClass('active');
			$(this).removeClass('btn-info').addClass('btn-default');
		}else{
			$(this).parent().addClass('active');
			$(this).addClass('btn-info').removeClass('btn-default');
		}
		var str = [];
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$search_config_modal.find('input[name=search_field]').val(str);
	});
	// 全选搜索字段
	$('.sc_check_all').on('click', function(){
		$search_config_modal.find('.btn-item').each(function(){
			if(!$(this).hasClass('active')){
				$(this).addClass('active');
				$(this).find('.btn').addClass('btn-info').removeClass('btn-default');
			}
		});
		var str = [];
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$search_config_modal.find('input[name=search_field]').val(str);
	});
	// 取消
	$('.sc_cancel').on('click', function(){
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$(this).find('.btn').addClass('btn-default').removeClass('btn-info');
			}
		});
		var str = [];
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$search_config_modal.find('input[name=search_field]').val(str);
	});
	// 搜索配置提交
	$search_config_modal.find('.btn-save').on('click', function(){
		var data = $('#search_config_modal').find('form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					})
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		});
	});
	$('ul.pagination a[href]').each(function(){
		$(this).attr('href', encodeURI($(this).attr('href')));
	});
	/**
	 *  右侧详情
	 */
	$('.details_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		Common.ajax({
			data    :{requestType:'get_detail', id:id},
			callback:function(r){
				var str = '';
				console.log(r);
				$.each(r[0], function(index1, value1){
					$.each(r[1], function(index2, value2){
						if(index1 == index2){
							str += ThisObject.detailsLiTemp.replace('#key#', value1)
								.replace('#name#', value2)
						}
					})
				});
				$('.info').html(str);
			}
		});
		$('.add_client').on('click', function(){
			var $add_recipient2 = $('#add_recipient2');
			var per = $add_recipient2.find('input[name=can_live]').val();
			$add_recipient2.modal('show');
			$add_recipient2.find('.can_live_p').text(per);
		});
		$('.add_employee').on('click', function(){
			var $add_recipient2_employee = $('#add_recipient2_employee');
			var per = $add_recipient2_employee.find('input[name=can_live]').val();
			$add_recipient2_employee.modal('show');
			$add_recipient2_employee.find('.can_live_p').text(per);
		});
		$('#add_recipient2').find('input[name=room_id]').val(id);
		$('#add_recipient2_employee').find('input[name=room_id]').val(id);
	});
	/**
	 * 礼品功能
	 */
	// 领取礼物
	$('.btn_gift').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'gift', id:id},
			callback:function(res){
				ManageObject.object.loading.complete();
				if(res.status){
					ManageObject.object.toast.toast(res.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					});
				}else{
					ManageObject.object.toast.toast(res.message, '2');
				}
			}
		});
	});
	// 退还礼物
	$('.anti_btn_gift').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'refund_gift', id:id},
			callback:function(res){
				ManageObject.object.loading.complete();
				if(res.status){
					ManageObject.object.toast.toast(res.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					});
				}else{
					ManageObject.object.toast.toast(res.message, '2');
				}
			}
		});
	});
	// 批量领取礼物
	$('.batch_receive_gift').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_receive_gift_modal').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_receive_gift_modal').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_receive_gift_modal').find('input[name=id]').val(newStr);
		$('#batch_receive_gift_modal .btn-save').on('click', function(){
			var data = $('#batch_receive_gift_modal form').serialize();
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
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		});
	});
	// 批量退还礼物
	$('.batch_refund_gift').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_refund_gift_modal').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_refund_gift_modal').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_refund_gift_modal').find('input[name=id]').val(newStr);
		$('#batch_refund_gift_modal .btn-save').on('click', function(){
			var data = $('#batch_refund_gift_modal form').serialize();
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
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		});
	});
	// 显示结果数
	$('.table_length').find('select').on('change', function(){
		var number = $(this).find('option:selected').text();
		var url    = url_object.setUrlParam('pageCount', number);
		url        = url_object.delUrlParam('p', url);
		location.replace(url);
	});
	// 页面显示列表数下拉框默认值处理
	(function(){
		var page_count = url_object.getUrlParam('pageCount');
		$('.table_length').find('select option').each(function(){
			var num = $(this).text();
			if(page_count == num){
				$('.table_length').find('select option:selected').removeProp('selected').removeAttr('selected');
				$(this).prop('selected', true).attr('selected', true);
				return true;
			}
		});
	})();
	$('.batch_badge').on('click', function(){
		var str = '', i = 0, para = '';
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
			para += id+'*';
		});
		var para      = para.substring(0, para.length-1);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			var url = url_object.setUrlParam('cid', para, ManageObject.data.badgePrintUrl);
			window.open(url);
			//location.href = url;
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
	});
	// 已签到客户列表
	$('.check_signed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('signed');
		var new_url = link.delUrlParam('p');
		if(param == 1){
			new_url = link.delUrlParam('signed', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('signed', 1, new_url);
			location.replace(new_url);
		}
	});
	// 未签到客户列表
	$('.check_not_signed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('signed');
		var new_url = link.delUrlParam('p');
		if(param == 0){
			new_url = link.delUrlParam('signed', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('signed', 0, new_url);
			location.replace(new_url);
		}
	});
	// 已审核客户列表
	$('.check_reviewed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('reviewed');
		var new_url = link.delUrlParam('p');
		if(param == 1){
			new_url = link.delUrlParam('reviewed', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('reviewed', 1, new_url);
			location.replace(new_url);
		}
	});
	// 未审核客户列表
	$('.check_not_reviewed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('reviewed');
		var new_url = link.delUrlParam('p');
		if(param == 0){
			new_url = link.delUrlParam('reviewed', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('reviewed', 0, new_url);
			location.replace(new_url);
		}
	});
	/*// 已收款客户列表
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
	 });*/
	// 客户类型列表
	$('.client').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('is_employee');
		var new_url = link.delUrlParam('p');
		if(param == 0){
			new_url = link.delUrlParam('is_employee', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('is_employee', 0, new_url);
			location.replace(new_url);
		}
	});
	// 员工类型列表
	$('.employee').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('is_employee');
		var new_url = link.delUrlParam('p');
		if(param == 1){
			new_url = link.delUrlParam('is_employee', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('is_employee', 1, new_url);
			location.replace(new_url);
		}
	});
	//	// 新客户列表
	//	$('.new_client').find('.iCheck-helper').on('click', function(){
	//		var $quasar = $('#quasar_script');
	//		var mvc     = $quasar.attr('data-url-sys-param');
	//		var suffix  = $quasar.attr('data-page-suffix');
	//		var link    = new Quasar.UrlClass(1, mvc, suffix);
	//		var param   = link.getUrlParam('client_type');
	//		var new_url = link.delUrlParam('p');
	//		if(param == 1){
	//			new_url = link.delUrlParam('client_type', new_url);
	//			location.replace(new_url);
	//		}else{
	//			new_url = link.setUrlParam('client_type', 1, new_url);
	//			location.replace(new_url);
	//		}
	//	});
	//	// 老客户列表
	//	$('.old_client').find('.iCheck-helper').on('click', function(){
	//		var $quasar = $('#quasar_script');
	//		var mvc     = $quasar.attr('data-url-sys-param');
	//		var suffix  = $quasar.attr('data-page-suffix');
	//		var link    = new Quasar.UrlClass(1, mvc, suffix);
	//		var param   = link.getUrlParam('client_type');
	//		var new_url = link.delUrlParam('p');
	//		if(param == 0){
	//			new_url = link.delUrlParam('client_type', new_url);
	//			location.replace(new_url);
	//		}else{
	//			new_url = link.setUrlParam('client_type', 0, new_url);
	//			location.replace(new_url);
	//		}
	//	});
	// 可用列表
	$('.usable').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('status');
		var new_url = link.delUrlParam('p');
		if(param == 1){
			new_url = link.delUrlParam('status', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('status', 1, new_url);
			location.replace(new_url);
		}
	});
	// 禁用列表
	$('.disable').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('status');
		var new_url = link.delUrlParam('p');
		if(param == 0){
			new_url = link.delUrlParam('status', new_url);
			location.replace(new_url);
		}else{
			new_url = link.setUrlParam('status', 0, new_url);
			location.replace(new_url);
		}
	});
	//审核按钮
	$('.review_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'review', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast(r.message, 1);
				}else{
					ManageObject.object.toast.toast(r.message, 2);
				}
			}
		});
	});  //alter
	// 取消审核按钮
	$('.anti_review_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'cancel_review', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast(r.message, 1);
				}else{
					ManageObject.object.toast.toast(r.message, 1);
				}
			}
		});
	});  //alter
	// 批量审核客户
	$('.batch_review_btn_confirm').on('click', function(){
		var str = [];
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		$('#batch_review_client').find('.sAmount').text(i);
		str = str.join(',');
		if(str != ''){
			$('#batch_review_client').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_review_client').find('input[name=id]').val(str);
		$('#batch_review_client .btn-save').on('click', function(){
			var data = $('#batch_review_client form').serialize(); // 表单序列化
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
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			})
		});
	});  // alter
	// 批量取消审核客户
	$('.batch_anti_review_btn_confirm').on('click', function(){
		var str = [];
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		$('#batch_anti_review_client').find('.sAmount').text(i);
		str = str.join(',');
		if(str != ''){
			$('#batch_anti_review_client').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_anti_review_client').find('input[name=id]').val(str);
		$('#batch_anti_review_client .btn-save').on('click', function(){
			var data = $('#batch_anti_review_client form').serialize(); // 表单序列化
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
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			})
		});
	});  // alter
	// 签到按钮
	$('.sign_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'sign', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, 1);
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
				}else{
					ManageObject.object.toast.toast(r.message, 1);
				}
			}
		});
	});  // alter v2
	// 取消签到按钮
	$('.anti_sign_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'cancel_sign', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast(r.message, 1);
				}else{
					ManageObject.object.toast.toast(r.message, 1);
				}
			}
		});
	});  // alter v2
	// 批量签到
	$('.batch_sign_point').on('click', function(){
		var str = [];
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		str = str.join(',');
		$('#batch_sign_point').find('.sAmount').text(i);
		if(str != ''){
			$('#batch_sign_point').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_sign_point').find('input[name=id]').val(str);
		$('#batch_sign_point .btn-save').on('click', function(){
			var data = $('#batch_sign_point form').serialize(); // 表单序列化
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
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			})
		})
	});  // alter
	// 批量取消签到
	$('.batch_anti_sign_point ').on('click', function(){
		var str = [];
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		str = str.join(',');
		$('#batch_anti_sign_point').find('.sAmount').text(i);
		if(str != ''){
			$('#batch_anti_sign_point').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_anti_sign_point').find('input[name=id]').val(str);
		$('#batch_anti_sign_point .btn-save').on('click', function(){
			var data = $('#batch_anti_sign_point form').serialize(); // 表单序列化
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
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			})
		})
	});  // alter
	// 发送消息
	$('.send_message_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'send_message', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					var message = r.message+'<br>';
					for(var key in r.data){
						//noinspection JSUnfilteredForInLoop
						message += r.data[key].type+'发送'+r.data[key].count+'条<br>';
					}
					ManageObject.object.toast.toast(message, 3);
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
				}
				else ManageObject.object.toast.toast(r.message, 2);
			}
		});
	});
	// 批量发送消息
	$('.batch_send_message_btn_confirm').on('click', function(){
		var str = '', i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_send_message').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_send_message').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_send_message').find('input[name=id]').val(newStr);
		$('#batch_send_message .btn-save').on('click', function(){
			var data = $('#batch_send_message form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						var message = r.message+'<br>';
						for(var key in r.data){
							//noinspection JSUnfilteredForInLoop
							message += r.data[key].type+'发送'+r.data[key].count+'条<br>';
						}
						ManageObject.object.toast.toast(message, 3);
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload(true);
						});
					}
					else ManageObject.object.toast.toast(r.message, 2);
				}
			})
		});
	});
	// 分配签到点 (single)
	$('.alter_sign_point_btn').on('click', function(){
		var cid = $(this).parent().attr('data-id');
		var str = '';
		$('#alter_sign_place_cid').val(cid);
		Common.ajax({
			data    :{requestType:'get_assign_sign_place', cid:cid},
			callback:function(r){
				$.each(r.data, function(index, value){
					if(value.assign_status == 1){
						str += ThisObject.signActiveBntTemp.replace('$id', value.id).replace('$signName', value.name);
					}else if(value.assign_status == 0){
						str += ThisObject.signBntTemp.replace('$id', value.id).replace('$signName', value.name);
					}
				});
				$('#alter_sign_point').find('.coupon_list').html(str);
				$('#alter_sign_point .coupon_list a').on('click', function(){
					if($(this).hasClass('active')){
						$(this).removeClass('active');
					}else{
						$(this).addClass('active');
					}
					var arr = [];
					$('#alter_sign_point .coupon_list a.active').each(function(){
						var id = $(this).attr('data-id');
						arr.push(id);
					});
					$('#alter_sign_point').find('input[name=sign_place]').val(arr);
				});
			}
		});
	});
	// 分配签到点 (multi)
	$('.assign_sign_place').on('click', function(){
		var str = '';
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+','
		});
		str = str.substr(0, str.length-1);
		if(str != ''){
			$('#batch_alter_sign_point').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#alter_multi_sign_place_cid').val(str).attr('value', str);
	});
	$('#batch_alter_sign_point .coupon_list a').on('click', function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
		var arr = [];
		$('#batch_alter_sign_point .coupon_list a.active').each(function(){
			var id = $(this).attr('data-id');
			arr.push(id);
		});
		$('#batch_alter_sign_point').find('input[name=sign_place]').val(arr);
	});
	// 分组 (multi)
	$('.batch_group_btn').on('click', function(){
		var str = [];
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
		});
		if(str != ''){
			$('#batch_grouping').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#grouping_cid').val(str);
		$('#batch_grouping .btn-save').on('click', function(){
			var data = $('#batch_grouping form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						})
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			});
		});
	});
	// 分房 (multi)
	$('.batch_room_btn').on('click', function(){
		var str = [];
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var cid = $(this).find('.icheck').val();
			str.push(cid);
		});
		if(str != ''){
			$('#batch_choose_room').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_choose_room input[name=cid]').val(str);
		Common.ajax({
			data    :{requestType:'get_hotel_list'},
			callback:function(r){
				var str = '<option value=\'0\'>--请选择房间--</option>';
				$.each(r, function(index, value){
					console.log(value);
					str += '<option value='+value.value+'>'+value.html+'</option>'
				});
				$('#hotel').html(str);
				$('#hotel').on('change', function(){
					var hid = $(this).val();
					Common.ajax({
						data    :{requestType:'get_room_list', hid:hid},
						callback:function(res){
							ManageObject.object.room.update(res);
						}
					})
				});
			}
		});
		// 保存分房
		$('#batch_choose_room .btn-save').on('click', function(){
			var data = $('#batch_choose_room form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						})
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			});
		});
	});
	// 复制
	$('.btn_copy').on('click', function(){
		var cid = $(this).parent('.btn-group').attr('data-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'copy', cid:cid},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.href = r.nextPage
					})
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		})
	});
	// 同步微信数据
	$('#synchronous_wx_modal .btn-save').on('click', function(){
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'synchronize_wechat_information'},
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
		});
	});
	// 初始化筛选条件选中样式
	(function(){
		// 人员状态列表（签到\审核\收款）
		var mvc         = $('#quasar_script').attr('data-url-sys-param');
		var suffix      = $('#quasar_script').attr('data-page-suffix');
		var link        = new Quasar.UrlClass(1, mvc, suffix);
		var signed      = link.getUrlParam('signed');
		var reviewed    = link.getUrlParam('reviewed');
		//var receivables = link.getUrlParam('receivables');
		var client_type = link.getUrlParam('client_type');
		var status      = link.getUrlParam('status');
		var is_employee = link.getUrlParam('is_employee');
		if(signed == 1) $('.check_signed').find('.iradio_square-green').addClass('checked');
		if(signed == 0) $('.check_not_signed').find('.iradio_square-green').addClass('checked');
		if(reviewed == 1) $('.check_reviewed').find('.iradio_square-blue').addClass('checked');
		if(reviewed == 0) $('.check_not_reviewed').find('.iradio_square-blue').addClass('checked');
		/*if(receivables == 1) $('.check_receivables').find('.iradio_square-red').addClass('checked');
		 if(receivables == 0) $('.check_not_receivables').find('.iradio_square-red').addClass('checked');*/
		//		if(client_type == 1) $('.new_client').find('.iradio_square-red').addClass('checked');
		//		if(client_type == 0) $('.old_client').find('.iradio_square-red').addClass('checked');
		if(status == 1) $('.usable').find('.iradio_square-yellow').addClass('checked');
		if(status == 0) $('.disable').find('.iradio_square-yellow').addClass('checked');
		if(is_employee == 1) $('.employee').find('.iradio_square-red').addClass('checked');
		if(is_employee == 0) $('.client').find('.iradio_square-red').addClass('checked');
	})();
	/**
	 * 双击表格弹出编辑界面
	 */
	$('#tableExcel .can-alter').on('dblclick', function(eve){
		var html  = $(this).find('.hide-form').html();
		var cid   = $(this).parent('tr').attr('data-cid');
		var table = $(this).find('input[name=_table]').val();
		var self  = $(this);
		$(this).IcemanAlterPopup({
			html       :html,
			requestType:'modify_column',
			cid        :cid,
			eve        :eve,
			success    :function(res){
				self.find('.need-alter').text(res.value);
				self.find('.sub_val').attr('value', res.value);
				ManageObject.object.toast.toast(res.message, '1');
			}, error   :function(res){
				ManageObject.object.toast.toast(res.message, '1');
			}
		})
		;
	});
	// 批量新增客户
	$('.batch_create_btn').on('click', function(){
		$('#batch_create_client').modal('show');
	});
	// 批量提交客户
	$('#batch_create_client .btn_save').on('click', function(){
		var formData = $('#batch_create_form').serialize();
		console.log(formData);
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :formData,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.href = r.nextPage
					})
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		})
	});
	// 客户记录字段重复判定字段 点击事件处理
	$('.client_repeat_mode').on('click', function(){
		var column_name = $(this).attr('data-column');
		if($(this).hasClass('active')){
			$(this).removeClass('active btn-info');
			document.getElementById(column_name).value = 0;
		}else{
			$(this).addClass('active btn-info');
			document.getElementById(column_name).value = 1;
		}
	});
	// 客户记录字段重复时执行动作 点击事件处理
	$('.client_repeat_action').on('click', function(){
		var action = $(this).attr('data-action');
		var value  = $(this).attr('data-value');
		$('.client_repeat_action').removeClass('active btn-info');
		$(this).addClass('active btn-info');
		$('input[name=client_repeat_action][type=hidden]').val(value);
	});
	// cookie储存已选参会人员
	$('.check_item .iCheck-helper').on('click', function(){
		var str = [];
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		str        = str.join(',');
		var cookie = new Quasar.CookieClass();
		cookie.setCookie('checkedStr_client', str, 1);
	});
	// 初始化已选参会人员
	try{
		(function(){
			var cookie            = new Quasar.CookieClass();
			var checkedStr_client = cookie.getCookie('checkedStr_client');
			$('.check_item .icheck').each(function(){
				var id = $(this).parents('tr').attr('data-cid');
				if(checkedStr_client.indexOf(id)>=0){
					$(this).iCheck('check');
				}
			});
		})();
	}catch(error){
		console.log(error);
	}
	/**
	 * 列表字段功能
	 */
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
	/**
	 * 客户字段控制
	 */
	// 选中字段操作
	$('.field_checkbox .icheck_f').on('ifChecked', function(){
		var t_code = $(this).parent().find('.icheck_f').attr('data-code');
		$('#field_list .btn').each(function(){
			var m_code = $(this).attr('data-code');
			if(t_code == m_code){
				$(this).addClass('show').removeClass('hide');
				$(this).find('input.c_view').val(1);
			}
		});
	});
	// 取消选中字段操作
	$('.field_checkbox .icheck_f').on('ifUnchecked', function(){
		var t_code = $(this).parent().find('.icheck_f').attr('data-code');
		$('#field_list .btn').each(function(){
			var m_code = $(this).attr('data-code');
			if(t_code == m_code){
				$(this).addClass('hide').removeClass('show');
				$(this).find('input.c_view').val(0);
			}
		});
	});
	// 保存列表字段设置
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
})