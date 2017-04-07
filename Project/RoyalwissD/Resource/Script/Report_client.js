/**
 * Created by 1195 on 2017-4-5.
 */

$(function(){
	$('#control_form .btn-save').on('click',function(){
		var data = $('#control_form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data:data,
			callback:function(r){
				ManageObject.object.loading.complete();
			}
		})
		
	});
});
