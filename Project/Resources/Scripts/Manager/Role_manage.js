/**
 * Created by qyqy on 2016-9-9.
 */
var ScriptObject = {
	assignRoleTemp   :'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\'>$name</a>',
	authorizeRoleTemp:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\' data-group="$group-code" data-name="$group-name">$name</a>',
	pannelTemp       :'<div class="pannel_n" id="$id" data-group="$group">\n\t<div>\n\t\t<header class="title">$name <button type="button" class="btn btn-sm check_all">全选</button></header>\n\t\t<section>\n\t\t\t\n\t\t</section>\n\t</div>\n</div>',
	bindEvent        :function(){
		var self = this;
		/**
		 * 授权 未分配的授权分类列表
		 */
		$('#authorize_all a').on('click', function(){
			var id          = $(this).attr('data-id');
			var code        = $(this).attr('data-group');
			var group_name  = $(this).attr('data-name');
			var role_id     = $('input[name=hide_role_id]').val();
			var name        = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'assign_permission', pid:id, id:role_id}, callback:function(data){
					ManageObject.object.loading.complete();
					console.log(data);
					var htm = '';
					if(data.status){
						$('#authorize_all a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						if($('#authorize_select div[data-group='+code+']').length == 0){
							htm = ScriptObject.pannelTemp.replace('$group', code)
											  .replace('$name', group_name).replace('$id', code);
							$('#authorize_select').prepend(htm);
							setTimeout(function(){
								str = ScriptObject.authorizeRoleTemp.replace('$id', id)
												  .replace('$name', name)
												  .replace('$group-name', group_name)
												  .replace('$group-code', code);
								$('#authorize_select #'+code).find('section').prepend(str);
								/*ScriptObject.unbindEvent();
								 ScriptObject.bindEvent();*/
							}, 1000);
						}else{
							select_temp = ScriptObject.authorizeRoleTemp.replace('$id', id).replace('$name', name)
													  .replace('$group-code', code).replace('$group_name2', group_name);
							$('#authorize_select div[data-group='+code+']').find('section').prepend(select_temp);
						}
						ScriptObject.unbindEvent();
						ScriptObject.bindEvent();
					}
				}
			});
		});
		/**
		 * 点击已授予的权限，删除并将其移至未选择区域
		 */
		$('#authorize_select a').on('click', function(){
			var id          = $(this).attr('data-id');
			var type        = $(this).attr('data-type');
			var code        = $(this).attr('data-group');
			var group_name  = $(this).attr('data-name');
			var role_id     = $('input[name=hide_role_id]').val();
			var name        = $(this).text();
			var b_name      = name.substring(name.lastIndexOf(")")+1);
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'anti_assign_permission', pid:id, id:role_id}, callback:function(data){
					ManageObject.object.loading.complete();
					var htm = '';
					if(data.status){
						$('#authorize_select a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						if($('#authorize_all div[data-group='+code+']').length == 0){
							htm = ScriptObject.pannelTemp.replace('$group', code)
											  .replace('$name', group_name).replace('$id', code);
							$('#authorize_all').prepend(htm);
							setTimeout(function(){
								var str = ScriptObject.authorizeRoleTemp.replace('$id', id)
													  .replace('$name', name)
													  .replace('$group-name', group_name)
													  .replace('$group-code', code);
								$('#authorize_all #'+code).find('section').prepend(str);
								/*	ScriptObject.unbindEvent();
								 ScriptObject.bindEvent();*/
							}, 1000);
						}else{
							select_temp = ScriptObject.authorizeRoleTemp.replace('$id', id).replace('$name', name)
													  .replace('$group-code', code).replace('$group_name2', group_name);
							$('#authorize_all div[data-group='+code+']').find('section').prepend(select_temp);
						}
						ScriptObject.unbindEvent();
						ScriptObject.bindEvent();
					}
				}
			});
		});
		/**
		 * 一个模块权限全选   -- 已分配权限
		 */
		$('#authorize_select .check_all').on('click', function(){
			var code = $(this).parents('.pannel_n').attr('id');
			var e    = $(this).parents('.pannel_n');
			self.antiAssignGroup(code, e);
		});
		/**
		 * 一个模块权限全选  -- 未分配权限
		 */
		$('#authorize_all .check_all').on('click', function(){
			var code = $(this).parents('.pannel_n').attr('id');
			var e    = $(this).parents('.pannel_n');
			self.assignGroup(code, e);
		});
	},
	unbindEvent      :function(){
		$('#authorize_select a').off('click');
		$('#authorize_all a').off('click');
		$('#authorize_select .check_all').off('click');
		$('#authorize_all .check_all').off('click');
	},
	// 初始化权限界面
	initGetAuthorize :function(data_id){
		var self = this;
		// 初始化已选择的权限
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_assigned_permission', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				$('#authorize_select').empty();
				console.log(data);
				self.writeInSelectHtml(data, data_id);
			}
		});
		// 初始化未选择的权限
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_unassigned_permission', id:data_id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				$('#authorize_all').empty();
				//console.log(data);
				self.writeInAllHtml(data, data_id);
			}
		});
	},
	// 未选择权限搜索
	unSearchAuthorize:function(){
		var self    = this;
		var eid     = $('input[name=hide_role_id]').val();
		var keyword = $('#authorize_search input').val();
		ManageObject.object.loading.loading(true);
		Common.ajax({
			data    :{requestType:'get_unassigned_permission', id:eid, keyword:keyword},
			async   :false,
			callback:function(data){
				console.log(data);
				ManageObject.object.loading.complete();
				var str = '';
				self.writeInAllHtml(data, eid);
			}
		});
		ScriptObject.bindEvent();
	},
	// 已选择权限搜索
	searchAuthorize  :function(){
		var eid     = $('input[name=hide_role_id]').val();
		var keyword = $('#authorize_search_o input').val();
		ManageObject.object.loading.loading(true);
		Common.ajax({
			data    :{requestType:'get_assigned_permission', id:eid, keyword:keyword},
			async   :false,
			callback:function(data){
				ManageObject.object.loading.complete();
				var str = '';
				$.each(data, function(index, value){
					str += ScriptObject.authorizeRoleTemp.replace('$id', value.id).replace('$name', value.name)
									   .replace('$type', value.type);
				});
				$('#authorize_all').html(str);
			}
		});
		ScriptObject.bindEvent();
	},
	// 创建角色不能为空
	checkIsEmpty     :function(){
		var $create_role_name = $('#create_role_name');
		if($create_role_name.val() == ''){
			ManageObject.object.toast.toast("角色名称不能为空");
			$create_role_name.focus();
			return false;
		}
	},
	// 修改角色不能为空
	checkIsEmpty2    :function(){
		var $modify_role_name = $('#modify_role_name');
		if($modify_role_name.val() == ''){
			ManageObject.object.toast.toast("角色名称不能为空");
			$modify_role_name.focus();
			return false;
		}
	},
	// 写入已分配的权限
	writeInSelectHtml:function(data, data_id){
		var str = '', htm = '';
		$('#authorize_select').html('');
		if(data){
			$.each(data, function(index, value){
				//console.log(value);
				var group_code = value.group_code;
				if($('#authorize_select div[data-group='+group_code+']').length == 0){
					htm += ScriptObject.pannelTemp.replace('$group', group_code)
									   .replace('$name', value.group_name).replace('$id', group_code);
					//console.log(htm);
					$('#authorize_select').html(htm);
				}
				setTimeout(function(){
					str = ScriptObject.authorizeRoleTemp.replace('$id', value.id)
									  .replace('$name', value.name)
									  .replace('$group-name', value.group_name)
									  .replace('$group-code', value.group_code);
					$('#authorize_select #'+group_code).find('section').prepend(str);
					ScriptObject.unbindEvent();
					ScriptObject.bindEvent();
				}, 1000);
			});
			$('input[name=hide_role_id]').val(data_id);
		}
	},
	// 写入未分配的权限
	writeInAllHtml   :function(data, data_id){
		var str = '', htm = '';
		$('#authorize_all').html('');
		if(data){
			$.each(data, function(index, value){
				//var group_code= value.group_code.replace(/(^\s*)|(\s*$)/g, "");
				var group_code = value.group_code;
				if($('#authorize_all div[data-group='+group_code+']').length == 0){
					htm += ScriptObject.pannelTemp.replace('$group', group_code)
									   .replace('$name', value.group_name).replace('$id', group_code);
					$('#authorize_all').html(htm);
				}
				setTimeout(function(){
					str = ScriptObject.authorizeRoleTemp.replace('$id', value.id)
									  .replace('$name', value.name)
									  .replace('$group-name', value.group_name)
									  .replace('$group-code', value.group_code);
					$('#authorize_all #'+group_code).find('section').prepend(str);
					ScriptObject.unbindEvent();
					ScriptObject.bindEvent();
				}, 1000);
			});
		}
	},
	// 收回权限   已分配权限 -》 未分配权限
	antiAssignGroup  :function(code, e){
		var role_id = $('input[name=hide_role_id]').val();
		// ManageObject.object.loading.loading(true);
		Common.ajax({
			data    :{requestType:'anti_assign_permission_group', code:code, id:role_id},
			callback:function(r){
				//ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message);
					//e.remove();
					ScriptObject.initGetAuthorize('');
				}
			}
		})
	},
	// 授权   未分配权限 -》 已分配权限
	assignGroup      :function(code, e){
		var role_id = $('input[name=hide_role_id]').val();
		//ManageObject.object.loading.loading(true);
		Common.ajax({
			data    :{requestType:'assign_permission_group', code:code, id:role_id},
			callback:function(r){
				// ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message);
					ScriptObject.initGetAuthorize('');
				}
			}
		})
	}
}
$(function(){
	ScriptObject.bindEvent();
	// 未选择权限搜索
	$('#authorize_search .input-group-addon').on('click', function(){
		ScriptObject.unSearchAuthorize();
	});
	// 已选择权限搜索
	$('#authorize_search_o .input-group-addon').on('click', function(){
		ScriptObject.searchAuthorize();
	});
	/**
	 *  授权按钮-ajax请求
	 *  授权搜索按钮
	 *  回车搜索
	 */
	$('.authorize_btn').on('click', function(){
		var data_id = $(this).parent('.btn-group').attr('data-id');
		var name    = $(this).parents('tr').find('.name').text();
		$('#authorize_role').find('.role_name').text(name);
		ScriptObject.initGetAuthorize(data_id);
	});
	// 角色修改
	$('.modify_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#modify_role').find('input[name=id]').val(id);
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'get_role', id:id}, async:false, callback:function(data){
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
	// 单个角色删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_role ').find('input[name=id]').val(id);
	});
	// 批量删除角色
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_employee').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_delete_employee').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择客户！');
		}
		$('#batch_delete_employee').find('input[name=id]').val(newStr);
	});
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
});
