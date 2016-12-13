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
		$('.delete_btn').on('click', function(){
			var id = $(this).parents('.btn-group').attr('data-id');
			$('#delete_receivables').find('input[name=id]').val(id);
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
	$('.print').on('click', function(){
		var id = $(this).attr('data-id');
		Common.ajax({
			data    :{requestType:'get_receivables', id:id},
			callback:function(r){
				console.log(r);
				var time = new Date(parseInt(r.creatime)*1000);
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
	/*$('.plus').on('click', function(){
	 if($(this).parents('.rece_item').find('.rece_body').hasClass('hide')){
	 $(this).parents('.rece_item').find('.rece_body').removeClass('hide');
	 $(this).removeClass('glyphicon-plus').addClass('glyphicon-minus');
	 }else{
	 $(this).parents('.rece_item').find('.rece_body').addClass('hide');
	 $(this).removeClass('glyphicon-minus').addClass('glyphicon-plus');
	 }
	 });*/
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
	ManageObject.object.meetingName.onQuasarSelect(function(){
		var value = ManageObject.object.meetingName.getValue();
		$add_receivables_modal.find('input[name=mid]').val(value);
	});
	ManageObject.object.clientName.onQuasarSelect(function(){
		var value = ManageObject.object.clientName.getValue();
		$add_receivables_modal.find('input[name=cid]').val(value);
	});
	// 修改收款
	$('.modify_btn').on('click', function(){
		var id = $(this).parents('.btn-group').attr('data-id');
		Common.ajax({
			data    :{requestType:'get_receivables_detail', id:id},
			callback:function(r){
				var $alter_receivables_object = $('#alter_receivables');
				var time                      = new Date(r.time*1000).format('yyyy-MM-dd HH:mm:ss');
				$alter_receivables_object.find('input[name=id]').val(id);
				//noinspection JSUnresolvedVariable
				$alter_receivables_object.find('#client_name_a').val(r.client_name);
				$alter_receivables_object.find('#price_a').val(r.price);
				$alter_receivables_object.find('#receivables_time_a').val(time);
				$alter_receivables_object.find('#comment_a').val(r.comment);
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
				ManageObject.object.payeeNameA.setHtml(r.payee_name);
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
})
;
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
