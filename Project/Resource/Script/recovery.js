/**
 * Created by Iceman on 2017-4-11.
 */

$(function(){
	/**
	 *  批量删除项目
	 */
	$('.batch_recover_btn').on('click', function(){
		var str = [], i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		$('#batch_recover_modal').find('.sAmount').text(i);
		if(str != ''){
			$('#batch_recover_modal').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择列表项！');
		}
		$('#batch_recover_modal').find('input[name=id]').val(str);
		$('#batch_recover_modal .btn-save').on('click', function(){
			var data = $('#batch_recover_modal form').serialize();
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
});
