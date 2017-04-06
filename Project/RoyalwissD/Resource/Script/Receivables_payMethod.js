$(function(){
	/**
	 * 新增支付方式
	 */
	$('#add_payMethod .btn-save').on('click', function(){
		var data            = $('#add_payMethod form').serialize();
		var $payMethod_name = $('#payMethod_name');
		if($payMethod_name.val() != ''){
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
		}else{
			ManageObject.object.toast.toast('支付方式不能为空！', '2');
		}
	});
	/**
	 * 修改支付方式
	 */
	$('.modify_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.payMethod').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_payMethod').find('input[name=id]').val(id);
		$('#alter_payMethod').find('#payMethod_name_a').val(name);
		$('#alter_payMethod').find('#comment_a').val(comment);
		$('#alter_payMethod .btn-save').on('click', function(){
			if($('#alter_payMethod #payMethod_name_a').val() != ''){
				var data = $('#alter_payMethod form').serialize();
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
			}else{
				ManageObject.object.toast.toast('支付方式不能为空！');
			}
		});
	});
	/**
	 * 删除支付方式
	 */

	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_payMethod').find('input[name=id]').val(id);
	});
});