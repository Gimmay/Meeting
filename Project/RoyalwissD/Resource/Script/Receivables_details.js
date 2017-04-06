/**
 * Created by 1195 on 2016-10-19.
 */

var ThisObject     = {
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
			var cid          = $(this).attr('data-client-id');
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
			var main_id         = $(this).attr('data-main-id');
			var document_number = $(this).parents('.rece_details_item').find('.document_number').text();
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
var digitUppercase = function(n){
	var fraction = ['角', '分'];
	var digit    = [
		'零', '壹', '贰', '叁', '肆',
		'伍', '陆', '柒', '捌', '玖'
	];
	var unit     = [
		['元', '万', '亿'],
		['', '拾', '佰', '仟']
	];
	var head     = n<0 ? '欠' : '';
	n            = Math.abs(n);
	var s        = '';
	for(var i = 0; i<fraction.length; i++){
		s += (digit[Math.floor(n*10*Math.pow(10, i))%10]+fraction[i]).replace(/零./, '');
	}
	s = s || '整';
	n = Math.floor(n);
	for(var i = 0; i<unit[0].length && n>0; i++){
		var p = '';
		for(var j = 0; j<unit[1].length && n>0; j++){
			p = digit[n%10]+unit[1][j]+p;
			n = Math.floor(n/10);
		}
		s = p.replace(/(零.)*零$/, '').replace(/^$/, '零')+unit[0][i]+s;
	}
	return head+s.replace(/(零.)*零元/, '元')
				 .replace(/(零.)+/g, '零')
				 .replace(/^整$/, '零元整');
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
		var order_number = $(this).attr('data-order-number');
		var client_id    = $(this).attr('data-client-id');
		Common.ajax({
			data    :{requestType:'get_receivables', client_id:client_id, order_number:order_number},
			callback:function(r){
				console.log(r);
				var time = new Date(parseInt(r.time)*1000);
				time     = time.format('yyyy年MM月dd日');
				$('#print').find('.time').text(time);
				//				$('#print').find('.unit').text(r.unit);
				$('#print').find('.client_name').text(r.client_name);
				console.log(r.project);
				var pro = '', i = 1;
				$.each(r.project, function(index, value){
					var n = Number(index);
					pro += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+i+'、'+value.project+'&nbsp;&nbsp;&nbsp;(金额：'+value.price+')'+'&nbsp;&nbsp;&nbsp;('+'备注：'+value.comment+')'+'<br>';
					i++;
				});
				//pro=pro.substring(0,pro.length-1);
				console.log(pro);
				$('#print').find('.project_type').html(pro); //项目
				$('#print').find('.price_capital').text(digitUppercase(r.price)); //大写
				$('#print').find('.identifier').text('No.'+order_number); //收据号
				$('#print').find('.price').text(r.price); //金额
				$('#print').find('.payee').text(r.payee_name); //开收据的人
				/*if(r.comment == null){
				 $('#print').find('.comment_wrap').removeClass('hide');
				 $('#print').find('.comment').text(r.comment);
				 }else{
				 $('#print').find('.comment_wrap').addClass('hide');
				 }*/
				var str = '', ii = 0, nn = 0, commentStr = '';
				$.each(r.option, function(index, value){
					console.log(r.option)
					console.log(nn);
					commentStr += value.comment+'';
					/*	console.log(value);*/
					//console.log(value.pos_machine);
					if(nn == 0){
						$('.type1').text(value.pay_method);
						$('.price1').text(value.price);
					}else{
						str += ThisObject.trTemp.replace('$type', value.pay_method).replace('$price', value.price);
					}
					nn++;
					ii++;
				});
				/*if(commentStr){
				 $('#print').find('.comment_wrap').removeClass('hide');
				 $('#print').find('.comment').text(commentStr);
				 }else{
				 $('#print').find('.comment_wrap').addClass('hide');
				 }*/
				$('.sign_tr').find('.rmb').attr('rowspan', ii);
				$('.sign_tr').after(str);
				preview(1);
				location.reload();
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
	var cur_order_column = $('#default_order_column').val();
	var cur_order_method = $('#default_order_method').val();
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
function preview(oper){
	if(oper<10){
		bdhtml                         = window.document.body.innerHTML;//获取当前页的html代码
		sprnstr                        = "<!--startprint"+oper+"-->";//设置打印开始区域
		eprnstr                        = "<!--endprint"+oper+"-->";//设置打印结束区域
		prnhtml                        = bdhtml.substring(bdhtml.indexOf(sprnstr)+18); //从开始代码向后取html
		prnhtml                        = prnhtml.substring(0, prnhtml.indexOf(eprnstr));//从结束代码向前取html
		window.document.body.innerHTML = prnhtml;
		window.print();
		window.document.body.innerHTML = bdhtml;
	}else{
		window.print();
	}
}