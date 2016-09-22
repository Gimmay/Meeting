/**
 * Created by 1195 on 2016-9-9. Role
 */
var temp = {
	asignRoleTemp:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\'>$name</a>',
	authorizeRoleTemp:'<a class=\"btn btn-default btn-sm btn_role\" href="javascript:void(0)" role="button" data-id=\'$id\' data-type=\'$type\' data-role-id="$role_id">$name</a>',
	authorizeEmployeeTemp:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\' data-type=\'$type\'>$name</a>',
	bindEvent:function(){
		/*
		* 分配角色
		*/
		// 点击未选择角色，删除并将其移至已寻找区域
		$('.all_role_area a').on('click',function(){
			var id = $(this).attr('data-id');
			var employee_id = $('input[name=hide_employee_id]').val();
			var name = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({data:{requestType:'assign_role',rid:id,id:employee_id},callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					$('.all_role_area a').each(function(){
						if($(this).attr('data-id') == id){
							$(this).remove();
						}
					});
					select_temp = temp.asignRoleTemp.replace('$id',id).replace('$name',name);
					$('.select_role_area').append(select_temp);
					temp.unbindEvent();
					temp.bindEvent();
				}
			}});
		});
		// 点击已选择角色，删除并将其移至未选择区域
		$('.select_role_area a').on('click',function(){
			var id = $(this).attr('data-id');
			var employee_id = $('input[name=hide_employee_id]').val();
			var name = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({data:{requestType:'anti_assign_role',rid:id,id:employee_id},callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					$('.select_role_area a').each(function(){
						if($(this).attr('data-id') == id){
							$(this).remove();
						}
					});
					select_temp = temp.asignRoleTemp.replace('$id',id).replace('$name',name);
					$('.all_role_area').append(select_temp);
					temp.unbindEvent();
					temp.bindEvent();
				}
			}});
		});

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
					select_temp = temp.authorizeEmployeeTemp.replace('$id',id).replace('$name',name);
					$('#authorize_select').append(select_temp);
					temp.unbindEvent();
					temp.bindEvent();
				}
			}});
		});
		// 点击已选择角色，删除并将其移至未选择区域
		$('#authorize_select a').on('click',function(){
			var id = $(this).attr('data-id');
			var type = $(this).attr('data-type');
			var role_id = $(this).attr('data-role-id');
			var employee_id = $('input[name=hide_employee_id]').val();
			var name = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({data:{requestType:'anti_assign_permission',pid:id,id:employee_id,type:type,rid:role_id},callback:function(data){
				ManageObject.object.loading.complete();
				if(data.status){
					$('#authorize_select a').each(function(){
						if($(this).attr('data-id') == id){
							$(this).remove();
						}
					});
					select_temp = temp.authorizeEmployeeTemp.replace('$id',id).replace('$name',name);
					$('#authorize_all').append(select_temp);
					temp.unbindEvent();
					temp.bindEvent();
				}
			}});
		});
	},
	unbindEvent:function(){
		$('.select_role_area a').off('click');
		$('.all_role_area a').off('click');
		$('#authorize_select a').off('click');
		$('#authorize_all a').off('click');
	},
	searchRole:function(){
		var eid = $('input[name=hide_employee_id]').val();
		console.log(eid);
		var keyword = $('.role_search_input').val();
		ManageObject.object.loading.loading(true);
		Common.ajax({data:{requestType:'get_unassigned_role',id:eid,keyword:keyword},async:false, callback:function(data){
			ManageObject.object.loading.complete();
			console.log(data);
			var str ='';
			$.each(data,function(index,value){
				str+=temp.asignRoleTemp.replace('$id',value.id).replace('$name',value.name);
			});
			$('.all_role_area').html(str);
		}});
		temp.bindEvent();
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
				str+=temp.authorizeRoleTemp.replace('$id',value.id).replace('$name',value.name).replace('$type',value.type);
			});
			$('#authorize_all').html(str);
		}});
		temp.bindEvent();
	}
};
$(function(){
	/*
	*  分配角色按钮-ajax请求
	*  分配角色搜索按钮
	*  回车搜索
	 */
	$('.btn_distribution_role').on('click',function(){
		var data_id = $(this).parent('.btn-group').attr('data-id');
		// 初始化已选择的角色
		ManageObject.object.loading.loading();
		Common.ajax({data:{requestType:'get_assigned_role',id:data_id},async:false, callback:function(data){
			ManageObject.object.loading.complete();
			var str ='';
			if(data){
				$.each(data,function(index,value){
					str+=temp.asignRoleTemp.replace('$id',value.id).replace('$name',value.name);
				});
				$('input[name=hide_employee_id]').val(data_id);
				$('.select_role_area').html(str);
			}
		}});

		// 初始化未选择的角色
		ManageObject.object.loading.loading();
		Common.ajax({data:{requestType:'get_unassigned_role',id:data_id},async:false, callback:function(data){
			ManageObject.object.loading.complete();
			console.log(data);
			var str ='';
			$.each(data,function(index,value){
				str+=temp.asignRoleTemp.replace('$id',value.id).replace('$name',value.name);
			});
			$('.all_role_area').html(str);
		}});
		temp.bindEvent();
	});
	$('.role_search_btn').on('click',function(){
		temp.searchRole();
	});
	$('.role_search_input').on('keydown',function(e){
		if(e.keyCode == 13) {
			$(".role_search_btn").click();
			return false;
		}
	});

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
						if(value.type == '0'){
							str += temp.authorizeRoleTemp.replace('$id', value.id).replace('$name', value.name).replace('$type',value.type).replace('$role_id', value.rid);
						}else if(value.type == '1'){
							str += temp.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name).replace('$type',value.type);
						}
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
						str += temp.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name);
					});
					console.log(str);
					$('input[name=hide_employee_id]').val(data_id);
					$('#authorize_all').html(str);
				}
			}
		});
		temp.bindEvent();
	});
	$('#authorize_search .input-group-addon').on('click',function(){
		temp.searchAuthorize();
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
	$('.batch_delete_btn_confirm').on('click',function(){
		var str = '';
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str+=id+','
		});
		var s,newStr="";
		s = str.charAt(str.length-1);
		if(s==","){
			for(var i=0;i<str.length-1;i++){
				newStr+=str[i];
			}
			console.log(newStr);
		}
		$('#batch_delete_employee').find('input[name=id]').val(newStr);
	});

	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click',function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
});