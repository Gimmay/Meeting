/**
 * Created by qyqy on 2016-9-12.
 */
var ScriptObject = {
	detailsLiTemp   :'<tr><td>#key#</td><td>#name#</td></tr>',
	detailsLiTempImg:'<tr><td>#key#</td><td><img class="qrcode-style" src="#src#" alt=""></td></tr>',
	bindEvent       :function(){
		var self = this;
		$('.release_a').on('click', function(){
			var id = $(this).attr('data-id');
			var t  = $(this);
			Common.ajax({
				data    :{requestType:'release', id:id},
				callback:function(data){
					console.log(data);
					if(data.status){
						ManageObject.object.toast.toast(data.message);
						t.removeClass('release_a').addClass('cancel_release_a').text('新建');
						self.unbindEvent();
						self.bindEvent();
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(data.message);
					}
				}
			})
		});
		// 取消发布
		$('.cancel_release_a').on('click', function(){
			var id = $(this).attr('data-id');
			var t  = $(this);
			Common.ajax({
				data    :{requestType:'cancel_release', id:id},
				callback:function(data){
					console.log(data);
					if(data.status){
						ManageObject.object.toast.toast(data.message);
						t.removeClass('cancel_release_a').addClass('release_a').text('发布');
						self.unbindEvent();
						self.bindEvent();
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(data.message);
					}
				}
			})
		});
	},
	unbindEvent     :function(){
		$('.release_a').off('click');
		$('.cancel_release_a').off('click');
	},
	roleListTemp    :'<tr>\n\t<td>$i</td>\n\t<td class="name">$name</td>\n\t<td>$comment</td>\n\t<td>\n\t\t<div class="btn-group" data-id="$id">\n\t\t\t<button type="button" class="btn btn-default btn-xs choose_user" data-toggle="modal" data-target="#add_meeting_manager">添加</button>\n\t\t\t<button type="button" class="btn btn-default btn-xs get_meeting_manager" data-toggle="modal" data-target="#see_meeting_manager">查看人员</button>\n\t\t</div>\n\t</td>\n</tr>',
	userListTemp    :'<tr>\n\t<td class="check_item_e"><input type="checkbox" class="icheck" value="$id" placeholder=""></td>\n\t<td>$num</td>\n\t<td class="name">$name</td>\n\t<td class="nickname">$nickname</td>\n</tr>',
	userListTemp2   :'<tr data-id="$id" data-uid="$uid">\n\t<td>$num</td>\n\t<td class="name">$name</td>\n\t<td class="nickname">$nickname</td>\n\t<td>\n\t\t<button type="button" class="btn btn-default btn-xs delete_user" data-toggle="modal" data-target="#delete_meeting_manager">删除</button>\n\t</td>\n</tr>',
};
$(function(){
	$('#add_meeting_manager').keydown(function(e){
		console.log(e.keyCode);
		if(e.keyCode == 13){
			$('#add_meeting_manager .main_search').trigger('click');
		}
	});
	ScriptObject.bindEvent();
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 会议详情
	$('.details_btn').on('click', function(){
		var id = $(this).attr('data-id');
		Common.ajax({
			data    :{requestType:'get_detail', id:id},
			callback:function(r){
				var str = '';
				console.log(r);
				$.each(r[0], function(index1, value1){
					$.each(r[1], function(index2, value2){
						if(index1 == index2){
							if(value2 != null){
								if(index1 == 'logo'){
									str += ScriptObject.detailsLiTempImg.replace('#key#', value1)
													   .replace('#src#', value2)
								}else{
									str += ScriptObject.detailsLiTemp.replace('#key#', value1)
													   .replace('#name#', value2)
								}
							}else{
								str += ScriptObject.detailsLiTemp.replace('#key#', value1)
												   .replace('#name#', '')
							}
						}
					})
				});
				$('.info').html(str);
			}
		});
	});
	// 会议管理角色列表
	$('.meeting_manager_btn').on('click', function(){
		var mid  = $(this).parents('ul.fun_btn').attr('data-id');
		var name = $(this).parents('.meeting_li').find('.meeting_name').text();
		$('#choose_meeting_manager').find('input[name=id]').val(mid);
		$('#role_list_modal').find('input[name=mid]').val(mid);
		$('#add_meeting_manager').find('input[name=mid]').val(mid);
		$('#role_list_modal').find('.meeting_name').text(name);
		$('#add_meeting_manager').find('.meeting_name').text(name);
		$('#see_meeting_manager').find('.meeting_name').text(name);
		var str = '', str2 = '';
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_role', id:mid},
			callback:function(data){
				ManageObject.object.loading.complete();
				console.log(data);
				$.each(data, function(index, value){
					str += ScriptObject.roleListTemp.replace('$i', index+1).replace('$name', value.name)
									   .replace('$level', value.level).replace('$comment', value.comment)
									   .replace('$id', value.id)
				});
				$('#role_list').html(str);
				// 添加人
				$('#role_list_modal .choose_user').on('click', function(){
					var id = $(this).parent('.btn-group').attr('data-id');
					$('#add_meeting_manager').find('input[name=rid]').val(id);
					var str = '', i = 0;
					ManageObject.object.loading.loading();
					Common.ajax({
						data    :{requestType:'get_user', rid:id, mid:mid},
						callback:function(data){
							ManageObject.object.loading.complete();
							$.each(data, function(index, value){
								str += ScriptObject.userListTemp.replace('$num', index+1)
												   .replace('$name', value.name)
												   .replace('$id', value.id).replace('$nickname', value.nickname)
								i++;
							});
							$('#add_meeting_manager').find('.current_attendee').text(i);
							$('#user_body').html(str);
							$('.icheck').iCheck({
								checkboxClass:'icheckbox_square-green',
								radioClass   :'iradio_square-green'
							});
							all_check_e();
						}
					});
				});
				// 查看角色已添加员工
				$('.get_meeting_manager').on('click', function(){
					var rid = $(this).parent('.btn-group').attr('data-id');
					ManageObject.object.loading.loading();
					get_user_list(mid, rid);
				});
			}
		});
	});
	// 搜索员工
	$('#add_meeting_manager').find('.main_search').on('click', function(){
		var keyword = $(this).parents('.repertory_text').find('input[name=keyword]').val();
		var mid     = $('#add_meeting_manager').find('input[name=mid]').val();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_user2', keyword:keyword, mid:mid},
			callback:function(data){
				ManageObject.object.loading.complete();
				console.log(data);
				var str = '', i = 0;
				$.each(data, function(index, value){
					if(value.gender == 0){
						var gender = '未知';
					}else if(value.gender == 1){
						var gender = '男';
					}else if(value.gender == 2){
						var gender = '女';
					}
					str += ScriptObject.userListTemp.replace('$num', index+1)
									   .replace('$name', value.name)
									   .replace('$id', value.id).replace('$nickname', value.nickname)
					i++;
				});
				$('#add_meeting_manager').find('.current_attendee').text(i);
				$('#user_body').html(str);
				$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				});
				all_check_e();
			}
		});
	});
	// 确认添加
	$('#add_meeting_manager').find('.btn_save').on('click', function(){
		var arr = [];
		var rid = $('#add_meeting_manager').find('input[name=rid]').val();
		var mid = $('#add_meeting_manager').find('input[name=mid]').val();
		$('.check_item_e').find('.icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			arr.push(id);
		});
		ManageObject.object.loading.loading();
		if(arr != ''){
			Common.ajax({
				data    :{requestType:'assign_meeting_manager', uid:arr, rid:rid, mid:mid},
				callback:function(data){
					ManageObject.object.loading.complete();
					if(data.status == 1){
						$('#add_meeting_manager').modal('hide');
						ManageObject.object.toast.toast(data.message);
					}else if(data.status == 0){
						$('#add_meeting_manager').modal('hide');
						ManageObject.object.toast.toast(data.message);
					}
				}, error:function(){
					/*ManageObject.object.toast.toast('添加失败');*/
				}
			});
		}else{
			ManageObject.object.loading.complete();
			ManageObject.object.toast.toast('添加失败，未选择员工！！');
		}
	});
	// 点击过滤标签-全部
	$('#filter_btn_all').on('click', function(){
		var new_url = ManageObject.url.thisPage;
		new_url     = url_object.delUrlParam('process', new_url);
		location.replace(new_url);
	});
	// 点击过滤标签-进行中
	$('#filter_btn_ing').on('click', function(){
		var new_url = ManageObject.url.thisPage;
		new_url     = url_object.setUrlParam('process', 'ing', new_url);
		location.replace(new_url);
	});
	// 点击过滤标签-已结束
	$('#filter_btn_fin').on('click', function(){
		var new_url = ManageObject.url.thisPage;
		new_url     = url_object.setUrlParam('process', 'fin', new_url);
		location.replace(new_url);
	});
	var $search_config_modal = $('#search_config_modal');
	/**
	 * 搜索功能
	 */
	// 搜索配置
	$search_config_modal.find('.btn-item .btn').on('click', function(){
		if($(this).parent().hasClass('active')){
			$(this).parent().removeClass('active');
			$(this).removeClass('btn-info').addClass('btn-default');
		}else{
			$(this).parent().addClass('active');
			$(this).addClass('btn-info').removeClass('btn-default');
		}
		var str = [];
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$search_config_modal.find('input[name=column]').val(str);
	});
	// 全选搜索字段
	$('.sc_check_all').on('click', function(){
		$search_config_modal.find('.btn-item').each(function(){
			if(!$(this).hasClass('active')){
				$(this).addClass('active');
				$(this).find('.btn').addClass('btn-info').removeClass('btn-default');
			}
		});
		var str = [];
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$search_config_modal.find('input[name=column]').val(str);
	});
	// 取消
	$('.sc_cancel').on('click', function(){
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$(this).find('.btn').addClass('btn-default').removeClass('btn-info');
			}
		});
		var str = [];
		$search_config_modal.find('.btn-item').each(function(){
			if($(this).hasClass('active')){
				var name = $(this).find('.btn').attr('data-name');
				str.push(name);
			}
		});
		$search_config_modal.find('input[name=column]').val(str);
	});
	// 搜索配置提交
	$search_config_modal.find('.btn-save').on('click', function(){
		var data = $('#search_config_modal').find('form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					})
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		});
	});
	/**
	 * 列表字段功能
	 */
	// 自定义列表字段控制
	//  点击field_checkbox label标签
	$('.field_checkbox').on('click', function(){
		if($(this).find('.icheckbox_flat-blue').hasClass('checked')){
			var t_code = $(this).find('.icheck_f').attr('data-code');
			$('#field_list .btn').each(function(){
				var m_code = $(this).attr('data-code');
				if(t_code == m_code){
					$(this).addClass('show').removeClass('hide');
					$(this).find('input.c_view').val(1);
				}
			});
		}else{
			var t_code = $(this).find('.icheck_f').attr('data-code');
			$('#field_list .btn').each(function(){
				var m_code = $(this).attr('data-code');
				if(t_code == m_code){
					$(this).addClass('hide').removeClass('show');
					$(this).find('input.c_view').val(0);
				}
			});
		}
	});
	/**
	 * 客户字段控制
	 */
	// 选中字段操作
	$('.field_checkbox .icheck_f').on('ifChecked', function(){
		var t_code = $(this).parent().find('.icheck_f').attr('data-code');
		$('#field_list .btn').each(function(){
			var m_code = $(this).attr('data-code');
			if(t_code == m_code){
				$(this).addClass('show').removeClass('hide');
				$(this).find('input.c_view').val(1);
			}
		});
	});
	// 取消选中字段操作
	$('.field_checkbox .icheck_f').on('ifUnchecked', function(){
		var t_code = $(this).parent().find('.icheck_f').attr('data-code');
		$('#field_list .btn').each(function(){
			var m_code = $(this).attr('data-code');
			if(t_code == m_code){
				$(this).addClass('hide').removeClass('show');
				$(this).find('input.c_view').val(0);
			}
		});
	});
	// 保存列表字段设置
	$('#list_menu .btn-save').on('click', function(){
		var data = $('#list_menu form').serialize();
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
		})
	});
	// 会议配置
	$('.config_btn').on('click', function(){
		var $this_modal = $('#config_modal');
		var mid         = $(this).parent('li').attr('data-id');
		$this_modal.find('input[name=id]').val(mid);
		Common.ajax({
			data    :{
				requestType:'get_configure', mid:mid
			},
			callback:function(r){
				$this_modal.modal('show');
				if(r.sms_mobset_configure == null){
					r.sms_mobset_configure = 0;
				}
				if(r.wechat_official_configure == null){
					r.wechat_official_configure = 0;
				}
				if(r.wechat_enterprise_configure == null){
					r.wechat_enterprise_configure = 0;
				}
				if(r.email_configure == null){
					r.email_configure = 0;
				}
				$this_modal.find('select[name=sms_mobset_configure]').val(r.sms_mobset_configure);
				$this_modal.find('select[name=wechat_official_configure]').val(r.wechat_official_configure);
				$this_modal.find('select[name=wechat_enterprise_configure]').val(r.wechat_enterprise_configure);
				$this_modal.find('select[name=email_configure]').val(r.email_configure);
				$('input[name=wechat_mode]').eq(r.wechat_mode-1).iCheck('check');
			}
		});
	});
	// 会议配置保存
	$('#config_modal').find('.btn-save').on('click', function(){
		var $this_modal = $('#config_modal');
		var data        = $this_modal.find('form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
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
function get_user_list(mid, rid){
	Common.ajax({
		data    :{requestType:'get_meeting_manager', mid:mid, rid:rid},
		callback:function(data){
			ManageObject.object.loading.complete();
			var str = '';
			if(data != ''){
				$.each(data, function(index, value){
					str += ScriptObject.userListTemp2.replace('$num', index+1)
									   .replace('$name', value.name)
									   .replace('$id', value.id).replace('$uid', value.uid)
									   .replace('$nickname', value.nickname)
				});
				$('#user_body1').html(str);
				$('.delete_user').on('click', function(){
					var id  = $(this).parents('tr').attr('data-id');
					var uid = $(this).parents('tr').attr('data-uid');
					ManageObject.object.loading.loading();
					Common.ajax({
						data    :{requestType:'delete_meeting_manager', id:id, uid:uid, mid:mid, rid:rid},
						callback:function(data){
							ManageObject.object.loading.complete();
							console.log(data);
							if(data.status){
								ManageObject.object.toast.toast(data.message);
								get_user_list(mid, rid);
							}
						}
					})
				});
				$('.no_choice').hide();
			}else{
				$('#user_body1').empty();
				$('.no_choice').show();
			}
		}
	});
}
function all_check_e(){
	// 全选checkbox
	$('.all_check_e').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item_e').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item_e').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	$('.check_item_e').find('.iCheck-helper').on('click', function(){
		var n = 0;
		$('.check_item_e').find('.icheckbox_square-green.checked').each(function(){
			n++;
		});
		$('#add_meeting_manager').find('.selected_attendee').text(n);
	});
	$('.all_check_e').find('.iCheck-helper').on('click', function(){
		var i = 0;
		$('.check_item_e').find('.icheckbox_square-green.checked').each(function(){
			i++;
		});
		$('#add_meeting_manager').find('.selected_attendee').text(i);
	})
}

