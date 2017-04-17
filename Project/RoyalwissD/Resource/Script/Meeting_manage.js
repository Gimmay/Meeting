/**
 * Created by qyqy on 2016-9-12.
 */
var ScriptObject = {
	bindEvent    :function(){
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
					}else{
						ManageObject.object.toast.toast(data.message);
					}
				}
			})
		});
	},
	unbindEvent  :function(){
		$('.release_a').off('click');
		$('.cancel_release_a').off('click');
	},
	roleListTemp :'<tr>\n\t<td>$i</td>\n\t<td>$name</td>\n\t<td>$comment</td>\n\t<td>\n\t\t<div class="btn-group" data-id="$id">\n\t\t\t<button type="button" class="btn btn-default btn-xs choose_user" data-toggle="modal" data-target="#add_meeting_manager">添加</button>\n\t\t\t<button type="button" class="btn btn-default btn-xs get_meeting_manager" data-toggle="modal" data-target="#see_meeting_manager">查看人员</button>\n\t\t</div>\n\t</td>\n</tr>',
	userListTemp :'<tr>\n\t<td class="check_item_e"><input type="checkbox" class="icheck" value="$id" placeholder=""></td>\n\t<td>$num</td>\n\t<td class="name">$name</td>\n\t<td class="nickname">$nickname</td>\n</tr>',
	userListTemp2:'<tr data-id="$id" data-uid="$uid">\n\t<td>$num</td>\n\t<td class="name">$name</td>\n\t<td class="nickname">$nickname</td>\n\t<td>\n\t\t<button type="button" class="btn btn-default btn-xs delete_user" data-toggle="modal" data-target="#delete_meeting_manager">删除</button>\n\t</td>\n</tr>',
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
	// 发布
	/*// 单个会议删除
	 $('.delete_btn').on('click', function(){
	 var id = $(this).parents('ul.fun_btn').attr('data-id');
	 $('#delete_modal').find('input[name=id]').val(id);
	 });*/
	// 会议详情
	$('.details_btn').on('click', function(){
		var id = $(this).attr('data-id');
		Common.ajax({
			data    :{requestType:'get_detail', id:id},
			callback:function(r){
				console.log(r);
				var $right_details = $('.right_details');
				$right_details.find('.meeting_name').text(r.name);
				if(r.status == 0){
					$right_details.find('.status').text("禁用");
				}else if(r.status == 1){
					$right_details.find('.status').text("新建");
				}else if(r.status == 2){
					$right_details.find('.status').text("发布");
				}else if(r.status == 3){
					$right_details.find('.status').text("进行中");
				}else if(r.status == 4){
					$right_details.find('.status').text("结束");
				}
				$right_details.find('.type').text(r.type);
				$right_details.find('.host').text(r.host);
				$right_details.find('.plan').text(r.plan);
				$right_details.find('.place').text(r.place);
				$right_details.find('.hotel').text(r.hotel);
				$right_details.find('.car').text(r.car);
				$right_details.find('.setime').text(r.start_time+'-'+r.end_time);
				$right_details.find('.director_name').text(r.director);
				$right_details.find('.create_time').text(r.creatime);
				$right_details.find('.brief').html(r.brief);
				$right_details.find('.comment').html(r.comment);
				$right_details.find('.message_type').html(r.message_type);
				if(r.qrcode){
					$right_details.find('.QRcode img').attr('src', r.qrcode)
				}else{
					$('.QRcode').hide();
				}
				if(r.logo){
					$right_details.find('.logo img').attr('src', r.logo)
				}else{
					$('.details_btn .logo').hide();
				}
				var str = '';
				/*$.each(r,function(index,value){
				 if(!value.leave_time){
				 str+=ScriptObject.roomDetailsTemp.replace('$n',index+1).replace('$name',value.name).replace('$id',value.id);
				 }else{
				 str+=ScriptObject.roomDetailsTemp2.replace('$n',index+1).replace('$name',value.name).replace('$id',value.id);
				 }
				 });
				 $('#list_c').html(str);
				 leave_btn();
				 change_room();*/
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
					console.log(data);
					if(data.status == 1){
						$('#add_meeting_manager').modal('hide');
						ManageObject.object.toast.toast('添加成功！');
					}else if(data.status == 0){
						$('#add_meeting_manager').modal('hide');
						ManageObject.object.toast.toast('添加成功！');
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
					var id = $(this).parents('tr').attr('data-id');
					var uid = $(this).parents('tr').attr('data-uid');
					ManageObject.object.loading.loading();
					Common.ajax({
						data    :{requestType:'delete_meeting_manager', id:id, uid:uid, mid:mid, rid:rid},
						callback:function(data){
							ManageObject.object.loading.complete();
							console.log(data);
							if(data.status){
								ManageObject.object.toast.toast('删除成功！');
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

