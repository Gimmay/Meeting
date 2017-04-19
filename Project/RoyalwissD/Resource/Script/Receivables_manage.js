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
		$('.delete_btn').on('click', function(){
			$('#delete_receivables').modal('show');
			var id = $(this).parent().attr('data-id');
			$('#delete_receivables').find('input[name=id]').val(id);
		});
		// 修改收款基本信息
		$('.modify_btn').on('click', function(){
			$('#alter_public_modal').modal('show');
			var id = $(this).parent().attr('data-id');
			$('#alter_public_modal').find('input[name=id]').val(id);
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'get_order', id:id},
				callback:function(r){
					ManageObject.object.loading.complete();
					$('#alter_public_modal').find('#order_number').val(r.order_number); //
					ManageObject.object.clientName.setHtml(r.client); //客户
					ManageObject.object.clientName.setValue(r.cid);
					ManageObject.object.payeeId.setHtml(r.payee); //收款人
					ManageObject.object.payeeId.setValue(r.payee_code);
					$('#alter_public_modal').find('#receivables_time1').val(r.time);
					$('#alter_public_modal').find('#place').val(r.place);
					$('#alter_public_modal .btn-save').on('click', function(){
						var data = $('#alter_public_modal form').serialize();
						ManageObject.object.loading.loading();
						Common.ajax({
							data    :data,
							callback:function(r){
								ManageObject.object.loading.complete();
								if(r.status){
									ManageObject.object.toast.toast(r.message, '1');
									ManageObject.object.toast.onQuasarHidden(function(){
										location.reload();
									});
								}else{
									ManageObject.object.toast.toast(r.message, '2');
								}
							}
						});
					});
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
	$('#updateBackground').on('change', function(){
		$('input[name=requestType]').val('upload_receivables_order_logo');
		$('#submit_logo').trigger('click');
	});
	// 收款详情展开隐藏
	$('.rece_item .plus').on('click', function(){
		if($(this).parents('.rece_item').find('.rece_body').hasClass('hide')){
			$(this).parents('.rece_item').find('.rece_body').removeClass('hide');
			$(this).removeClass('glyphicon-plus').addClass('glyphicon-minus');
		}else{
			$(this).parents('.rece_item').find('.rece_body').addClass('hide');
			$(this).removeClass('glyphicon-minus').addClass('glyphicon-plus');
		}
	});
	// 搜索
	$('#main_search').on('click', function(){
		var keyword = $('#keyword').val();
		var new_url = url_object.delUrlParam('p');
		if(keyword == ''){
			new_url = url_object.delUrlParam('keyword', new_url);
		}else{
			keyword = encodeURI(keyword);
			keyword = encodeURI(keyword);
			new_url = url_object.setUrlParam('keyword', keyword, new_url);
		}
		location.replace(new_url);
	});
	$('#keyword').on('keydown', function(e){
		if(e.keyCode == 13){
			var keyword = $('#keyword').val();
			var new_url = url_object.delUrlParam('p');
			if(keyword == ''){
				new_url = url_object.delUrlParam('keyword', new_url);
			}else{
				keyword = encodeURI(keyword);
				keyword = encodeURI(keyword);
				new_url = url_object.setUrlParam('keyword', keyword, new_url);
			}
			location.replace(new_url);
		}
	}); //空格搜索
	// 搜索配置
	$('#search_config_modal .btn-item .btn').on('click', function(){
		if($(this).parent().hasClass('active')){
			$(this).parent().removeClass('active');
			$(this).removeClass('btn-info').addClass('btn-default');
		}else{
			$(this).parent().addClass('active');
			$(this).addClass('btn-info').removeClass('btn-default');
		}
		var str = [];
		$('#search_config_modal .btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$('#search_config_modal input[name=column]').val(str);
	});
	// 全选
	$('.sc_check_all').on('click', function(){
		$('#search_config_modal .btn-item').each(function(){
			if(!$(this).hasClass('active')){
				$(this).addClass('active');
				$(this).find('.btn').addClass('btn-info').removeClass('btn-default');
			}
		});
		var str = [];
		$('#search_config_modal .btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$('#search_config_modal input[name=column]').val(str);
	});
	// 取消
	$('.sc_cancel').on('click', function(){
		$('#search_config_modal .btn-item').each(function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$(this).find('.btn').addClass('btn-default').removeClass('btn-info');
			}
		});
		var str = [];
		$('#search_config_modal .btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$('#search_config_modal input[name=column]').val(str);
	});
	// 搜索配置提交
	$('#search_config_modal .btn-save').on('click', function(){
		var data = $('#search_config_modal form').serialize();
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
	// 已审核客户列表
	$('.check_reviewed').find('.iCheck-helper').on('click', function(){
		var param   = url_object.getUrlParam('reviewed');
		var new_url = url_object.delUrlParam('p');
		if(param == 1){
			new_url = url_object.delUrlParam('reviewed', new_url);
			location.replace(new_url);
		}else{
			new_url = url_object.setUrlParam('reviewed', 1, new_url);
			location.replace(new_url);
		}
	});
	// 未审核客户列表
	$('.check_not_reviewed').find('.iCheck-helper').on('click', function(){
		var param   = url_object.getUrlParam('reviewed');
		var new_url = url_object.delUrlParam('p');
		if(param == 0){
			new_url = url_object.delUrlParam('reviewed', new_url);
			location.replace(new_url);
		}else{
			new_url = url_object.setUrlParam('reviewed', 0, new_url);
			location.replace(new_url);
		}
	});
	// 已收款客户列表
	$('.check_receivables').find('.iCheck-helper').on('click', function(){
		var param   = url_object.getUrlParam('receivables');
		if(param == 1){
			var new_url = url_object.delUrlParam('receivables');
			location.replace(new_url);
		}else{
			var reviewed_url = url_object.setUrlParam('receivables', 1);
			location.replace(reviewed_url);
		}
	});
	// 未收款客户列表
	$('.check_not_receivables').find('.iCheck-helper').on('click', function(){
		var param   = url_object.getUrlParam('receivables');
		if(param == 0){
			var new_url = url_object.delUrlParam('receivables');
			location.replace(new_url);
		}else{
			var reviewed_url = url_object.setUrlParam('receivables', 0);
			location.replace(reviewed_url);
		}
	});
	(function(){
		// 人员状态列表（签到\审核\收款）
		var reviewed    = url_object.getUrlParam('reviewed');
		var receivables = url_object.getUrlParam('receivables');
		if(reviewed == 1) $('.check_reviewed').find('.iradio_square-blue').addClass('checked');
		if(reviewed == 0) $('.check_not_reviewed').find('.iradio_square-blue').addClass('checked');
		if(receivables == 1) $('.check_receivables').find('.iradio_square-green').addClass('checked');
		if(receivables == 0) $('.check_not_receivables').find('.iradio_square-green').addClass('checked');
	})();
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
	$('.cancel_review_btn').on('click', function(){
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
	// 编辑表格内容
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
	// 删除单据
	$('#delete_receivables .btn-save').on('click', function(){
		var data = $('#delete_receivables form').serialize();
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
	// 复制收款记录
	$('.btn_copy').on('click', function(){
		var id = $(this).parent().attr('data-id');
		$('#copy_receivables').find('input[name=id]').val(id);
		$('#copy_receivables .btn-save').on('click', function(){
			var data = $('#copy_receivables form').serialize();
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
	// 修改单据确认
	$('#alter_modal .btn-save').on('click', function(){
		var data = $('#delete_receivables form').serialize();
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
	$('.alter_details_btn').on('click', function(){
		var $modal_obj = $('#alter_receivable_details');
		var id         = $(this).parent().attr('data-id');
		$modal_obj.modal('show');
		$modal_obj.find('input[name=id]').val(id);
		Common.ajax({
			data    :{requestType:'get_detail', id:id},
			callback:function(r){
				ManageObject.object.otherName.setHtml('['+r.project_type+'] '+r.project); //客户
				ManageObject.object.otherName.setValue(r.project_code);
				ManageObject.object.otherName.setExtend(r.project_type_code);
				$modal_obj.find('#pay_method').val(r.pay_method_code);
				$modal_obj.find('#pos_machine').val(r.pos_machine_code);
				$modal_obj.find('#source_type').val(r.source);
				$modal_obj.find('#price_details').val(r.price);
				$modal_obj.find('#comment').val(r.comment);
			}
		});
	});
	$('#alter_receivable_details .btn-save').on('click', function(){
		var data = $('#alter_receivable_details form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					});
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		});
	});
	// 打印
	$('.print').on('click', function(){
		var id = $(this).parent().attr('data-id');
		Common.ajax({
			data    :{requestType:'get_print_data', id:id},
			callback:function(r){
				$('#print').find('.time').text(r.time);
				$('#print').find('.unit').text(r.unit);
				$('#print').find('.client_name').text(r.client);
				var pro = '', i = 1;
				$.each(r.project, function(index, value){
					var n = Number(index);
					//var comment = value.comment.substring(1, value.comment.length);
					if(value.comment){
						pro += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+i+'、'+'['+value.type+']'+value.name+'&nbsp;&nbsp;&nbsp;(金额：'+value.price+')'+'&nbsp;&nbsp;&nbsp;('+'备注：'+value.comment+')'+'<br>';
					}else{
						pro += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+i+'、'+'['+value.type+']'+value.name+'&nbsp;&nbsp;&nbsp;(金额：'+value.price+')'+'<br>';
					}
					i++;
				});
				$('#print').find('.project_type').html(pro); //项目
				$('#print').find('.price_capital').text(digitUppercase(r.price)); //大写
				$('#print').find('.identifier').text('No.'+r.orderNumber); //收据号
				$('#print').find('.price').text(r.price); //金额
				$('#print').find('.payee').text(r.payee); //开收据的人
				var str = '', ii = 0, nn = 0, commentStr = '';
				$.each(r.payMethod, function(index, value){
					commentStr += value.comment+'';
					if(nn == 0){
						$('.type1').text(value.name);
						$('.price1').text(value.price);
					}else{
						str += ThisObject.trTemp.replace('$type', value.name).replace('$price', value.price);
					}
					nn++;
					ii++;
				});
				$('.sign_tr').find('.rmb').attr('rowspan', ii);
				$('.sign_tr').after(str);
				preview(1);
				location.reload();
			}
		})
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
					$('.list_form').removeClass('hide');
					$('.list_form .ticket_h').removeClass('hide');
					$('.list_form .coupon_h').addClass('hide');
					$('#add_receivables .modal-footer').removeClass('hide');
					$.each(data, function(index, value){
						str += ThisObject.option.replace('$id', value.id).replace('$name', value.name)
										 .replace('$price', value.price);
					});
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
					$('.list_form').removeClass('hide');
					$('.list_form .ticket_h').addClass('hide');
					$('.list_form .coupon_h').removeClass('hide');
					$('#add_receivables .modal-footer').removeClass('hide');
					$.each(data, function(index, value){
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
	$.ajax({
		url        :'',
		type       :'POST',
		data       :data,
		dataType   :'JSON',
		cache      :false,
		processData:false,
		contentType:false
	}).done(function(data){
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
function upLoadLogo(){
	var data = new FormData($('#configure_form')[0]);
	$.ajax({
		url        :'',
		type       :'POST',
		data       :data,
		dataType   :'JSON',
		cache      :false,
		processData:false,
		contentType:false
	}).done(function(data){
		if(data.status){
			ManageObject.object.toast.toast(data.message, '1');
			$('input[name=logo]').val(data.data.filePath);
			$('.logo_wp img').attr('src', data.data.filePath);
		}else{
			ManageObject.object.toast.toast(data.message, '2');
		}
	});
	$('input[name=requestType]').val('set_config');
	return false;
}