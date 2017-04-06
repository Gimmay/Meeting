/**
 * Created by qyqy on 2016-11-17.
 */
$(function(){
	/**
	 * 新增pos机
	 */
	$('#add_pos_machine .btn-save').on('click', function(){
		var data         = $('#add_pos_machine form').serialize();
		var $pos_machine = $('#pos_machine');
		if($pos_machine.val() != ''){
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
			ManageObject.object.toast.toast('pos机不能为空！', '2');
		}
	});
	/**
	 * 修改pos机
	 */
	$('.modify_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.posMachine').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_pos_machine').find('input[name=id]').val(id);
		$('#alter_pos_machine').find('#pos_machine_a').val(name);
		$('#alter_pos_machine').find('#comment_a').val(comment);
		$('#alter_pos_machine .btn-save').on('click', function(){
			if($('#alter_pos_machine #pos_machine_a').val() != ''){
				var data = $('#alter_pos_machine form').serialize();
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
				ManageObject.object.toast.toast('pos机不能为空！');
			}
		});
	});
	// 删除支付方式
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_pos_machine').find('input[name=id]').val(id);
	});
});