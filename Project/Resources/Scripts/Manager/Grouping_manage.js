/**
 * Created by 1195 on 2016-12-2.
 */
var ScriptObject = {
	clientLiTemp:'<tr>\n\t<td class="check_item">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$num</td>\n\t<td>$unit</td>\n\t<td class="name">$name</td>\n\t<td>$clientType</td>\n\t<td>$gender</td>\n\t<td>$position</td>\n\t<td>$mobile</td>\n</tr>',
};
$(function(){
	/**
	 * 新建组
	 * 组长类型改变事件
	 * 组长类型 ：员工和客户
	 * 员工 ： 对应leader_employee_wrap     客户 ： 对应leader_client_wrap
	 */
	$('#add_group_modal #leader_type').on('change', function(){
		if($(this).find('option:selected').val() == 1){
			$('.leader_employee_wrap').addClass('hide');
			$('.leader_client_wrap').removeClass('hide');
		}else if($(this).find('option:selected').val() == 0){
			$('.leader_employee_wrap').removeClass('hide');
			$('.leader_client_wrap').addClass('hide');
		}
	});
	// 同上
	$('#add_group_modal #deputy_leader_type').on('change', function(){
		if($(this).find('option:selected').val() == 1){
			$('.deputy_leader_employee_wrap').addClass('hide');
			$('.deputy_leader_client_wrap').removeClass('hide');
		}else if($(this).find('option:selected').val() == 0){
			$('.deputy_leader_employee_wrap').removeClass('hide');
			$('.deputy_leader_client_wrap').addClass('hide');
		}
	});
	$('.alter_btn').on('click', function(){
		var gid                = $(this).parent('li').attr('data-id');
		var $alter_group_modal = $('#alter_group_modal');
		Common.ajax({
			data    :{requestType:'get_group_info', gid:gid},
			callback:function(r){
				console.log(r);
				$alter_group_modal.find('#code_alter').val(r.code); // 组号
				$alter_group_modal.find('#leader_type_alter option').eq(r.leader_type).prop('selected', true);
				/**
				 *  组长修改：
				 *  leader_type：0  -  组长类型为员工
				 *  leader_type：1  -  组长类型为客户
				 */
				if(r.leader_type == '0'){
					$alter_group_modal.find('.leader_alter_employee_wrap').removeClass('hide');
					$alter_group_modal.find('.leader_alter_client_wrap').addClass('hide');
					ThisObject.object.leaderAlterEmployee.setValue(r.leader);
					ThisObject.object.leaderAlterEmployee.setHtml(r.leader_name_name);
				}else if(r.leader_type == '1'){
					$alter_group_modal.find('.leader_alter_employee_wrap').addClass('hide');
					$alter_group_modal.find('.leader_alter_client_wrap').removeClass('hide');
					ThisObject.object.leaderAlterClient.setValue(r.leader);
					ThisObject.object.leaderAlterClient.setHtml(r.leader_name_name);
				}
				$alter_group_modal.find('#deputy_leader_type_alter option').eq(r.deputy_leader_type)
								  .prop('selected', true);
				/**
				 *  副组长修改：
				 *  leader_type：0  -  副组长类型为员工
				 *  leader_type：1  -  副组长类型为客户
				 */
				if(r.deputy_leader_type == '0'){
					$alter_group_modal.find('.deputy_leader_alter_employee_wrap').removeClass('hide');
					$alter_group_modal.find('.deputy_leader_alter_client_wrap').addClass('hide');
					ThisObject.object.deputyLeaderAlterEmployee.setValue(r.deputy_leader);
					ThisObject.object.deputyLeaderAlterEmployee.setHtml(r.deputy_leader_name);
				}else if(r.deputy_leader_type == '1'){
					$alter_group_modal.find('.deputy_leader_alter_employee_wrap').addClass('hide');
					$alter_group_modal.find('.deputy_leader_alter_client_wrap').removeClass('hide');
					ThisObject.object.deputyLeaderAlterClient.setValue(r.deputy_leader);
					ThisObject.object.deputyLeaderAlterClient.setHtml(r.deputy_leader_name);
				}
				$alter_group_modal.find('#comment_alter').val(r.comment);  // 备注
				$alter_group_modal.find('#gid').val(r.id);  // 组ID
			}
		});
	});
	/**
	 * 修改组
	 * 组长类型改变事件
	 * 组长类型 ：员工和客户
	 * 员工 ： 对应leader_employee_wrap     客户 ： 对应leader_client_wrap
	 */
	$('#alter_group_modal #leader_type_alter').on('change', function(){
		if($(this).find('option:selected').val() == 1){
			$('.leader_alter_employee_wrap').addClass('hide');
			$('.leader_alter_client_wrap').removeClass('hide');
		}else if($(this).find('option:selected').val() == 0){
			$('.leader_alter_employee_wrap').removeClass('hide');
			$('.leader_alter_client_wrap').addClass('hide');
		}
	});
	// 同上
	$('#alter_group_modal #deputy_leader_type_alter').on('change', function(){
		if($(this).find('option:selected').val() == 1){
			$('.deputy_leader_alter_employee_wrap').addClass('hide');
			$('.deputy_leader_alter_client_wrap').removeClass('hide');
		}else if($(this).find('option:selected').val() == 0){
			$('.deputy_leader_alter_employee_wrap').removeClass('hide');
			$('.deputy_leader_alter_client_wrap').addClass('hide');
		}
	});
	// 删除组
	$('.delete_btn').on('click', function(){
		var gid = $(this).parent('li').attr('data-id');
		$('#delete_modal').find('input[name=gid]').val(gid);
	});
	// 添加组员
	$('.add_client').on('click', function(){
		var id = $(this).attr('data-id');
		$('#add_crew').find('.btn_save').attr('data-id', id);
		getClient(id, '');
	});
	// 搜索组员
	$('.ajax_search').on('click', function(){
		var keyword = $('#add_crew').find('input[name=keyword]').val();
		var id      = $('#add_crew').find('.btn_save').attr('data-id');
		getClient(id, keyword);
	});
	// 查看所有组员
	$('.ajax_search_all').on('click', function(){
		var id = $('#add_crew').find('.btn_save').attr('data-id');
		getClient(id, '');
	});
	// 选择组员确认按钮
	$('#add_crew').find('.btn_save').on('click', function(){
		var arr = [];
		var gid = $(this).attr('data-id');
		$('.check_item').find('.icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			arr.push(id);
		});
		var str_string = arr.toString(); //数组转化为字符串
		console.log(str_string);
		ThisObject.object.loading.loading();
		if(arr != ''){
			Common.ajax({
				data    :{requestType:'save_client', id:str_string, gid:gid},
				callback:function(data){
					ThisObject.object.loading.complete();
					console.log(data);
					if(data.status){
						ThisObject.object.toast.toast('添加成功！');
						$('#add_crew').modal('hide');
						location.reload()
					}
				}, error:function(){
					ThisObject.object.toast.toast('添加失败');
				}
			});
		}else{
			ThisObject.object.loading.complete();
			ThisObject.object.toast.toast('添加失败，未选择员工！！');
		}
	});
	// 删除点击的组员
	$('.group_con a .g_close').on('click', function(){
		var cid = $(this).attr('data-id');
		$(this).parent().remove();
		ThisObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'delete_client', cid:cid},
			callback:function(r){
				ThisObject.object.loading.complete();
				console.log(r);
				if(r.status){
					ThisObject.object.toast.toast('组员删除完成！');
				}
			}
		});
	});
	// 清空组员
	$('.empty').on('click', function(){
		var gid = $(this).attr('data-id');
		$('#empty_modal').find('input[name=gid]').val(gid);
	});
	// 分数表写入分数
	$('.write').find('.w_edit').on('click', function(){
		var gid  = $(this).parent('td.write').attr('data-id');
		var date = $(this).parent('td.write').attr('data-date');
		$('#write_score').find('input[name=gid]').val(gid);
		$('#write_score').find('input[name=date]').val(date);
	});
	//保存分数
	$('.score_save').on('click', function(){
		var formData = $('#score_form').serialize();
		console.log(formData);
		ThisObject.object.loading.loading();
		Common.ajax({
			data    :formData,
			callback:function(r){
				console.log(r);
				ThisObject.object.loading.complete();
				if(r.status){
					ThisObject.object.toast.toast('写入成功！');
					$('#write_score').modal('hide');
					location.reload();
				}
			}
		})
	});
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 显示日期
	$('#meeting_time').on('change', function(){
		var number = $(this).find('option:selected').val();
		var url    = url_object.setUrlParam('date', number);
		location.replace(url);
	});
	(function(){
		// 日期
		var mvc    = $('#quasar_script').attr('data-url-sys-param');
		var suffix = $('#quasar_script').attr('data-page-suffix');
		var link   = new Quasar.UrlClass(1, mvc, suffix);
		var date   = link.getUrlParam('date');
		$('#meeting_time').find('option').eq(date-1).prop('selected', true);
	})();
});
function all_check(){
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	$('.check_item').find('.iCheck-helper').on('click', function(){
		var n = 0;
		$('.check_item').find('.icheckbox_square-green.checked').each(function(){
			n++;
		});
		$('#add_crew').find('.selected_attendee').text(n);
	});
	$('.all_check').find('.iCheck-helper').on('click', function(){
		var i = 0;
		$('.check_item').find('.icheckbox_square-green.checked').each(function(){
			i++;
		});
		$('#add_crew').find('.selected_attendee').text(i);
	})
}
function getClient(id, keyword){
	Common.ajax({
		data    :{requestType:'get_client', id:id, keyword:keyword},
		callback:function(r){
			console.log(r);
			var str    = '';
			var gender = '';
			var i      = 0;
			$.each(r, function(index, value){
				i++;
				if(value.gender == 0){
					gender = '未知';
				}else if(value.gender == 1){
					gender = '男';
				}else if(value.gender == 2){
					gender = '女';
				}
				str += ScriptObject.clientLiTemp.replace('$id', value.cid).replace('$num', index+1)
								   .replace('$name', value.name).replace('$gender', gender)
								   .replace('$position', value.position).replace('$mobile', value.mobile)
								   .replace('$unit', value.unit).replace('$clientType', value.type);
			});
			$('#add_crew').find('.current_attendee').text(i);
			$('#add_crew').find('#client_body').html(str);
			icheck                   :$('.icheck').iCheck({
				checkboxClass:'icheckbox_square-green',
				radioClass   :'iradio_square-green'
			});
			all_check();
		}
	});
}
