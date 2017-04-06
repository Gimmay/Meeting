/**
 * Created by Iceman on 2017-3-31.
 */

$(function(){
	/**
	 * 创建接口配置
	 */
	$('#create_modal .btn-save').on('click', function(){
		var data = $('#create_modal form').serialize();
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
		})
	});
	/**
	 * 修改取值
	 */
	$('.modify_btn').on('click', function(){
		var id          = $(this).parent().attr('data-id');
		var $this_modal = $('#alter_modal');
		Common.ajax({
			data    :{requestType:'get_detail', id:id},
			callback:function(r){
				$.each(r, function(index, value){
					console.log(index);
					console.log(value);
					if(index == 'mtype'){
						$this_modal.find('select[name=mtype]').val(value);
					}else if(index == 'comment'){
						$this_modal.find('textarea[name=comment]').val(value);
					}else{
						$this_modal.find('input[name='+index+']').val(value);
					}
				})
			}
		});
	});
	/**
	 * 修改确认
	 */
	$('#alter_modal .btn-save').on('click', function(){
		var data = $('#alter_modal form').serialize();
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
		})
	});
});
