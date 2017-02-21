/**
 * Created by 1195 on 2016-10-19.
 */

var ThisObject = {
	aTemp               :'<a class="btn btn-default btn-sm " href="javascript:void(0)" role="button" data-id="$id">$name</a>',
	aActiveTemp         :'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$name</a>',
	uploadInterval      :null,
	trTemp              :'<tr><td colspan="2" class="type2" style="text-align: center">$type</td>\n\t<td colspan="2" class="price2" style="text-align: center">$price</td>\n</tr>',
	word                :0,
	option              :'<option value="$id" data-price="$price">$name</option>',
	receivablesTemp     :'<div class="form-group">\n\t<label class="col-sm-2 control-label"><span class="delete_rece_btn glyphicon glyphicon-minus-sign"></span></label>\n\t<div class="col-sm-10">\n\t\t<div class="col-sm-3">\n\t\t\t<div class="form-group">\n\t\t\t\t<label class="col-sm-4 control-label">支付方式：</label>\n\t\t\t\t<div class="col-sm-8">\n\t\t\t\t\t<select name="method[]" class="form-control">\n\t\t\t\t\t</select>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t\t<div class="col-sm-3">\n\t\t\t<div class="form-group">\n\t\t\t\t<label class="col-sm-4 control-label">POS机：</label>\n\t\t\t\t<div class="col-sm-8">\n\t\t\t\t\t<select name="pos_id[]" class="form-control">\n\t\t\t\t\t</select>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t\t<div class="col-sm-3">\n\t\t\t<div class="form-group">\n\t\t\t\t<label class="col-sm-4 control-label">来源状态：</label>\n\t\t\t\t<div class="col-sm-8">\n\t\t\t\t\t<select name="source_type[]" class="form-control">\n\t\t\t\t\t\t<option value="0">会前收款</option>\n\t\t\t\t\t\t<option value="1">会中收款</option>\n\t\t\t\t\t\t<option value="2">会后收款</option>\n\t\t\t\t\t</select>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t\t<div class="col-sm-3">\n\t\t\t<div class="form-group">\n\t\t\t\t<label class="col-sm-4 control-label">备注：</label>\n\t\t\t\t<div class="col-sm-8">\n\t\t\t\t\t<input class="form-control comment" name="comment[]">\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>',
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
			var order_number = $(this).attr('data-order-number');
			var cid = $(this).attr('data-client-id');
			$('#delete_receivables').find('input[name=cid]').val(cid);
			$('#delete_receivables').find('input[name=order_number]').val(order_number);
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
		$('.alter_number').on('click', function(){
			$('#alter_number_modal').modal('show');
			var main_id = $(this).attr('data-main-id');
			var document_number = $(this).parents('.item_order').find('.document_number').text();
			$('#alter_number_modal').find('input[name=main_id]').val(main_id);
			$('#alter_number_modal').find('#document_number').val(document_number);
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
	$('.print').on('click', function(){
		var id = $(this).attr('data-id');
		Common.ajax({
			data    :{requestType:'get_receivables_by_client', id:id},
			callback:function(r){
				console.log(r);
				var time = new Date(parseInt(r.creatime)*1000);
				time     = time.format('yyyy年MM月dd日');
				$('#print').find('.time').text(time);
				//$('#print').find('.unit').text(r.unit);
				$('#print').find('.client_name').text(r.client);
				$('#print').find('.project_type').text(r.receivables_type+'('+r.coupon_name+')');
				$('#print').find('.price_capital').text(r.price_word);
				$('#print').find('.identifier').text('No.'+r.order_number);
				$('#print').find('.price').text(r.price);
				$('#print').find('.payee').text(r.payee);
				/*if(r.comment == null){
				 $('#print').find('.comment_wrap').removeClass('hide');
				 $('#print').find('.comment').text(r.comment);
				 }else{
				 $('#print').find('.comment_wrap').addClass('hide');
				 }*/
				var str = '', i = 0, commentStr = '';
				$.each(r.option.pay_method_list, function(index, value){
					commentStr += value.comment+'';
					/*	console.log(value);*/
					//console.log(value.pos_machine);
					if(index == 0){
						if(value.name == '刷卡'){
							if(value.pos_machine == null){
								$('.type1').text(value.name);
								$('.price1').text(value.price);
							}else{
								$('.type1').text(value.name+'('+value.pos_machine+')');
								$('.price1').text(value.price);
							}
						}else{
							$('.type1').text(value.name);
							$('.price1').text(value.price);
						}
					}else{
						if(value.name == '刷卡'){
							if(value.pos_machine == null){
								console.log(value.pos_machine);
								str += ThisObject.trTemp.replace('$type', value.name)
												 .replace('$price', value.price);
							}else{
								str += ThisObject.trTemp.replace('$type', value.name+'('+value.pos_machine+')')
												 .replace('$price', value.price);
							}
						}else{
							str += ThisObject.trTemp.replace('$type', value.name).replace('$price', value.price);
						}
					}
					i++;
				});
				if(commentStr){
					$('#print').find('.comment_wrap').removeClass('hide');
					$('#print').find('.comment').text(commentStr);
				}else{
					$('#print').find('.comment_wrap').addClass('hide');
				}
				$('.sign_tr').find('.rmb').attr('rowspan', i);
				$('.sign_tr').after(str);
				/*	setTimeout(function(){
				 $("#print").printArea();
				 location.reload();
				 },1000);*/
			}
		})
	});
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
	/*	ManageObject.object.meetingName.onQuasarSelect(function(){
	 var value = ManageObject.object.meetingName.getValue();
	 $add_receivables_modal.find('input[name=mid]').val(value);
	 });
	 ManageObject.object.clientName.onQuasarSelect(function(){
	 var value = ManageObject.object.clientName.getValue();
	 $add_receivables_modal.find('input[name=cid]').val(value);
	 });*/
	// 修改收款
	/*$('.modify_btn').on('click', function(e){
	 e.stopPropagation();
	 var type = $(this).parents('.header').attr('data-type');
	 if(type == 2){
	 $('#alter_receivables .coupon_group').removeClass('hide');
	 $('#alter_receivables .coupon_group').addClass('hide');
	 }else{
	 $('#alter_receivables .coupon_group').addClass('hide');
	 $('#alter_receivables .coupon_group').removeClass('hide');
	 }
	 $('#alter_receivables').modal('show');
	 var id = $(this).parent('.header').attr('data-id');
	 Common.ajax({
	 data    :{requestType:'get_receivables_detail', id:id},
	 callback:function(r){
	 console.log(r);
	 var $alter_receivables_object = $('#alter_receivables');
	 var time                      = new Date(r.time*1000).format('yyyy-MM-dd HH:mm:ss');
	 $alter_receivables_object.find('input[name=id]').val(id);
	 //noinspection JSUnresolvedVariable
	 $alter_receivables_object.find('#client_name_a').val(r.client);
	 $alter_receivables_object.find('#price_a').val(r.price);
	 $alter_receivables_object.find('#receivables_time_a').val(time);
	 //$alter_receivables_object.find('#comment_a').val(r.comment);
	 $alter_receivables_object.find('#place_a').val(r.place);

	 $alter_receivables_object.find('#source_type_a>option').prop('selected', false);
	 $alter_receivables_object.find('#source_type_a').val(r.source_type);
	 $alter_receivables_object.find('#source_type_a>option[value='+r.source_type+']').prop('selected', true);
	 $alter_receivables_object.find('#type_a>option').prop('selected', false);
	 $alter_receivables_object.find('#type_a').val(r.type);
	 $alter_receivables_object.find('#type_a>option[value='+r.type+']').prop('selected', true);
	 $alter_receivables_object.find('#method_a>option').prop('selected', false);
	 $alter_receivables_object.find('#method_a').val(r.method);
	 $alter_receivables_object.find('#method_a>option[value='+r.method+']').prop('selected', true);
	 $alter_receivables_object.find('#pos_id_a>option').prop('selected', false);
	 $alter_receivables_object.find('#pos_id_a').val(r.pos_id);
	 $alter_receivables_object.find('#pos_id_a>option[value='+r.pos_id+']').prop('selected', true);
	 ManageObject.object.payeeNameA.setValue(r.payee_id);
	 ManageObject.object.payeeNameA.setHtml(r.payee);
	 }
	 });
	 Common.ajax({
	 data    :{requestType:'alter_coupon', id:id},
	 callback:function(r){
	 var arr = [], str = '';
	 //$('#alter_receivables .no_c').hide();
	 $.each(r, function(index1, value1){
	 $.each(value1, function(index2, value2){
	 if(index1 == 'coupon_item_yes'){
	 if(value2 != null){
	 str += ThisObject.aActiveTemp.replace('$id', value2.id)
	 .replace('$name', value2.code);
	 arr.push(value2.id);
	 }
	 }
	 if(index1 == 'coupon_item_not'){
	 if(value2 != null){
	 str += ThisObject.aTemp.replace('$id', value2.id)
	 .replace('$name', value2.code);
	 }
	 }
	 });
	 });
	 if(str == ''){
	 $('#alter_receivables .no_c').show();
	 }else{
	 // 记录之前已选择的代金券
	 $('#alter_receivables').find('input[name=old_coupon_code]').val(arr);
	 //输出所有代金券
	 $('#alter_receivables .coupon_list').html(str);
	 }
	 ThisObject.unbindEvent();
	 ThisObject.bindEvent();
	 }
	 });
	 });*/
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
	/**
	 * 1、收款类型分为门票和代金券
	 * 2、当选择的select 值为1 ，则为门票
	 * 3、当选择的select 值为2 ，则为代金券
	 */
	$('#type').on('change', function(){
		if($(this).find('option:selected').val() == 1){
			ManageObject.object.loading.loading();
			var str = '';
			Common.ajax({
				data    :{requestType:'ticket', code:1},
				callback:function(data){
					ManageObject.object.loading.complete();
					console.log(data);
					$('.list_form').removeClass('hide');
					$('.list_form .ticket_h').removeClass('hide');
					$('.list_form .coupon_h').addClass('hide');
					$('#add_receivables .modal-footer').removeClass('hide');
					$.each(data, function(index, value){
						console.log(value);
						str += ThisObject.option.replace('$id', value.id).replace('$name', value.name)
										 .replace('$price', value.price);
					});
					console.log(str);
					$('#ticket_type').html(str).prepend('<option value="0" selected>请选择门票类型</option>');
					$('#ticket_type').on('change', function(){
						var price = $(this).find('option:selected').attr('data-price');
						$('#price').val(price);
					});
				}
			})
		}else if($(this).find('option:selected').val() == 2){
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'coupon', code:2},
				callback:function(data){
					ManageObject.object.loading.complete();
					console.log(data);
					$('.list_form').removeClass('hide');
					$('.list_form .ticket_h').addClass('hide');
					$('.list_form .coupon_h').removeClass('hide');
					$('#add_receivables .modal-footer').removeClass('hide');
					$.each(data, function(index, value){
						console.log(value);
					});
				}
			})
		}
	})
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
// 添加收款不为空控制
function checkIsEmpty(){
	var $add_receivables = $('#add_receivables');
	var name             = $add_receivables.find('#selected_client_name').val();
	var price            = $add_receivables.find('#price').val();
	if(name == ''){
		ManageObject.object.toast.toast('客户姓名不能为空');
		$add_receivables.find('#selected_client_name').focus();
		return false;
	}
	if(price == ''){
		ManageObject.object.toast.toast('金额不能为空');
		$add_receivables.find('#price').focus();
		return false;
	}
	return true;
}
// 修改收款不为空控制
function checkIsEmptyAlter(){
	var $alter_receivables = $('#alter_receivables');
	var name               = $alter_receivables.find('#selected_client_name').val();
	var price              = $alter_receivables.find('#price').val();
	if(name == ''){
		ManageObject.object.toast.toast('客户姓名不能为空');
		$alter_receivables.find('#selected_client_name').focus();
		return false;
	}
	if(price == ''){
		ManageObject.object.toast.toast('金额不能为空');
		$alter_receivables.find('#price').focus();
		return false;
	}
	return true;
}
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
