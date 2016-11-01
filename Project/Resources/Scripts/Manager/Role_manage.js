/**
 * Created by qyqy on 2016-9-9.
 */
var ScriptObject = {
	asignRoleTemp        :'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\'>$name</a>',
	authorizeRoleTemp    :'<a class=\"btn btn-default btn-sm btn_role\" href="javascript:void(0)" role="button" data-id=\'$id\'>$name</a>',
	authorizeEmployeeTemp:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\' data-group="$group-code" data-name="$group-name">$name</a>',
	authorizeEmployeeTemp2:'<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id=\'$id\' data-group="$group-code" data-name="$group_name">($group_name2)$name</a>',
	pannelTemp           :'<div class="pannel_n" id="$id"  data-group="$group">\n\t<div>\n\t\t<header class="title">$name</header>\n\t\t<section>\n\t\t\t\n\t\t</section>\n\t</div>\n</div>',
	bindEvent            :function(){
		/*
		 * 授权
		 */
		$('#authorize_all a').on('click', function(){
			var id          = $(this).attr('data-id');
			var code = $(this).attr('data-group');
			var group_name = $(this).attr('data-name');
			var employee_id = $('input[name=hide_employee_id]').val();
			var name        = $(this).text();
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'assign_permission', pid:id, id:employee_id}, callback:function(data){
					ManageObject.object.loading.complete();
					if(data.status){
						$('#authorize_all a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						select_temp = ScriptObject.authorizeEmployeeTemp2.replace('$id', id).replace('$name', name).replace('$group-code',code).replace('$group_name',group_name).replace('$group_name2',group_name);
						$('#authorize_select').append(select_temp);
						ScriptObject.unbindEvent();
						ScriptObject.bindEvent();
					}
				}
			});
		});
		/*
		 * 点击已授予的权限，删除并将其移至未选择区域
		 */
		$('#authorize_select a').on('click', function(){
			var id          = $(this).attr('data-id');
			var type        = $(this).attr('data-type');
			var code = $(this).attr('data-group');
			var group_name = $(this).attr('data-name');
			var employee_id = $('input[name=hide_employee_id]').val();
			var name        = $(this).text();
			var b_name=name.substring(name.lastIndexOf(")")+1);
			var select_temp = '';
			ManageObject.object.loading.loading(true);
			Common.ajax({
				data:{requestType:'anti_assign_permission', pid:id, id:employee_id}, callback:function(data){
					ManageObject.object.loading.complete();
					if(data.status){
						$('#authorize_select a').each(function(){
							if($(this).attr('data-id') == id){
								$(this).remove();
							}
						});
						select_temp = ScriptObject.authorizeEmployeeTemp.replace('$id', id).replace('$name', b_name).replace('$group-name',group_name).replace('$group-code',code);
						$('#'+code).find('section').append(select_temp);
						ScriptObject.unbindEvent();
						ScriptObject.bindEvent();
					}
				}
			});
		});
	},
	unbindEvent          :function(){
		$('#authorize_select a').off('click');
		$('#authorize_all a').off('click');
	},
	searchAuthorize      :function(){
		var eid = $('input[name=hide_employee_id]').val();
		var keyword = $('#authorize_search input').val();
		ManageObject.object.loading.loading(true);
		Common.ajax({
			data    :{requestType:'get_unassigned_permission', id:eid, keyword:keyword},
			async   :false,
			callback:function(data){
				ManageObject.object.loading.complete();
				var str = '';
				$.each(data, function(index, value){
					str += ScriptObject.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name)
									 .replace('$type', value.type);
				});
				$('#authorize_all').html(str);
			}
		});
		ScriptObject.bindEvent();
	},
	checkIsEmpty         :function(){
		var $create_role_name = $('#create_role_name');
		if($create_role_name.val() == ''){
			ManageObject.object.toast.toast("角色名称不能为空");
			$create_role_name.focus();
			return false;
		}
	},
	checkIsEmpty2         :function(){
		var $modify_role_name = $('#modify_role_name');
		if($modify_role_name.val() == ''){
			ManageObject.object.toast.toast("角色名称不能为空");
			$modify_role_name.focus();
			return false;
		}
	}
};
$(function(){
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
						str += ScriptObject.authorizeEmployeeTemp2.replace('$id', value.id).replace('$name', value.name).replace('$group-code',value.group_code).replace('$group_name',value.group_name).replace('$group_name2',value.group_name);


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
				var str = '', htm = '';
				$('#authorize_all').empty();
				if(data){
					$.each(data, function(index, value){
						var group_code= value.group_code.replace(/(^\s*)|(\s*$)/g, "");
						if($('div[data-group='+group_code+']').length == 0){
							htm += ScriptObject.pannelTemp.replace('$group', group_code)
											 .replace('$name', value.group_name).replace('$id', group_code)
							$('#authorize_all').html(htm);
						}
						setTimeout(function(){
							str=ScriptObject.authorizeEmployeeTemp.replace('$id', value.id).replace('$name', value.name).replace('$group-name',value.group_name).replace('$group-code',value.group_code);
							$('#'+group_code).find('section').append(str);
							ScriptObject.unbindEvent();
							ScriptObject.bindEvent();
						},1000);

					});
				}
			}
		});
		ScriptObject.bindEvent();

	});
	$('#authorize_search .input-group-addon').on('click', function(){
		ScriptObject.searchAuthorize();
	});
	$('#authorize_search input[type=text]').on('keydown', function(e){
		if(e.keyCode == 13){
			$("#authorize_search .input-group-addon").click();
			return false;
		}
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
		var i =0;
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
		if(newStr!=''){
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
