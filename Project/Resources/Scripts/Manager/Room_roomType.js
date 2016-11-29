/**
 * Created by qyqy on 2016-11-29.
 */

$(function(){

	// 修改支付方式
	$('.modify_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.name').text();
		var capacity    = $(this).parents('tr').find('.capacity').text();
		var price    = $(this).parents('tr').find('.price').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_roomType').find('input[name=id]').val(id);
		$('#alter_roomType').find('#name_a').val(name);
		$('#alter_roomType').find('#capacity_a').val(comment);
		$('#alter_roomType').find('#comment_a').val(comment);
	});
	// 删除支付方式
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#alter_roomType').find('input[name=id]').val(id);
	});
});