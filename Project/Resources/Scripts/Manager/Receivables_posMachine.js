/**
 * Created by qyqy on 2016-11-17.
 */
$(function(){

	// 修改支付方式
	$('.modify_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.payMethod').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_pos_machine').find('input[name=id]').val(id);
		$('#alter_pos_machine').find('#pos_machine_a').val(name);
		$('#alter_pos_machine').find('#comment_a').val(comment);
	});
	// 删除支付方式
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_pos_machine').find('input[name=id]').val(id);
	});
});