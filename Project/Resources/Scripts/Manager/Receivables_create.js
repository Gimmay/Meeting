/**
 * Created by 轻言轻语 on 2016-11-22.
 */

var ScriptObject = {
	couponLiTemp       :'<div class="rece_li clearfix rece_new">\n\t<input type="hidden" name="type[]" value="2">\n\t<div class="rece_item">\n\t\t<select name="name[]" class="form-control name">{::COUPON_NAME::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="coupon_code[]" class="form-control coupon_code"></select>\n\t\t<input type="hidden" class="select_code" name="select_code[]">\n\t</div>\n\t<div class="rece_item">\n\t\t<input type="text" class="form-control price" name="price[]" placeholder="金额">\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="payMethod[]" class="form-control payMethod">{::PAY_METHOD::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="pos[]" class="form-control pos">{::POS_MACHINE::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="source_type[]" class="form-control source_type" id="source_type">\n\t\t\t<option value="1">会前收款</option>\n\t\t\t<option value="2">会中收款</option>\n\t\t\t<option value="3">会后收款</option>\n\t\t</select>\n\t</div>\n\t<span class="delete glyphicon glyphicon-minus"></span>\n</div>',
	otherLiTemp        :'<div class="rece_li clearfix">\n\t<div class="rece_item">\n\t\t<select name="type[]" class="form-control project_type">\n\t\t\t<option value="0">请选择项目类型</option>\n\t\t\t<option value="1">门票</option>\n\t\t\t<option value="3">产品</option>\n\t\t\t<option value="4">其他</option>\n\t\t</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="name[]" class="form-control name"></select>\n\t</div>\n\t<div class="rece_item">\n\t\t<input type="text" class="form-control price" name="price[]" placeholder="金额">\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="payMethod[]" class="form-control payMethod">{::PAY_METHOD::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="pos[]" class="form-control pos">{::POS_MACHINE::}</select>\n\t</div>\n\t<div class="rece_item">\n\t\t<select name="source_type[]" class="form-control source_type" id="source_type">\n\t\t\t<option value="1">会前收款</option>\n\t\t\t<option value="2">会中收款</option>\n\t\t\t<option value="3">会后收款</option>\n\t\t</select>\n\t</div>\n\t<span class="delete glyphicon glyphicon-minus"></span>\n</div>',
	secCouponTemp      :'<div class="sec_coupon">\n\t<div class="title">$couponName</div>\n\t<div class="coupon_list">\n\t\t$htm\n\t</div>\n</div>',
	optionTemp         :'<option value="$val" data-price="$price">$name</option>',
	selectedCouponId   :[],
	/**
	 * 两个数组去重复
	 */
	arrayDeleteRepeat  :function(arr1, arr2){
		var arr1 = arr1; //数组A
		console.log(arr1);
		var arr2 = arr2;//数组B
		arr2     = arr2.split(",")
		console.log(arr2);
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
				console.log(r);
				var str = '';
				$.each(r, function(index, value){
					str += self.optionTemp.replace('$val', value.id).replace('$name', value.name)
							   .replace('$price', value.price);
				});
				var opts = '<option value="0">请选择代金券类型</option>';
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
				var str = "<option value='0' data-price=''>请选择代金券码</option>", arrId = [];
				var allCouponId                                                     = $('.select_all_coupon').val();
				var arr2                                                            = allCouponId.split(","); // 将 select_all_coupon 标签中所有的ID（string）转化为数组格式。
				if(arr2 != ''){
					$.each(r, function(index, value){
						if($.inArray(value.id, arr2) == -1){
							str += self.optionTemp.replace('$val', value.id).replace('$name', value.name)
									   .replace('$price', price);
							arrId.push(value.id);
						}
					});
				}else{
					$.each(r, function(index, value){
						str += self.optionTemp.replace('$val', value.id).replace('$name', value.name)
								   .replace('$price', price);
						arrId.push(value.id);
					});
				}
				element.parents('.rece_li').find('.coupon_code').html(str);   // select 代金券码写入
				// 代金券码选择，将代金券金额写入金额栏里面
				$('.coupon_code').on('change', function(){
					var price = $(this).find('option:selected').attr('data-price');
					var val   = $(this).val();
					$(this).parents('.rece_li').find('.price').val(price);
					$(this).parent('.rece_item').find('.select_code').val(val);
					self.totalAmount();
					var selectArr = []; // 创建空数组 -- 选择代金码ID
					$('.select_code').each(function(){
						var val = $(this).val();
						selectArr.push(val);
					});
					$('.select_all_coupon').val(selectArr);
				});
				var allCouponCode = $('.select_all_coupon').val(); // 所有已选择的代金券码ID
				/*	var a   = self.arrayDeleteRepeat(arrId, allCouponCode);*/
				ScriptObject.unbindEvent();
				ScriptObject.bindEvent();
			}
		})
	},
	/**
	 * refreshSelectVal ：当点击代金券CODE下拉select时，触发改事件。
	 */
	refreshSelectVal   :function(element){
		var allCouponId = $('.select_all_coupon').val();
		var arr2        = allCouponId.split(","); // 将 select_all_coupon 标签中所有的ID（string）转化为数组格式。
		console.log(arr2);
		var ss = [];
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
		console.log(ss);
	},
	/**
	 *
	 * @param _self  触发change事件的dom元素
	 * @param list  ajax传过来的第二列name的消息
	 */
	setSecondSelectList:function(_self, list){
		var self = this;
		var html = '<option value="0">请选择名称</option>';
		for(var i = 0; i<list.length; i++){
			html += "<option data-price='"+list[i]['price']+"' value='"+list[i]['id']+"'>"+list[i]['name']+"</option>";
		}
		$(_self).parent().parent().find('select.name').html(html).off('change').on('change', function(){
			var selected_item_price = $(this).find('option:selected').attr('data-price');
			$(_self).parent().parent().find('input.price').val(selected_item_price);
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
		/*$('.coupon_code').on('mousedown', function(){
			ScriptObject.refreshSelectVal($(this));
		});*/
		// 其他收款选择项目类型
		$('.rece_li').find('.project_type').on('change', function(){
			var self = this;
			console.log(self);
			var val = $(this).find('option:selected').val();
			switch(parseInt(val)){
				case 0:
				default:
					break;
				case 1:
					Common.ajax({
						data    :{requestType:'ticket', val:val},
						callback:function(r){
							ScriptObject.setSecondSelectList(self, r);
						}
					});
					break;
				case 3:
					Common.ajax({
						data    :{requestType:'product', val:val},
						callback:function(r){
							ScriptObject.setSecondSelectList(self, r);
						}
					});
					break;
				case 4:
					Common.ajax({
						data    :{requestType:'other', val:val},
						callback:function(r){
							ScriptObject.setSecondSelectList(self, r);
						}
					});
					break;
				case 5:
					break;
			}
		});
	},
	unbindEvent        :function(){
		$('.coupon_code').off('click');
		$('.c_list a').off('click');
		$('.conplete_btn').off('click');
		$('.rece_li').find('.project_type').off('change');
		$('.name').off('click');
	}
	,
	totalAmount        :function(){
		var amount = 0;
		$('.price').each(function(){
			var price = Number($(this).val());
			amount += price;
		});
		$('.foot').find('.color-red').text(amount+'元');
		$('#total_amount').val(amount);
	}
}
	;
$(function(){
	ScriptObject.bindEvent();
	ScriptObject.setCouponName();
	$('.other .add').on('click', function(){
		var setSelectList = function(temp){
			var pay_method_source_data  = JSON.parse($('#pay_method_source_data').text());
			var pos_machine_source_data = JSON.parse($('#pos_machine_source_data').text());
			var html_pos_machine        = '<option value="0">请选择POS机</option>';
			var html_pay_method         = '<option value="0">请选择支付方式</option>';
			var i;
			for(i = 0; i<pos_machine_source_data.length; i++) html_pos_machine += '<option value="'+pos_machine_source_data[i]['id']+'">'+pos_machine_source_data[i]['name']+'</option>';
			for(i = 0; i<pay_method_source_data.length; i++) html_pay_method += '<option value="'+pay_method_source_data[i]['id']+'">'+pay_method_source_data[i]['name']+'</option>';
			temp = temp.replace('{::POS_MACHINE::}', html_pos_machine);
			temp = temp.replace('{::PAY_METHOD::}', html_pay_method);
			return temp;
			console.log(temp);
		};
		var str           = setSelectList(ScriptObject.otherLiTemp);
		$('.mo_rece.mo_content .other').append(str);
		$('.delete').on('click', function(){
			$(this).parents('.rece_li').remove();
		});
		ScriptObject.unbindEvent();
		ScriptObject.bindEvent();
	});
	$('.coupon .add').on('click', function(){
		var setSelectList = function(temp){
			var pay_method_source_data  = JSON.parse($('#pay_method_source_data').text());
			var pos_machine_source_data = JSON.parse($('#pos_machine_source_data').text());
			var html_pos_machine        = '<option value="0">请选择POS机</option>';
			var html_pay_method         = '<option value="0">请选择支付方式</option>';
			var i;
			for(i = 0; i<pos_machine_source_data.length; i++) html_pos_machine += '<option value="'+pos_machine_source_data[i]['id']+'">'+pos_machine_source_data[i]['name']+'</option>';
			for(i = 0; i<pay_method_source_data.length; i++) html_pay_method += '<option value="'+pay_method_source_data[i]['id']+'">'+pay_method_source_data[i]['name']+'</option>';
			temp = temp.replace('{::POS_MACHINE::}', html_pos_machine);
			temp = temp.replace('{::PAY_METHOD::}', html_pay_method);
			return temp;
		};
		var str           = setSelectList(ScriptObject.couponLiTemp);
		$('.mo_rece.mo_content .coupon').append(str);
		ScriptObject.setCouponName();
		$('.coupon .delete').on('click', function(){
			$(this).parents('.rece_li').remove();
			var selectArr = []; // 创建空数组 -- 选择代金码ID
			$('.select_code').each(function(){
				var val = $(this).val();
				selectArr.push(val);
			});
			$('.select_all_coupon').val(selectArr);
		});
		$('.other .delete').on('click', function(){
			$(this).parents('.rece_li').remove();
		});
		ScriptObject.unbindEvent();
		ScriptObject.bindEvent();
	});
});

