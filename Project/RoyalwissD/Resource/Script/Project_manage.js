/**
 * Created by qyqy on 2016-10-8.
 */

var template = '<tr>\n\t<td>$type</td>\n\t<td>$number</td>\n\t<td>$createTime</td>\n\t<td>$comment</td>\n</tr>';
$(function(){
	// 新增项目保存
	$('#create_project .btn-save').on('click', function(){
		if(checkIsEmpty()){
			var data = $('#create_project form').serialize();
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
		}
	});
	// 选择项目是否有库存限制
	$('#is_stock_limit').on('change', function(){
		if($(this).prop('checked')){
			$(this).parents('.input-group').find('#stock').prop({'readonly':false, 'placeholder':'有库存限制'});
		}else{
			$(this).parents('.input-group').find('#stock').prop({'readonly':true, 'placeholder':'无库存限制'}).val('');
		}
	});
	// 修改项目
	$('.alter_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.name').text();
		var type    = $(this).parents('tr').find('.type').text();
		var price   = $(this).parents('tr').find('.price').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_project').find('input[name=id]').val(id);
		$('#alter_project').find('input[name=name]').val(name);
		$('#alter_project').find('input[name=price]').val(price);
		$('#alter_project').find('textarea[name=comment]').val(comment);
		// 修改项目保存
		$('#alter_project .btn-save').on('click', function(){
			var data = $('#alter_project form').serialize();
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
	// 库存限制
	$('.alter_stock_btn').on('click', function(){
		var id           = $(this).parent().attr('data-id');
		var stock_limit  = $(this).parent().attr('data-stock-limit');
		var stock_number = $(this).parent().attr('data-stock');
		if(stock_limit == 1){
			$('#alter_stock_limit').find('#stock_alter').prop({'readonly':false, 'placeholder':'有库存限制'});
			$('#alter_stock_limit .active_form').removeClass('hidden');
			$('#alter_stock_limit').find('#is_stock_limit_alter').prop('checked', true);
		}else if(stock_limit == 0){
			$('#alter_stock_limit').find('#stock_alter').prop({'readonly':true, 'placeholder':'无库存限制'}).val('');
			$('#alter_stock_limit .active_form').addClass('hidden');
		}
		$('#alter_stock_limit').find('input[name=id]').val(id);
		$('#alter_stock_limit .btn-save').on('click', function(){
			var data           = $('#alter_stock_limit form').serialize();
			var current_number = $('#alter_stock_limit #stock_alter').val();
			ManageObject.object.loading.loading();
			if($('#alter_stock_limit .export').find('.iradio_square-green').hasClass('checked')){
				if(Number(current_number)>Number(stock_number)){
					ManageObject.object.loading.complete();
					ManageObject.object.toast.toast('库存数量不足！', '2');
				}else{
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
				}
			}else{
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
			}
		});
	});
	// 修改库存限制方式
	$('#is_stock_limit_alter').on('change', function(){
		if($(this).prop('checked')){
			$(this).parents('.input-group').find('#stock_alter').prop({'readonly':false, 'placeholder':'有库存限制'});
			$('#alter_stock_limit .active_form').removeClass('hidden');
		}else{
			$(this).parents('.input-group').find('#stock_alter').prop({'readonly':true, 'placeholder':'无库存限制'}).val('');
			$('#alter_stock_limit .active_form').addClass('hidden');
		}
	});
	// 出入库记录
	$('.seeList').on('click', function(){
		var id = $(this).parent().attr('data-id');
		Common.ajax({
			data    :{requestType:'get_inventory_history', id:id},
			callback:function(r){
				var str = '';
				$.each(r.list, function(index, value){
					str += template.replace('$type', value.type).replace('$number', value.number)
								   .replace('$createTime', value.creatime).replace('$comment', value.comment);
				})
				var $tableTbody = $('#inventory_history_body');
				$tableTbody.html(str);
			}
		});
	});
});
function checkIsEmpty(){
	var $project_name         = $('#project_name');
	var $project_price_create = $('#project_price_create');
	if($project_name.val() == ''){
		ManageObject.object.toast.toast("项目名称不能为空!");
		$project_name.focus();
		return false;
	}
	if($project_price_create.val() == ''){
		ManageObject.object.toast.toast("项目价格不能为空!");
		$project_price_create.focus();
		return false;
	}
	return true;
}
function checkIsEmptyAlter(){
	var $project_name_modify  = $('#project_name_modify');
	var $project_price_modify = $('#project_price_modify');
	if($project_name_alter.val() == ''){
		ManageObject.object.toast.toast("项目名称不能为空!");
		$project_name_alter.focus();
		return false;
	}
	if($project_price_modify.val() == ''){
		ManageObject.object.toast.toast("项目价格不能为空!");
		$project_price_modify.focus();
		return false;
	}
	return true;
}
