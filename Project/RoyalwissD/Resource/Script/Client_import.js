$(function(){
	$('#excel_file').change(function(){
		var src = $(this).val();
		var arr = src.split('\\');
		arr     = arr[arr.length-1];
		$('#excel_val').val(arr);
		$('#submit').removeAttr('disabled'); // 移除不可按属性
	});
	$('#submit').on('click', function(){
		upload();
	});
});
function upload(){
	var data = new FormData($('#form')[0]);
	ManageObject.object.loading.loading();
	console.log(data);
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
		ManageObject.object.loading.complete();
		if(data.status){
			ManageObject.object.toast.toast(data.message, '1');
			ManageObject.object.toast.onQuasarHidden(function(){
				location.href = data.nextPage;  // 跳转链接
			});
		}else{
			ManageObject.object.toast.toast(data.message, '2');
		}
	});
}