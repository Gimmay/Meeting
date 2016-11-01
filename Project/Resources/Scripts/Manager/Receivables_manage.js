/**
 * Created by 1195 on 2016-10-19.
 */

var ThisObject = {
	aTemp:'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$name</a>'
};
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	var $add_receivables_modal = $('#add_receivables');
	ManageObject.object.meetingName.onQuasarSelect(function(){
		var value = ManageObject.object.meetingName.getValue();
		$add_receivables_modal.find('input[name=mid]').val(value);
	});
	ManageObject.object.clientName.onQuasarSelect(function(){
		var value = ManageObject.object.clientName.getValue();
		$add_receivables_modal.find('input[name=cid]').val(value);
	});
	$('.coupon_list a').on('click', function(){
		var id = $(this).attr('data-id');
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
	});
	$('.coupon_list a').on('click', function(){
		var arr = [];
		$('.coupon_list a.active').each(function(){
			var id = $(this).attr('data-id');
			arr.push(id)
		});
		$('#add_receivables').find('input[name=coupon_code]').val(arr);
	});
	$('#price').on('focus',function(){
		$('#idCalculadora').removeClass('hide');
	});
	$('.close_calcuator button').on('click',function(){
		$('#idCalculadora').addClass('hide');
	})
	$('.equal').on('click',function(){
		var sum = $('#input-box').val();
		$('#price').val(sum);
	});
	$('.clear-marginleft').on('click',function(){
		$('#price').val(0);
	});

	// 删除收款记录
	$('.delete_btn').on('click',function(){
		var id =$(this).parents('.btn-group').attr('data-id');
		$('#delete_receivables').find('input[name=id]').val(id);
	});

	// 修改收款
	$('.modify_btn').on('click',function(){
		var id =$(this).parents('.btn-group').attr('data-id');
		var price =$(this).parents('tr').find('.price').text();
		var type =$(this).parents('tr').find('.type').text();
		var method =$(this).parents('tr').find('.method').text();
		var time =$(this).parents('tr').find('.time').text();
		var place =$(this).parents('tr').find('.place').text();
		var source_type =$(this).parents('tr').find('.source_type').text();
		var comment =$(this).parents('tr').find('.comment').text();
		$('#alter_receivables').find('input[name=id]').val(id);
		$('#alter_receivables').find('#price_a').val(price);
		$('#alter_receivables').find('#selected_method').val(method);
		$('#alter_receivables').find('#selected_type').val(type);
		$('#alter_receivables').find('#place_a').val(place);
		$('#alter_receivables').find('#source_type').val(source_type);
		$('#alter_receivables').find('#receivables_time_a').val(time);
		$('#alter_receivables').find('#comment_a').val(comment);
		Common.ajax({
			data:{requestType:'alter_coupon',id:id},
			callback:function(r){
				console.log(r);
			}
		});
	});
});
