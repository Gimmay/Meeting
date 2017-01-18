function checkIsEmpty(){
	var status = true;
	$('.form-group').each(function(){
		var must_id = $(this).find('input[type=hidden]').attr('data-must');
		if(must_id == 1){
			var text = $(this).find('label ').text();
			text = text.substring(0, text.length-1); //去掉最后一个字符
			text = text.substring(1, text.length); //去掉第一个一个字符
			if($(this).find('.form-control').val() == ''){
				CreateObject.object.toast.toast(text+"不能为空");
				$(this).find('.form-control').focus();
				status = false;
				return false;
			}
		}
	});
	return !!status;
}
$(function(){
	$('#updateBackground').on('change',function(){
		$('input[name=requestType]').val('upload_receipt_logo');
		$('#submit_logo').trigger('click');
	});
	$('.configure').on('click', function(){
		$('#client_create_configure').modal('show');
	});
	/*// 全选checkbox 显示
	 $('.all_check_blue').find('.iCheck-helper').on('click', function(){
	 if($(this).parent('.icheckbox_square-blue').hasClass('checked')){
	 $('.check_blue').find('.icheckbox_square-blue').addClass('checked');
	 }else{
	 $('.check_blue').find('.icheckbox_square-blue').removeClass('checked');
	 }
	 });

	 // 全选checkbox 必输项
	 $('.all_check_green').find('.iCheck-helper').on('click', function(){
	 if($(this).parent('.icheckbox_square-green').hasClass('checked')){
	 $('.check_green').find('.icheckbox_square-green').addClass('checked');
	 }else{
	 $('.check_green').find('.icheckbox_square-green').removeClass('checked');
	 }
	 });*/
	//显示
	$('.check_blue .iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-blue').hasClass('checked')){
			$(this).parents('.check_blue').find('input[type=hidden]').val('1');
		}else{
			$(this).parents('.check_blue').find('input[type=hidden]').val('0');
		}
	});
	//必输项
	$('.check_green .iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$(this).parents('.check_green').find('input[type=hidden]').val('1');
		}else{
			$(this).parents('.check_green').find('input[type=hidden]').val('0');
		}
	});
	(function(){
		$('.form-group').each(function(){
			var must_id = $(this).find('input[type=hidden]').attr('data-must');
			if(must_id == 1){
				$(this).find('label').addClass('color-red').prepend("<b style=\'vertical-align: middle;\'>*</b>");
			}
		});
	})();
});

function upLoadLogo(){
	var data = new FormData($('#form')[0]);
	$.ajax({
		url        :'',
		type       :'POST',
		data       :data,
		dataType   :'JSON',
		cache      :false,
		processData:false,
		contentType:false
	}).done(function(data){
		console.log(data);
		if(data.status){
			ThisObject.object.toast.toast("Logo上传成功");
			$('input[name=logo]').val(data.imageUrl);
			$('.logo_not').hide();
			$('.logo_wp img').attr('src',data.imageUrl);

		}else{
			ThisObject.object.toast.toast("Logo上传失败");
		}
	});
	$('input[name=requestType]').val('set_config');
	return false;
}
