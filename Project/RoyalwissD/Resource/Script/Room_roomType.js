/**
 * Created by qyqy on 2016-11-29.
 */

$(function(){
	$('#add_roomType .btn-save').on('click', function(){
		if(checkIsEmpty()){
			var data = $('#add_roomType form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload()
						});
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			});
		}
	});
	// 修改支付方式
	$('.modify_btn').on('click', function(){
		var $this_modal = $('#alter_roomType');
		var id          = $(this).parent('.btn-group').attr('data-id');
		$this_modal.find('input[name=id]').val(id);
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_room_type', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				$this_modal.find('#name_a').val(r.name);
				$this_modal.find('#capacity_a').val(r.capacity);
				$this_modal.find('#price_a').val(r.price);
				$this_modal.find('#number_a').val(r.number);
				$this_modal.find('#comment_a').val(r.comment);
				$this_modal.find('.btn-save').on('click', function(){
					var data = $this_modal.find('form').serialize();
					ManageObject.object.loading.loading();
					Common.ajax({
						data    :data,
						callback:function(r){
							ManageObject.object.loading.complete();
							if(r.status){
								ManageObject.object.toast.toast(r.message, '1');
								ManageObject.object.toast.onQuasarHidden(function(){
									location.reload()
								});
							}else{
								ManageObject.object.toast.toast(r.message, '2');
							}
						}
					})
				});
			}
		});
	});
});
function checkIsEmpty(){
	var $this_modal = $('#add_roomType');
	var name        = $this_modal.find('#name').val();
	var capacity    = $this_modal.find('#capacity').val();
	var price       = $this_modal.find('#price').val();
	var number      = $this_modal.find('#number').val();
	if(name == ''){
		ManageObject.object.toast.toast('名称不能为空！');
		$this_modal.find('#name').focus();
		return false;
	}
	if(capacity == ''){
		ManageObject.object.toast.toast('可容纳人数不能为空！');
		$this_modal.find('#capacity').focus();
		return false;
	}
	if(price == ''){
		ManageObject.object.toast.toast('价格不能为空！');
		$this_modal.find('#price').focus();
		return false;
	}
	if(number == ''){
		ManageObject.object.toast.toast('房间数量不能为空！');
		$this_modal.find('#number').focus();
		return false;
	}
	return true;
}
function checkIsEmptyAlter(){
	var $this_modal = $('#add_roomType');
	var name        = $this_modal.find('#name_a').val();
	var capacity    = $this_modal.find('#capacity_a').val();
	var price       = $this_modal.find('#price_a').val();
	var number      = $this_modal.find('#number_a').val();
	if(name == ''){
		ManageObject.object.toast.toast('名称不能为空！');
		$this_modal.find('#name_a').focus();
		return false;
	}
	if(capacity == ''){
		ManageObject.object.toast.toast('可容纳人数不能为空！');
		$this_modal.find('#capacity_a').focus();
		return false;
	}
	if(price == ''){
		ManageObject.object.toast.toast('价格不能为空！');
		$this_modal.find('#price_a').focus();
		return false;
	}
	if(number == ''){
		ManageObject.object.toast.toast('房间数量不能为空！');
		$this_modal.find('#number_a').focus();
		return false;
	}
	return true;
}