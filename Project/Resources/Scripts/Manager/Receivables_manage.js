/**
 * Created by 1195 on 2016-10-19.
 */

var ThisObject = {
	aTemp               :'<a class="btn btn-default btn-sm " href="javascript:void(0)" role="button" data-id="$id">$name</a>',
	aActiveTemp         :'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$name</a>',
	word                :0,
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
		var id          = $(this).parents('.btn-group').attr('data-id');
		Common.ajax({
			data:{requestType:'get_receivables_detail', id:id},
			callback:function(r){
				var $alter_receivables_object = $('#alter_receivables');
				var time = new Date(r.time*1000).format('yyyy-MM-dd HH:mm:ss');
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
});
function checkIsEmpty(){
};
