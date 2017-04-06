$(function(){
	$('#updateBackground').on('change', function(){
		$('input[name=requestType]').val('upload_receipt_logo');
		$('#submit_logo').trigger('click');
	});
	$('#form .btn-save').on('click', function(){
		var data = $('#form').serialize();
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
		});
	});
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
			ManageObject.object.toast.toast("Logo上传成功");
			$('input[name=logo]').val(data.imageUrl);
			$('.logo_not').hide();
			$('.logo_wp img').attr('src', data.imageUrl);
		}else{
			ManageObject.object.toast.toast("Logo上传失败");
		}
	});
	$('input[name=requestType]').val('set_config');
	return false;
}
