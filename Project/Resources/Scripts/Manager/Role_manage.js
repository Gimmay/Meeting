/**
 * Created by qyqy on 2016-9-9.
 */
var roleManage = {
	asignRoleTemp:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\'>$name</a>',
	authorizeRoleTemp:'<a class=\"btn btn-default btn-sm btn_role\" href="javascript:void(0)" role="button" data-id=\'$id\' data-type=\'$type\'>$name</a>',
	authorizeEmployeeTemp:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\' data-type=\'$type\'>$name</a>',
	bindEvent:function(){
		/*
		 * 授权
		 */
		$('#authorize_all a').on('click',function(){
			var id = $(this).attr('data-id');
			var employee_id = $('input[name=hide_employee_id]').val();
			var name = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({data:{requestType:'assign_permission',pid:id,id:employee_id},callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					$('#authorize_all a').each(function(){
						if($(this).attr('data-id') == id){
							$(this).remove();
						}
					});
					select_temp = roleManage.authorizeEmployeeTemp.replace('$id',id).replace('$name',name);
					$('#authorize_select').append(select_temp);
					roleManage.unbindEvent();
					roleManage.bindEvent();
				}
			}});
		});
		// 点击已选择角色，删除并将其移至未选择区域
		$('#authorize_select a').on('click',function(){
			var id = $(this).attr('data-id');
			var type = $(this).attr('data-type');
			var employee_id = $('input[name=hide_employee_id]').val();
			var name = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({data:{requestType:'anti_assign_permission',pid:id,id:employee_id,type:type},callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					$('#authorize_select a').each(function(){
						if($(this).attr('data-id') == id){
							$(this).remove();
						}
					});
					select_temp = roleManage.authorizeEmployeeTemp.replace('$id',id).replace('$name',name);
					$('#authorize_all').append(select_temp);
					roleManage.unbindEvent();
					roleManage.bindEvent();
				}
			}});
		});
	},
	unbindEvent:function(){
		$('#authorize_select a').off('click');
		$('#authorize_all a').off('click');
	},
	searchAuthorize:function(){
		var eid = $('input[name=hide_employee_id]').val();
		console.log(eid);
		var keyword = $('#authorize_search input').val();
		ManageObject.object.loading.loading(true);
		Common.ajax({data:{requestType:'get_unassigned_permission',id:eid,keyword:keyword},async:false, callback:function(data){
			ManageObject.object.loading.complete();
			console.log(data);
			var str ='';
			$.each(data,function(index,value){
				str+=roleManage.authorizeEmployeeTemp.replace('$id',value.id).replace('$name',value.name).replace('$type',value.type);
			});
			$('#authorize_all').html(str);
		}});
		roleManage.bindEvent();
	}
};
$(function(){
	/*
	 *  授权按钮-ajax请求
	 *  授权搜索按钮
	 *  回车搜索
	 */
	$('.authorize_btn').on('click',function(){
		var data_id = $(this).parent('.btn-group').attr('data-id');
		// 初始化已选择的权限
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_assigned_permission', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				var str     = '';
				if(data){
					$.each(data, function(index, value){
							str += roleManage.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name).replace('$type',value.type);
					});
					$('input[name=hide_employee_id]').val(data_id);
					$('#authorize_select').html(str);
				}
			}
		});
		// 初始化未选择的权限
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_unassigned_permission', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				var str     = '';
				if(data){
					$.each(data, function(index, value){
						console.log(value.id);
						str += roleManage.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name);
					});
					console.log(str);
					$('input[name=hide_employee_id]').val(data_id);
					$('#authorize_all').html(str);
				}
			}
		});
		roleManage.bindEvent();
	});
	$('#authorize_search .input-group-addon').on('click',function(){
		roleManage.searchAuthorize();
	});
	$('#authorize_search input[type=text]').on('keydown',function(e){
		if(e.keyCode == 13) {
			$("#authorize_search .input-group-addon").click();
			return false;
		}
	});

	// 单个员工删除
	$('.delete_btn').on('click',function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_employee').find('input[name=id]').val(id);
	});

	// 批量删除员工
	$('.batch_delete_btn').on('click',function(){

	});
})
