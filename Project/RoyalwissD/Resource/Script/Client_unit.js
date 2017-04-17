/**
 * Created by Iceman on 2017-4-17.
 */

$(function(){
	// 修改
	$('.alter_btn').on('click', function(){
		var $this_modal = $('#alter_modal');
		var id          = $(this).parent().attr('data-id');
		$this_modal.find('input[name=id]').val(id);
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_detail', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				$this_modal.find('input[name=name]').val(r.name);
				$this_modal.find('input[name=area]').val(r.area);
				$this_modal.find('select[name=is_new]').val(r.is_new);
				$this_modal.find('textarea[name=comment]').val(r.comment);
				// 修改项目保存
				$this_modal.find('.btn-save').on('click', function(){
					var data = $this_modal.find('form').serialize();
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
			}
		});
	});
});
function checkIsEmpty(){
	var $project_name         = $('#project_name');
	var $project_price_create = $('#project_price_create');
	if($project_name.val() == ''){
		ManageObject.object.toast.toast("项目名称不能为空!");
		$project_name.focus();
		return false;
	}
	if($project_price_create.val() == ''){
		ManageObject.object.toast.toast("项目价格不能为空!");
		$project_price_create.focus();
		return false;
	}
	return true;
}
