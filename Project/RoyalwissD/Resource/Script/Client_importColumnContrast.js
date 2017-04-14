/**
 * Created by Iceman on 2017-3-10.
 */

$(function(){
	$('.select_h').each(function(){
		$(this).on('change', function(){
			if($(this).parents('tr').find('.status span').hasClass('color-red')){
				$(this).parents('tr').find('.status span').addClass('glyphicon-ok-sign color-info')
					   .removeClass('glyphicon-info-sign color-red');
			}
		})
	});
	$('#form .btn-save').on('click', function(){
		var data = $('#form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(res){
				ManageObject.object.loading.complete();
				ManageObject.object.toast.toast(res.message, '1');
				ManageObject.object.toast.onQuasarHidden(function(){
					location.href = res.nextPage;
				});
			}
		});
	});
});