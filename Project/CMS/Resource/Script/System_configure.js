$(function(){
	var $form = $('#form');
	$form.find('.btn-save').on('click', function(){
		var data = $form.serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, 1);
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					});
				}else{
					ManageObject.object.toast.toast(r.message, 2);
				}
			}
		});
	});
});