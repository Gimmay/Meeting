/**
 * Created by 轻言轻语 on 2016-11-22.
 */

var ScriptObject = {
	couponLiTemp       :'<div class="rece_li clearfix rece_new">\n\t<div class="rece_item">\n\t\t&nbsp;\n\t</div>\n\t<div class="rece_item">\n\t\t&nbsp;\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="payMethod[]" class="form-control payMethod">{::PAY_METHOD::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<input type="text" class="form-control price" name="price[]" placeholder="金额">\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="posMachine[]" class="form-control pos">{::POS_MACHINE::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="source[]" class="form-control source_type" id="source_type">\n\t\t\t<option value="1">会前收款</option>\n\t\t\t<option value="2">会中收款</option>\n\t\t\t<option value="3">会后收款</option>\n\t\t</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<input type="text" class="form-control comment" name="comment[]" placeholder="备注">\n\t</div>\n\t<div class="rece_item">\n\t\t<span class="delete glyphicon glyphicon-minus"></span>\n\t</div>\n</div>',
	otherInnerTemp     :'<div class="rece_li clearfix rece_new">\n\t<div class="rece_item project_style">\n\t\t&nbsp;\n\t</div>\n\t<div class="rece_item price_style">\n\t\t&nbsp;\n\t</div>\n\t<div class="rece_item pay_style">\n\t\t<select name="payMethod$i[]" class="form-control payMethod">{::PAY_METHOD::}</select>\n\t</div>\n\t<div class="rece_item price_style">\n\t\t<input type="text" class="form-control price" name="price$i[]" value="0">\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="posMachine$i[]" class="form-control pos">{::POS_MACHINE::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="source$i[]" class="form-control source_type" id="source_type">\n\t\t\t<option value="1">会前收款</option>\n\t\t\t<option value="2">会中收款</option>\n\t\t\t<option value="3">会后收款</option>\n\t\t</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<input type="text" class="form-control comment" name="comment$i[]" placeholder="备注">\n\t</div>\n\t<div class="rece_item other_style">\n\t\t<span class="delete glyphicon glyphicon-minus"></span>\n\t</div>\n</div>',
	otherLiTemp        :'<div class="rece_wrap">\n\t<span class="delete_item glyphicon glyphicon-minus"></span>\n\t<div class="rece_li clearfix">\n\t\t<div class="rece_item project_style">\n\t\t\t<div id="{::name_id::}"></div>\n\t\t</div>\n\t\t<div class="rece_item price_style">\n\t\t\t<input type="text" class="form-control fixed_price" name="fixed_price$i[]" placeholder="方案款" title="方案款" value="0">\n\t\t</div>\n\t\t<div class="rece_item pay_style">\n\t\t\t<select name="payMethod$i[]" class="form-control payMethod">{::PAY_METHOD::}</select>\n\t\t</div>\n\t\t<div class="rece_item price_style">\n\t\t\t<input type="text" class="form-control price" name="price$i[]" value="0">\n\t\t</div>\n\t\t<div class="rece_item">\n\t\t\t<select name="posMachine$i[]" class="form-control pos">{::POS_MACHINE::}</select>\n\t\t</div>\n\t\t<div class="rece_item">\n\t\t\t<select name="source$i[]" class="form-control source_type" id="source_type">\n\t\t\t\t<option value="1">会前收款</option>\n\t\t\t\t<option value="2">会中收款</option>\n\t\t\t\t<option value="3">会后收款</option>\n\t\t\t</select>\n\t\t</div>\n\t\t<div class="rece_item">\n\t\t\t<input type="text" class="form-control comment" name="comment$i[]" placeholder="备注">\n\t\t</div>\n\t\t<div class="rece_item text-center other_style">\n\t\t\t<span class="add glyphicon glyphicon-plus"></span>\n\t\t</div>\n\t</div>\n\t<input type="hidden" class="num" value="$i">\n</div>\n',
	secCouponTemp      :'<div class="sec_coupon">\n\t<div class="title">$couponName</div>\n\t<div class="coupon_list">\n\t\t$htm\n\t</div>\n</div>',
	optionTemp         :'<option value="$val" data-price="$price">$name</option>',
	couponATemp        :'<a href="javascript:void(0)" data-id="$id">$code</a>',
	selectedCouponId   :[],
	num                :2,
	current            :0,
	ver                :0,
	/**
	 * 两个数组去重复
	 */
	arrayDeleteRepeat  :function(arr1, arr2){
		var arr1      = arr1; //数组A
		var arr2      = arr2;//数组B
		arr2          = arr2.split(",");
		var temp      = []; //临时数组1
		var temparray = [];//临时数组2
		for(var i = 0; i<arr2.length; i++){
			temp[arr2[i]] = true;//巧妙地方：把数组B的值当成临时数组1的键并赋值为真
		}
		for(var i = 0; i<arr1.length; i++){
			if(!temp[arr1[i]]){
				temparray.push(arr1[i]);//巧妙地方：同时把数组A的值当成临时数组1的键并判断是否为真，如果不为真说明没重复，就合并到一个新数组里，这样就可以得到一个全新并无重复的数组
			}
		}
		return temparray.join(",")+"";
	},
	/**
	 *  ------------------------------代金券注释---------------------------------
	 */
	/**
	 *  通过ajax提交获取代金券详情列表,
	 *  requestType:coupon
	 *  写入数据后select触发change事件调用setCouponCode函数
	 */
	setCouponName      :function(){
		var self = this;
		Common.ajax({
			data    :{requestType:'coupon'},
			callback:function(r){
				var str = '';
				$.each(r, function(index, value){
					str += self.optionTemp.replace('$val', value.id).replace('$name', value.name)
							   .replace('$price', value.price);
				});
				var opts = '<option value="0">-----代金券类型-----</option>';
				$('.coupon .rece_new').find('.name').html(opts+str);
				$('.coupon .rece_new .name').on('change', function(){
					var id      = $(this).find('option:selected').val();
					var price   = $(this).find('option:selected').attr('data-price');
					var element = $(this);
					self.setCouponCode(id, element, price);
				});
			}
		});
	},
	/**
	 *  通过ajax提交获取代金券中含有的代金券码详情 -- coupon_code
	 *  requestType:get_coupon_code
	 *  id:代金券类型的ID
	 *  element：选择代金券类型的DOM标签
	 *  price:选择代金券类型的金额
	 */
	setCouponCode      :function(id, element, price){
		element.parents('.rece_li').removeClass('rece_new');
		var self = this;
		Common.ajax({
			data    :{requestType:'get_coupon_code', id:id},
			callback:function(r){
				/**
				 * str : js编写的代金券码的HMTL内容。
				 * arrId : 该代金券类型所有的代金券码的ID的数组。
				 */
				var str = '';
				$.each(r, function(index, value){
					str += self.couponATemp.replace('$id', value.id).replace('$code', value.name)
				});
				$('#coupon_modal .coupon_list').html(str);
				$('.coupon_list a').on('click', function(){
					if($(this).hasClass('.active')){
						$(this).removeClass('active');
					}else{
						$(this).addClass('active');
					}
				});
				$('#coupon_modal .coupon_save').on('click', function(){
					var arr = [], arrName = [];
					$('.coupon_list a.active').each(function(){
						var id   = $(this).attr('data-id');
						var name = $(this).text();
						arr.push(id);
						arrName.push(name);
					});
					$('.select_code').val(arr);
					$('#select_coupon_name').val(arrName);
					$('#coupon_modal').modal('hide');
				});
			}
		})
	},
	/**
	 * refreshSelectVal ：当点击代金券CODE下拉select时，触发该事件。
	 */
	refreshSelectVal   :function(element){
		var allCouponId = $('.select_all_coupon').val();
		var arr2        = allCouponId.split(","); // 将 select_all_coupon 标签中所有的ID（string）转化为数组格式。
		var ss          = [];
		element.find('option').not(':selected').each(function(){
			var id = $(this).val();
			ss.push(id);
			if($.inArray(id, arr2) == -1){
				/*//alert('1');*/
			}else{
				/*	alert('2');*/
				$(this).remove();
			}
		});
	},
	/**
	 *
	 * @param _self  触发change事件的dom元素
	 * @param list  ajax传过来的第二列name的消息
	 */
	setSecondSelectList:function(_self, list){
		var self = this;
		var html = '';
		ManageObject.object.name.update(list);
		$(_self).parent().parent().find('select.name').html(html).off('change').on('change', function(){
			var selected_item_price = $(this).find('option:selected').attr('data-price');
			// $(_self).parent().parent().find('input.price').val(selected_item_price);
			self.totalAmount();
		});
	},
	bindEvent          :function(){
		var self = this;
		// 金额改变后，触发总金额方法
		$('.price').on('change', function(){
				self.totalAmount();
			}
		);
		// 其他收款选择项目类型
		$('.rece_li').find('.project_type').on('change', function(){
			var self = this;
			var val  = $(this).find('option:selected').val();
			if(val != '2'){
				Common.ajax({
					data    :{requestType:'get_project', projectType:val},
					callback:function(r){
						ScriptObject.setSecondSelectList(self, r);
					}
				});
			}
		});
		$('.price').on('blur', function(){
			if(isNaN($(this).val())){
				ManageObject.object.toast.toast('请输入数字', '1');
				$(this).val('0');
				ScriptObject.totalAmount();
			}
		});
	},
	unbindEvent        :function(){
		$('.price').off('change');
		$('.coupon_code').off('click');
		$('.c_list a').off('click');
		$('.conplete_btn').off('click');
		$('.rece_li').find('.project_type').off('change');
		$('.name').off('click');
		$('.price').off('blur');
	},
	totalAmount        :function(){
		var amount = 0;
		$('.price').each(function(){
			var price = Number($(this).val());
			amount += price;
		});
		$('.foot').find('.color-red').text(amount+'元');
		$('#total_amount').val(amount);
	}
};
$(function(){
	$('#form_rece .submit').on('click', function(){
		if(check()){
			var data = $('#form_rece').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.href = r.nextPage
						});
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			})
		}
	});
	/**
	 * 新增客户
	 */
	(function(){
		$('.form-group').each(function(){
			var must_id = $(this).find('input[type=hidden]').attr('data-must');
			if(must_id == 1){
				$(this).find('label').addClass('color-red').prepend("<b style=\'vertical-align: middle;\'>*</b>");
			}
		});
	})();
	$('#create_client .btn-save').on('click', function(){
		if(isEmpty()){
			var data = $('#create_form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.href = res.nextPage;
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		}
	});
	(function(){
		// 会前 会中 会后状态
		var status = ManageObject.data.meetingStatus;
		$('.source_type').each(function(){
			if(status == 0){
				$(this).find('option').eq(0).prop('selected', true);
			}else if(status == 1){
				$(this).find('option').eq(1).prop('selected', true);
			}else if(status == 2){
				$(this).find('option').eq(2).prop('selected', true);
			}
		});
	})();
	$('.other .add').on('click', function(){
		var e = $(this);
		set_other_list(e);
	});
	$('#redirectUrl').val(document.referrer);
	ScriptObject.bindEvent();
	ScriptObject.setCouponName();
	$('.other .add_item').on('click', function(){
		ScriptObject.ver++;
		var setSelectList = function(temp){
			var pay_method_source_data  = JSON.parse($('#pay_method_source_data').text());
			var pos_machine_source_data = JSON.parse($('#pos_machine_source_data').text());
			var html_pos_machine        = '<option value="0">-----POS机-----</option>';
			var html_pay_method         = '<option value="0">-----支付方式-----</option>';
			var i;
			for(i = 0; i<pos_machine_source_data.length; i++){
				html_pos_machine += '<option value="'+pos_machine_source_data[i]['value']+'">'+pos_machine_source_data[i]['html']+'</option>';
			}
			for(i = 0; i<pay_method_source_data.length; i++){
				html_pay_method += '<option value="'+pay_method_source_data[i]['value']+'">'+pay_method_source_data[i]['html']+'</option>';
			}
			temp = temp.replace('{::POS_MACHINE::}', html_pos_machine);
			temp = temp.replace('{::name_id::}', 'name_rece'+ScriptObject.ver);
			temp = temp.replace('{::PAY_METHOD::}', html_pay_method);
			temp = temp.replace(/\$i/g, ScriptObject.num);
			return temp;
		};
		var str           = setSelectList(ScriptObject.otherLiTemp);
		$('.mo_rece.mo_content .other').append(str);
		var projectData = $('#project_source_data').text();
		$('#name_rece'+ScriptObject.ver).QuasarSelect({
			name        :'name[]',
			classStyle  :'form-control',
			idInput     :'selected_name'+ScriptObject.ver,
			idHidden    :'selected_name_form'+ScriptObject.ver,
			data        :projectData,
			placeholder :'',
			defaultValue:'',
			defaultHtml :'',
			hasEmptyItem:false,
			onSelect    :function(){
				var data = $(this).find('textarea').attr('data-ext');
				data     = data.split(',');
				$(this).parents('.rece_li').find('.price').val(data[1]);
				$(this).parents('.rece_li').find('.fixed_price').val(data[1]);
				ScriptObject.totalAmount()
			}
		});
		(function(){
			// 会前 会中 会后状态
			var status = ManageObject.data.meetingStatus;
			$('.source_type').each(function(){
				if(status == 0){
					$(this).find('option').eq(0).prop('selected', true);
				}else if(status == 1){
					$(this).find('option').eq(1).prop('selected', true);
				}else if(status == 2){
					$(this).find('option').eq(2).prop('selected', true);
				}
			});
		})();
		ScriptObject.num++;
		$('.delete_item').off('click');
		$('.delete_item').on('click', function(){
			var index = $(this).index('.delete_item');
			$(this).parents('.rece_wrap').remove();
			ScriptObject.totalAmount();
			ScriptObject.ver--;
		});
		$('.other .add').off('click');
		$('.other .add').on('click', function(){
			var e = $(this);
			set_other_list(e);
		});
		ScriptObject.unbindEvent();
		ScriptObject.bindEvent();
	});
	$('.coupon .add').on('click', function(){
		var setSelectList = function(temp){
			var pay_method_source_data  = JSON.parse($('#pay_method_source_data').text());
			var pos_machine_source_data = JSON.parse($('#pos_machine_source_data').text());
			var html_pos_machine        = '<option value="0">-----POS机-----</option>';
			var html_pay_method         = '<option value="0">-----支付方式-----</option>';
			var i;
			for(i = 0; i<pos_machine_source_data.length; i++) html_pos_machine += '<option value="'+pos_machine_source_data[i]['id']+'">'+pos_machine_source_data[i]['name']+'</option>';
			for(i = 0; i<pay_method_source_data.length; i++) html_pay_method += '<option value="'+pay_method_source_data[i]['id']+'">'+pay_method_source_data[i]['name']+'</option>';
			temp = temp.replace('{::POS_MACHINE::}', html_pos_machine);
			temp = temp.replace('{::PAY_METHOD::}', html_pay_method);
			return temp;
		};
		var str           = setSelectList(ScriptObject.couponLiTemp);
		$('.mo_rece.mo_content .coupon').append(str);
		(function(){
			// 会前 会中 会后状态
			var status = ManageObject.data.meetingStatus;
			$('.source_type').each(function(){
				if(status == 0){
					$(this).find('option').eq(0).prop('selected', true);
				}else if(status == 1){
					$(this).find('option').eq(1).prop('selected', true);
				}else if(status == 2){
					$(this).find('option').eq(2).prop('selected', true);
				}
			});
		})();
		ScriptObject.setCouponName();
		$('.coupon .delete').on('click', function(){
			$(this).parents('.rece_li').remove();
			var selectArr = []; // 创建空数组 -- 选择代金码ID
			$('.select_code').each(function(){
				var val = $(this).val();
				selectArr.push(val);
			});
			$('.select_all_coupon').val(selectArr);
			ScriptObject.totalAmount();
		});
		$('.other .delete').on('click', function(){
			$(this).parents('.rece_li').remove();
			ScriptObject.totalAmount();
		});
		ScriptObject.unbindEvent();
		ScriptObject.bindEvent();
	});
	// 选择代金券
	$('.select_code_wrap').on('click', function(){
		Common.ajax({
			data    :{requestType:'coupon'},
			callback:function(r){
				var str = '';
				/*	$.each(r, function(index, value){
				 str += self.optionTemp.replace('$val', value.id).replace('$name', value.name)
				 .replace('$price', value.price);
				 });
				 var opts = '<option value="0">代金券类型</option>';
				 $('.coupon .rece_new').find('.name').html(opts+str);
				 $('.coupon .rece_new .name').on('change', function(){
				 var id      = $(this).find('option:selected').val();
				 var price   = $(this).find('option:selected').attr('data-price');
				 var element = $(this);
				 self.setCouponCode(id, element, price);
				 });*/
			}
		});
	});
});
/*function set_other_list(e){
 var setSelectList = function(temp){
 var pay_method_source_data  = JSON.parse($('#pay_method_source_data').text());
 var pos_machine_source_data = JSON.parse($('#pos_machine_source_data').text());
 var html_pos_machine        = '<option value="0">POS机</option>';
 var html_pay_method         = '<option value="0">支付方式</option>';
 var i;
 for(i = 0; i<pos_machine_source_data.length; i++) html_pos_machine += '<option value="'+pos_machine_source_data[i]['id']+'">'+pos_machine_source_data[i]['name']+'</option>';
 for(i = 0; i<pay_method_source_data.length; i++) html_pay_method += '<option value="'+pay_method_source_data[i]['id']+'">'+pay_method_source_data[i]['name']+'</option>';
 temp = temp.replace('{::POS_MACHINE::}', html_pos_machine);
 temp = temp.replace('{::PAY_METHOD::}', html_pay_method);
 return temp;
 };
 var str           = setSelectList(ScriptObject.otherInnerTemp);
 e.parents('.rece_wrap ').append(str);
 (function(){
 // 会前 会中 会后状态
 var status = ManageObject.data.meetingStatus;
 $('.source_type').each(function(){
 if(status == 0){
 $(this).find('option').eq(0).prop('selected', true);
 }else if(status == 1){
 $(this).find('option').eq(1).prop('selected', true);
 }else if(status == 2){
 $(this).find('option').eq(2).prop('selected', true);
 }
 });
 })();
 }*/
function set_other_list(e){
	var num           = e.parents('.rece_wrap').find('.num').val();
	var setSelectList = function(temp){
		var pay_method_source_data  = JSON.parse($('#pay_method_source_data').text());
		var pos_machine_source_data = JSON.parse($('#pos_machine_source_data').text());
		var html_pos_machine        = '<option value="0">-----POS机-----</option>';
		var html_pay_method         = '<option value="0">-----支付方式-----</option>';
		var i;
		for(i = 0; i<pos_machine_source_data.length; i++) html_pos_machine += '<option value="'+pos_machine_source_data[i]['value']+'">'+pos_machine_source_data[i]['html']+'</option>';
		for(i = 0; i<pay_method_source_data.length; i++) html_pay_method += '<option value="'+pay_method_source_data[i]['value']+'">'+pay_method_source_data[i]['html']+'</option>';
		temp = temp.replace('{::POS_MACHINE::}', html_pos_machine);
		temp = temp.replace('{::PAY_METHOD::}', html_pay_method);
		temp = temp.replace(/\$i/g, num);
		return temp;
	};
	var str           = setSelectList(ScriptObject.otherInnerTemp);
	e.parents('.rece_wrap').append(str);
	(function(){
		// 会前 会中 会后状态
		var status = ManageObject.data.meetingStatus;
		$('.source_type').each(function(){
			if(status == 0){
				$(this).find('option').eq(0).prop('selected', true);
			}else if(status == 1){
				$(this).find('option').eq(1).prop('selected', true);
			}else if(status == 2){
				$(this).find('option').eq(2).prop('selected', true);
			}
		});
	})();
	$('.other .delete').off('click');
	$('.other .delete').on('click', function(){
		var index = $(this).index('.delete');
		$(this).parents('.rece_li').remove();
		ScriptObject.totalAmount();
	});
	$('.price').off('change').on('change', function(){
		ScriptObject.totalAmount();
	});
	ScriptObject.unbindEvent();
	ScriptObject.bindEvent();
}
function check(){
	if(ManageObject.object.clientName.getValue() == ''){
		ManageObject.object.toast.toast('客户姓名不能为空！');
		$('#selected_client_name').focus();
		return false;
	}
	var i = 0;
	$('.other .name').each(function(){
		if($(this).find('option:selected').val() != 0){
			i++;
		}
	});
	if($('.select_code').val() == 0 && i == 0){
		ManageObject.object.toast.toast('项目不能为空！');
		return false;
	}else{
		return true;
	}
	return true;
}
function isEmpty(){
	var status = true;
	$('.form-group').each(function(){
		if($(this).find('input').hasClass('necessary')){
			var text = $(this).find('label ').text();
			text     = text.substring(0, text.length-1); //去掉最后一个字符
			text     = text.substring(1, text.length); //去掉第一个一个字符
			if($(this).find('.form-control').val() == ''){
				ManageObject.object.toast.toast(text+"不能为空");
				$(this).find('.form-control').focus();
				status = false;
				return false;
			}
		}
	});
	return !!status;
}

