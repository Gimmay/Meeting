$(function(){

	// 修改支付方式
	$('.modify_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.receivablesType').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_receivablesType').find('input[name=id]').val(id);
		$('#alter_receivablesType').find('#receivablesType_name_a').val(name);
		$('#alter_receivablesType').find('#comment_a').val(comment);
	});
	// 删除支付方式
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_receivablesType').find('input[name=id]').val(id);
	});
});