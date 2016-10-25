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
		console.log(123);
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
	})
});
