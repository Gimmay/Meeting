/**
 * Created by 1195 on 2016-9-9. Role
 */
var temp = {
	assignRoleTemp       :'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\'>$name</a>',
	authorizeRoleTemp    :'<a class=\"btn btn-default btn-sm btn_role\" href="javascript:void(0)" role="button" data-id=\'$id\' data-type=\'$type\'>$name</a>',
	authorizeEmployeeTemp:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\' data-type=\'$type\'>$name</a>',
	bindEvent            :function(){
		/*
		 * 分配角色
		 */
		// 点击未选择角色，删除并将其移至已寻找区域
		$('.all_role_area a').on('click', function(){
			var id          = $(this).attr('data-id');
			var user_id     = $('input#selected_user_id').val();
			var name        = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'assign_role', rid:id, id:user_id}, callback:function(data){
					ManageObject.object.loading.complete();
					if(data.status){
						$('.all_role_area a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						select_temp = temp.assignRoleTemp.replace('$id', id).replace('$name', name);
						$('.select_role_area').append(select_temp);
						temp.unbindEvent();
						temp.bindEvent();
					}
				}
			});
		});
		// 点击已选择角色，删除并将其移至未选择区域
		$('.select_role_area a').on('click', function(){
			var id          = $(this).attr('data-id');
			var user_id     = $('input#selected_user_id').val();
			var name        = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'anti_assign_role', rid:id, id:user_id}, callback:function(data){
					ManageObject.object.loading.complete();
					if(data.status){
						$('.select_role_area a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						select_temp = temp.assignRoleTemp.replace('$id', id).replace('$name', name);
						$('.all_role_area').append(select_temp);
						temp.unbindEvent();
						temp.bindEvent();
					}
				}
			});
		});
		/*
		 * 授权
		 */
		$('#authorize_all a').on('click', function(){
			var id          = $(this).attr('data-id');
			var user_id     = $('input#selected_user_id').val();
			var name        = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'assign_permission', pid:id, id:user_id}, callback:function(data){
					ManageObject.object.loading.complete();
					if(data.status){
						$('#authorize_all a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						select_temp = temp.authorizeEmployeeTemp.replace('$id', id).replace('$name', name);
						$('#authorize_select').append(select_temp);
						temp.unbindEvent();
						temp.bindEvent();
					}
				}
			});
		});
		// 点击已选择角色，删除并将其移至未选择区域
		$('#authorize_select a').on('click', function(){
			var id          = $(this).attr('data-id');
			var type        = $(this).attr('data-type');
			var user_id     = $('input#selected_user_id').val();
			var name        = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'anti_assign_permission', pid:id, id:user_id, type:type}, callback:function(data){
					ManageObject.object.loading.complete();
					if(data.status){
						$('#authorize_select a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						select_temp = temp.authorizeEmployeeTemp.replace('$id', id).replace('$name', name);
						$('#authorize_all').append(select_temp);
						temp.unbindEvent();
						temp.bindEvent();
					}else{
						ManageObject.object.toast.toast(data.message);
					}
				}
			});
		});
	},
	unbindEvent          :function(){
		$('.select_role_area a').off('click');
		$('.all_role_area a').off('click');
		$('#authorize_select a').off('click');
		$('#authorize_all a').off('click');
	},
	searchRole           :function(){
		var eid = $('input#selected_user_id').val();
		console.log(eid);
		var keyword = $('.role_search_input').val();
		ManageObject.object.loading.loading(true);
		Common.ajax({
			data:{requestType:'get_unassigned_role', id:eid, keyword:keyword}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				console.log(data);
				var str = '';
				$.each(data, function(index, value){
					str += temp.assignRoleTemp.replace('$id', value.id).replace('$name', value.name);
				});
				$('.all_role_area').html(str);
			}
		});
		temp.bindEvent();
	},
	searchAuthorize      :function(){
		var eid = $('input#selected_user_id').val();
		console.log(eid);
		var keyword = $('#authorize_search input').val();
		ManageObject.object.loading.loading(true);
		Common.ajax({
			data    :{requestType:'get_unassigned_permission', id:eid, keyword:keyword},
			async   :false,
			callback:function(data){
				ManageObject.object.loading.complete();
				console.log(data);
				var str = '';
				$.each(data, function(index, value){
					str += temp.authorizeRoleTemp.replace('$id', value.id).replace('$name', value.name)
							   .replace('$type', value.type);
				});
				$('#authorize_all').html(str);
			}
		});
		temp.bindEvent();
	}
};
$(function(){
	/*
	 *  分配角色按钮-ajax请求
	 *  分配角色搜索按钮
	 *  回车搜索
	 */
	$('.btn_distribution_role').on('click', function(){
		var data_id = $(this).parent('.btn-group').attr('data-id');
		// 初始化已选择的角色
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_assigned_role', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				var str = '';
				if(data){
					$.each(data, function(index, value){
						str += temp.assignRoleTemp.replace('$id', value.id).replace('$name', value.name);
					});
					$('input#selected_user_id').val(data_id);
					$('.select_role_area').html(str);
				}
			}
		});
		// 初始化未选择的角色
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_unassigned_role', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				console.log(data);
				var str = '';
				$.each(data, function(index, value){
					str += temp.assignRoleTemp.replace('$id', value.id).replace('$name', value.name);
				});
				$('.all_role_area').html(str);
			}
		});
		temp.bindEvent();
	});
	$('.role_search_btn').on('click', function(){
		temp.searchRole();
	});
	$('.role_search_input').on('keydown', function(e){
		if(e.keyCode == 13){
			$(".role_search_btn").click();
			return false;
		}
	});
	/*
	 *  授权按钮-ajax请求
	 *  授权搜索按钮
	 *  回车搜索
	 */
	$('.authorize_btn').on('click', function(){
		var data_id = $(this).parent('.btn-group').attr('data-id');
		// 初始化已选择的权限
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_assigned_permission', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				var str = '';
				if(data){
					$.each(data, function(index, value){
						if(value.type == '0'){
							str += temp.authorizeRoleTemp.replace('$id', value.id).replace('$name', value.name)
									   .replace('$type', value.type).replace('$role_id', value.rid);
						}else if(value.type == '1'){
							str += temp.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name)
									   .replace('$type', value.type);
						}
					});
					$('input#selected_user_id').val(data_id);
					$('#authorize_select').html(str);
				}
			}
		});
		// 初始化未选择的权限
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_unassigned_permission', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				var str = '';
				if(data){
					$.each(data, function(index, value){
						console.log(value.id);
						str += temp.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name);
					});
					console.log(str);
					$('input#selected_user_id').val(data_id);
					$('#authorize_all').html(str);
				}
			}
		});
		temp.bindEvent();
	});
	$('#authorize_search .input-group-addon').on('click', function(){
		temp.searchAuthorize();
	});
	$('#authorize_search input[type=text]').on('keydown', function(e){
		if(e.keyCode == 13){
			$("#authorize_search .input-group-addon").click();
			return false;
		}
	});
	// 员工密码修改
	$(".btn_alter_password").on("click", function(){
		var id = $(this).parent('.btn-group').attr("data-id");
		$("#alter_password").find("input[name=id]").val(id);
		$('#alter_password .btn-save').on('click', function(){
			ManageObject.object.loading.loading();
			var data = $('#alter_password form').serialize();
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
		})
	});
	$(".password_btn").on("click", function(){
		//alert(111);return false;
		var old_password   = $("#old_password").val();
		var new_password   = $("#new_password").val();
		var new_password_2 = $("#new_password_2").val();
		var reg            = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,}$/;
		// 新密码功能判断条件
		if(!reg.test(new_password)){
			ManageObject.object.toast.toast("新密码至少6位，必须同时包含字母和数字！");
			return false;
		}else if(old_password == new_password){
			ManageObject.object.toast.toast("原密码与新密码不能相同！");
			return false;
		}else if(new_password != new_password_2){
			ManageObject.object.toast.toast("新密码与确认密码不一致！");
			return false;
		}
		// base64对新密码进行加密
		document.getElementById('old_password').value   = $("#old_password").val().base64Encode();
		document.getElementById('new_password').value   = $("#new_password").val().base64Encode();
		document.getElementById('new_password_2').value = $("#new_password_2").val().base64Encode();
		return true;
	});
	// 员工密码重置
	$('.btn_reset_password').on('click', function(){
		var id   = $(this).parent('.btn-group').attr('data-id');
		var name = $(this).parent().parent().parent().find('td[data-id='+id+']').text();
		$('#reset_password').find('input[name=id]').val(id);
		$('#reset_password_user_name').val(name).attr('value', name);
		$('#reset_password .btn-save').on('click', function(){
			ManageObject.object.loading.loading();
			var data = $('#reset_password form').serialize();
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
		})
	})
});