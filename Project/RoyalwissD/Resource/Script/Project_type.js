/**
 * Created by 1195 on 2017-3-15.
 */


$(function(){
	// 保存项目类型
	$('#create_project_type .btn-save').on('click', function(){
		var project_type = $('#project_type').val();
		if(project_type != ''){
			var data = $('#create_project_type form').serialize();
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
		}else{
			ManageObject.object.toast.toast('项目类型不能为空！');
		}
	});
	// 修改项目
	$('.alter_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.name').text();
		var comment = $(this).parents('tr').find('.comment').text();
		$('#alter_project_type').find('input[name=name]').val(name);
		$('#alter_project_type').find('input[name=id]').val(id);
		$('#alter_project_type').find('textarea[name=comment]').val(comment);
		$('#alter_project_type .btn-save').on('click', function(){
			var project_type = $('#project_type_a').val();
			if(project_type != ''){
				var data = $('#alter_project_type form').serialize();
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
			}else{
				ManageObject.object.toast.toast('项目类型不能为空！');
			}
		});
	});
});
