$(function(){

	// 修改支付方式
	$('.modify_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.payMethod').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_payMethod').find('input[name=id]').val(id);
		$('#alter_payMethod').find('#payMethod_name_a').val(name);
		$('#alter_payMethod').find('#comment_a').val(comment);
	});
	// 删除支付方式
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_payMethod').find('input[name=id]').val(id);
	});
});