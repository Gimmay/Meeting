$(function(){
	var prev_url = document.referrer;
	$('input[name=redirect]').val(prev_url);
	(function(){
		$('.form-group').each(function(){
			var must_id = $(this).find('input[type=hidden]').attr('data-must');
			if(must_id == 1){
				$(this).find('label').addClass('color-red').prepend("<b style=\'vertical-align: middle;\'>*</b>");
			}
		});
	})();
	$('.btn-save').on('click', function(){
		if(isEmpty()){
			var data = $('#form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.href = res.nextPage;
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		}
	});
});
function isEmpty(){
	var status = true;
	$('.form-group').each(function(){
		if($(this).find('input').hasClass('necessary')){
			console.log('1');
			var text = $(this).find('label ').text();
			text     = text.substring(0, text.length-1); //去掉最后一个字符
			text     = text.substring(1, text.length); //去掉第一个一个字符
			if($(this).find('.form-control').val() == ''){
				ManageObject.object.toast.toast(text+"不能为空");
				$(this).find('.form-control').focus();
				status = false;
				return false;
			}
		}
	});
	return !!status;
}