/**
 * Created by 1195 on 2016-10-19.
 */

var ThisObject = {
	aTemp               :'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$name</a>',
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
		// 打开计算器
		$('#price').on('focus', function(){
			$('#idCalculadora').removeClass('hide');
		});
		// 计算机关闭
		$('.close_calcuator button').on('click', function(){
			$('#idCalculadora').addClass('hide');
		});
		// 计算器等于号
		$('.equal').on('click', function(){
			var sum = $('#input-box').val();
			$('#price').val(sum);
		});
		$('.clear-marginleft').on('click', function(){
			$('#price').val(0);
		});
		// 删除收款记录
		$('.delete_btn').on('click', function(){
			var id = $(this).parents('.btn-group').attr('data-id');
			$('#delete_receivables').find('input[name=id]').val(id);
		});
		// 修改收款
		$('.modify_btn').on('click', function(){
			var id          = $(this).parents('.btn-group').attr('data-id');
			var client_name = $(this).parents('tr').find('.client_name').text();
			var price       = $(this).parents('tr').find('.price').text();
			var type        = $(this).parents('tr').find('.type').text();
			var method      = $(this).parents('tr').find('.method').text();
			var time        = $(this).parents('tr').find('.time').text();
			var place       = $(this).parents('tr').find('.place').text();
			var source_type = $(this).parents('tr').find('.source_type').text();
			var comment     = $(this).parents('tr').find('.comment').text();
			$('#alter_receivables').find('input[name=id]').val(id);
			$('#alter_receivables').find('#client_name_a').val(client_name);
			$('#alter_receivables').find('#price_a').val(price);
			$('#alter_receivables').find('#selected_method_a').val(method);
			$('#alter_receivables').find('#selected_type_a').val(type);
			$('#alter_receivables').find('#place_a').val(place);
			$('#alter_receivables').find('#source_type_a').children('option').each(function(){
				var opts          = $(this).text();
				var option        = opts.trim();
				var source_type_1 = source_type.trim();
				var index         = $(this).index();
				if(option == source_type_1){
					$('#alter_receivables').find('#source_type_a').children('option').eq(index)
										   .prop('selected', 'selected');
				}
			});
			$('#alter_receivables').find('#receivables_time_a').val(time);
			$('#alter_receivables').find('#comment_a').val(comment);
			Common.ajax({
				data    :{requestType:'alter_coupon', id:id},
				callback:function(r){
					console.log(r);
					var arr = [];
					if(ThisObject.word == 0){
						$.each(r, function(index, value){
							$('.coupon_list')
							.append('<a class=\"btn btn-default btn-sm active\" href="javascript:void(0)" role="button" data-id='+value.id+'>'+value.code+'</a>');
							arr.push(value.id);
							ThisObject.word = 1;
						});
						$('#alter_receivables').find('input[name=old_coupon_code]').val(arr);
					}
					ThisObject.unbindEvent();
					ThisObject.bindEvent();
				}
			});
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
	ThisObject.bindEvent();
});
