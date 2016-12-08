/**
 * Created by qyqy on 2016-9-12.
 */
var ScriptObject = {
	bindEvent        :function(){
		var self = this;
		$('.release_a').on('click', function(){
			var id = $(this).attr('data-id');
			var t  = $(this);
			Common.ajax({
				data    :{requestType:'release', id:id},
				callback:function(data){
					console.log(data);
					if(data.status){
						ThisObject.object.toast.toast('发布成功！');
						t.removeClass('release_a').addClass('cancel_release_a').text('取消发布');
						self.unbindEvent();
						self.bindEvent();
					}else{
						ThisObject.object.toast.toast('发布失败！');
					}
				}
			})
		});
		// 取消发布
		$('.cancel_release_a').on('click', function(){
			var id = $(this).attr('data-id');
			var t  = $(this);
			Common.ajax({
				data    :{requestType:'release', id:id},
				callback:function(data){
					console.log(data);
					if(data.status){
						ThisObject.object.toast.toast('取消发布成功！');
						t.removeClass('cancel_release_a').addClass('release_a').text('发布');
						self.unbindEvent();
						self.bindEvent();
					}else{
						ThisObject.object.toast.toast('取消发布失败！');
					}
				}
			})
		});
	},
	unbindEvent      :function(){
		$('.release_a').off('click');
		$('.cancel_release_a').off('click');
	},
	roleListTemp     :'<tr><td width="10%">$i</td><td width="20%">$name</td><td width="20%">$level</td><td width="20%">$comment</td><td width="30%"><div class="btn-group" data-id="$id"><button type="button" class="btn btn-default btn-xs choose_employee" data-toggle="modal" data-target="#add_management">添加</button><button type="button" class="btn btn-default btn-xs see_employee" data-toggle="modal" data-target="#see_management">查看人员</button></div></td></tr>',
	employeeListTemp :'<tr><td class="check_item_e"><input type="checkbox" class="icheck" value="$id" placeholder=""></td><td>$num</td><td class="name">$name</td><td>$gender</td><td>$department</td><td>$position</td><td>$mobile</td><td>$company</td></tr>',
	employeeListTemp2:'<tr data-id="$id"><td>$num</td><td class="name">$name</td><td>$gender</td><td>$department</td><td>$position</td><td>$mobile</td><td>$company</td><td><button type="button" class="btn btn-default btn-xs delete_employee" data-toggle="modal" data-target="#delete_management">删除</button></td></tr>',
};
$(function(){
	ScriptObject.bindEvent();
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 发布
	// 单个会议删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parents('ul.fun_btn').attr('data-id');
		$('#delete_meeting').find('input[name=id]').val(id);
	});
	// 批量删除会议
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_meeting').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(str != ''){
			$('#batch_delete_meeting').modal('show')
		}else{
			ThisObject.object.toast.toast('请选择会议！');
		}
		$('#batch_delete_meeting').find('input[name=id]').val(newStr);
	});
	// 全选checkbox
	$('.all_check_h').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item_h').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item_h').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 选择消息模板做提交获取数据
	$('.mes_btn').on('click', function(){
		var mid = $(this).parents('ul.fun_btn').attr('data-id');
		$('#choose_message').find('input[name=id]').val(mid);
		Common.ajax({
			data    :{requestType:'get_message_temp', id:mid},
			callback:function(data){
				console.log(data);
				ThisObject.object.signMessageSelect.setValue(0);
				ThisObject.object.signMessageSelect.setHtml('');
				ThisObject.object.antiSignMessageSelect.setValue(0);
				ThisObject.object.antiSignMessageSelect.setHtml('');
				ThisObject.object.receivablesMessageSelect.setValue(0);
				ThisObject.object.receivablesMessageSelect.setHtml('');
				for(var i = 0; i<data.length; i++){
					switch(parseInt(data[i]['assign_type'])){
						case 1:
							ThisObject.object.signMessageSelect.setValue(data[i]['message_id']);
							ThisObject.object.signMessageSelect.setHtml(data[i]['name']);
							break;
						case 2:
							ThisObject.object.antiSignMessageSelect.setValue(data[i]['message_id']);
							ThisObject.object.antiSignMessageSelect.setHtml(data[i]['name']);
							break;
						case 3:
							ThisObject.object.receivablesMessageSelect.setValue(data[i]['message_id']);
							ThisObject.object.receivablesMessageSelect.setHtml(data[i]['name']);
							break;
					}
				}
			}
		});
	});
	// 预览
	$('.mes_preview_btn').on('click', function(){
		var id = $(this).parents('.form-group').find('input[type=hidden]').val();
		Common.ajax({
			data    :{requestType:'get_message', id:id},
			callback:function(data){
				console.log(data);
				$('.mes_preview').show().text(data.data.context);
			}
		});
	});
	// 打开酒店modal
	$('.hotel_btn').on('click', function(){
		var mid = $(this).parents('.fun_btn').attr('data-id');
		$('#choose_hotel').find('input[name=mid]').val(mid);
	});
	// 酒店保存
	$('.btn_save_hotel').on('click', function(){
		var mid = $('#choose_hotel').find('input[name=mid]').val();
		var arr = [];
		$('.check_item_h').find('.icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			arr.push(id);
		});
		ThisObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'choose_hotel', mid:mid, id:arr},
			callback:function(r){
				ThisObject.object.loading.complete();
				console.log(r);
				$('#choose_hotel').modal('hide');
				ThisObject.object.toast.toast('酒店选择成功！');
				location.replace(document.referrer);
			}
		})
	});
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
	$('.management_btn').on('click', function(){
		var mid = $(this).parents('ul.fun_btn').attr('data-id');
		$('#choose_management').find('input[name=id]').val(mid);
		$('#role_list_modal').find('input[name=mid]').val(mid);
		$('#add_management').find('input[name=mid]').val(mid);
		var str = '', str2 = '';
		ThisObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_role', id:mid},
			callback:function(data){
				ThisObject.object.loading.complete();
				console.log(data);
				$.each(data, function(index, value){
					str += ScriptObject.roleListTemp.replace('$i', index+1).replace('$name', value.name)
						.replace('$level', value.level).replace('$comment', value.comment)
						.replace('$id', value.id)
				});
				$('#role_list').html(str);
				// 添加人
				$('#role_list_modal .choose_employee').on('click', function(){
					var id = $(this).parent('.btn-group').attr('data-id');
					$('#add_management').find('input[name=rid]').val(id);
					var str = '', i = 0;
					ThisObject.object.loading.loading();
					Common.ajax({
						data    :{requestType:'get_employee', id:id, mid:mid},
						callback:function(data){
							ThisObject.object.loading.complete();
							console.log(data);
							$.each(data, function(index, value){
								if(value.gender == 0){
									var gender = '未知';
								}else if(value.gender == 1){
									var gender = '男';
								}else if(value.gender == 2){
									var gender = '女';
								}
								str += ScriptObject.employeeListTemp.replace('$num', index+1)
									.replace('$name', value.name)
									.replace('$id', value.id).replace('$position', value.position)
									.replace('$mobile', value.mobile).replace('$company', value.company)
									.replace('$gender', gender).replace('$department', value.d_name);
								i++;
							});
							$('#add_management').find('.current_attendee').text(i);
							$('#employee_body').html(str);
							$('.icheck').iCheck({
								checkboxClass:'icheckbox_square-green',
								radioClass   :'iradio_square-green'
							});
							all_check_e();
						}
					});
				})
				// 查看角色已添加员工
				$('.see_employee').on('click', function(){
					var rid = $(this).parent('.btn-group').attr('data-id');
					ThisObject.object.loading.loading();
					get_employee_list(mid, rid);
				});
			}
		});
	});
	// 搜索员工
	$('#add_management').find('.main_search').on('click', function(){
		var keyword = $(this).parents('.repertory_text').find('input[name=keyword]').val();
		var mid     = $('#add_management').find('input[name=mid]').val();
		ThisObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_employee2', keyword:keyword, mid:mid},
			callback:function(data){
				ThisObject.object.loading.complete();
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
					str += ScriptObject.employeeListTemp.replace('$num', index+1)
						.replace('$name', value.name)
						.replace('$id', value.id).replace('$position', value.position)
						.replace('$mobile', value.mobile).replace('$company', value.company)
						.replace('$gender', gender).replace('$department', value.d_name);
					i++;
				});
				$('#add_management').find('.current_attendee').text(i);
				$('#employee_body').html(str);
				$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				});
				all_check_e();
			}
		});
	});
	// 确认添加
	$('#add_management').find('.btn_save').on('click', function(){
		var arr = [];
		var rid = $('#add_management').find('input[name=rid]').val();
		var mid = $('#add_management').find('input[name=mid]').val();
		$('.check_item_e').find('.icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			arr.push(id);
		});
		ThisObject.object.loading.loading();
		if(arr != ''){
			Common.ajax({
				data    :{requestType:'save_employee', id:arr, rid:rid, mid:mid},
				callback:function(data){
					ThisObject.object.loading.complete();
					console.log(data);
					if(data.status == 1){
						$('#add_management').modal('hide');
						ThisObject.object.toast.toast('添加成功！');
					}else if(data.status == 0){
						$('#add_management').modal('hide');
						ThisObject.object.toast.toast('添加成功！');
					}
				}, error:function(){
					/*ThisObject.object.toast.toast('添加失败');*/
				}
			});
		}else{
			ThisObject.object.loading.complete();
			ThisObject.object.toast.toast('添加失败，未选择员工！！');
		}
	});
	// 点击过滤标签-全部
	$('#filter_btn_all').on('click', function(){
		var new_url = ThisObject.url.thisPage;
		new_url     = url_object.delUrlParam('type', new_url);
		location.replace(new_url);
	});
	// 点击过滤标签-进行中
	$('#filter_btn_ing').on('click', function(){
		var new_url = ThisObject.url.thisPage;
		new_url     = url_object.setUrlParam('type', 'ing', new_url);
		location.replace(new_url);
	});
	// 点击过滤标签-已结束
	$('#filter_btn_fin').on('click', function(){
		var new_url = ThisObject.url.thisPage;
		new_url     = url_object.setUrlParam('type', 'fin', new_url);
		location.replace(new_url);
	});
});
function get_employee_list(mid, rid){
	Common.ajax({
		data    :{requestType:'see_employee', mid:mid, rid:rid},
		callback:function(data){
			ThisObject.object.loading.complete();
			var str = '';
			if(data != ''){
				$.each(data, function(index, value){
					if(value.gender == 0){
						var gender = '未知';
					}else if(value.gender == 1){
						var gender = '男';
					}else if(value.gender == 2){
						var gender = '女';
					}
					str += ScriptObject.employeeListTemp2.replace('$num', index+1)
						.replace('$name', value.name)
						.replace('$id', value.id).replace('$position', value.position)
						.replace('$mobile', value.mobile).replace('$company', value.company)
						.replace('$gender', gender).replace('$department', value.d_name);
				});
				$('#employee_body1').html(str);
				$('.delete_employee').on('click', function(){
					var id = $(this).parents('tr').attr('data-id');
					ThisObject.object.loading.loading();
					Common.ajax({
						data    :{requestType:'delete_employee', id:id, mid:mid, rid:rid},
						callback:function(data){
							ThisObject.object.loading.complete();
							console.log(data);
							if(data.status){
								ThisObject.object.toast.toast('删除成功！');
								get_employee_list(mid, rid);
							}
						}
					})
				});
				$('.no_choice').hide();
			}else{
				$('#employee_body1').empty();
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
		$('#add_management').find('.selected_attendee').text(n);
	});
	$('.all_check_e').find('.iCheck-helper').on('click', function(){
		var i = 0;
		$('.check_item_e').find('.icheckbox_square-green.checked').each(function(){
			i++;
		});
		$('#add_management').find('.selected_attendee').text(i);
	})
}

