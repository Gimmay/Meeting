/**
 * Created by qyqy on 2016-9-9.
 */
$(function(){
	// 新增角色
	$('#create_role .btn-save').on('click', function(){
		var $create_role_name = $('#create_role_name');
		if($create_role_name.val() != ''){
			ManageObject.object.loading.loading();
			var data = $('#create_role form').serialize();
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
		}else{
			ManageObject.object.toast.toast('名称不能为空！');
			$create_role_name.focus();
		}
	});
	// 角色修改  --获取信息
	$('.modify_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#modify_role').find('input[name=id]').val(id);
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_detail', id:id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				if(data){
					$('#modify_role_name').val(data.name);
					if(data.status == 1){
						$('#modify_role_status').find('option:eq(0)').prop('selected', 'selected');
					}else{
						$('#modify_role_status').find('option:eq(1)').prop('selected', 'selected');
					}
					if(data.level == 1){
						$('#modify_role_level').find('option:eq(0)').prop('selected', 'selected');
					}
					if(data.level == 2){
						$('#modify_role_level').find('option:eq(1)').prop('selected', 'selected');
					}
					if(data.level == 3){
						$('#modify_role_level').find('option:eq(2)').prop('selected', 'selected');
					}
					if(data.level == 4){
						$('#modify_role_level').find('option:eq(3)').prop('selected', 'selected');
					}
					if(data.level == 5){
						$('#modify_role_level').find('option:eq(4)').prop('selected', 'selected');
					}
					$('#modify_role_comment').val(data.comment);
				}
			}
		});
	});
	// 角色修改 -- 保存信息
	$('#modify_role .btn-save').on('click', function(){
		var $modify_role_name = $('#modify_role_name');
		if($modify_role_name.val() != ''){
			ManageObject.object.loading.loading();
			var data = $('#modify_role form').serialize();
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
		}else{
			ManageObject.object.toast.toast('名称不能为空！');
			$modify_role_name.focus();
		}
	});
});
