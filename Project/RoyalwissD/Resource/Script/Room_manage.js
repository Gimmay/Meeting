/**
 * Created by qyqy on 2016-10-25.
 */

var ScriptObject = {
	roomDetailsTemp     :'<tr data-id="$id">\n'+
	'\t<td><span class="glyphicon glyphicon-ok color-28B778"></span></td>\n'+
	'\t<td>$name</td>\n'+
	'\t<td>\n\t'+
	'\t<div class="btn-group">\n\t\t'+
	'\t<button type="button" class="btn btn-default btn-sm leave_btn" data-toggle="modal" data-target="#choose_leave_time">退房</button>\n\t\t'+
	'\t<button type="button" class="btn btn-default btn-sm change_btn">换房</button>\n\t'+
	'\t</div>\n\t</td>\n</tr>',
	roomDetailsTemp2    :'<tr data-id="$id">\n'+
	'\t<td><span class="glyphicon glyphicon-remove color-danger"></span></td>\n'+
	'\t<td>$name</td>\n'+
	'\t<td>\n\t'+
	'\t<div class="btn-group">\n\t\t'+
	'\t<button type="button" class="btn btn-default btn-sm leave_btn" disabled>已退房</button>\n\t'+
	'\t</div>\n\t</td>\n</tr>',
	roomDetailsTemp3    :'<tr data-id="$id">\n'+
	'\t<td><span class="glyphicon glyphicon-remove color-danger"></span></td>\n'+
	'\t<td>$name</td>\n'+
	'\t<td>\n\t'+
	'\t<div class="btn-group">\n\t\t'+
	'\t<button type="button" class="btn btn-default btn-sm leave_btn" disabled>已换房</button>\n\t'+
	'\t</div>\n\t</td>\n</tr>',
	changeFulltemp      :'<tr data-id="$rid">\n\t<td>$i</td>\n\t<td>$name</td>\n\t<td>$situation</td>\n\t<td>\n\t\t<div class="client_list">$clientSpan</div>\n\t</td>\n</tr>',
	changeEmptytemp     :'<tr data-id="$rid">\n\t<td>$i</td>\n\t<td>$name</td>\n\t<td>$situation</td>\n\t<td>\n\t\t<div class="client_list"><span class="c_add">添加</span></div>\n\t</td>\n</tr>',
	changeNotFulltemp   :'<tr data-id="$rid">\n\t<td>$i</td>\n\t<td>$name</td>\n\t<td>$situation</td>\n\t<td>\n\t\t<div class="client_list">$clientSpan<span class="c_add">添加</span></div>\n\t</td>\n</tr>',
	clientBtnTemp       :'<span class="cspan" data-id="$cid">$name</span>',
	roomTypeTemp        :'<option value="$id">$name</option>',
	roomTypeSelectedTemp:'<option value="$id" selected>$name</option>',
	createAddClient     :'<tr data-id="$id">\n\t<td>$i</td>\n\t<td>$unit</td>\n\t<td class="name">$name</td>\n\t<td>$clientType</td>\n\t<td>$gender</td>\n\t<td>$position</td>\n\t<td>$mobile</td>\n</tr>',
	createAddClient2    :'<tr>\n\t<td class="check_item_add2">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$i</td>\n\t<td>$unit</td>\n\t<td class="name">$name</td>\n\t<td>$clientType</td>\n\t<td>$gender</td>\n\t<td>$position</td>\n\t<td>$mobile</td>\n</tr>',
	hasSelectedA        :'<a href=\"javascript:void(0)\" data-id="$id">$name</a>',
	status              :0,
	pastNumber          :0,
	clientArr           :[],
	bindEvent           :function(){
		$('#change_room .client_list .cspan').on('click', function(){
			var cid = $(this).attr('data-id');
			var rid = $(this).parents('tr').attr('data-id');
			$('#change_room .client_list .cspan').removeClass('active');
			$(this).addClass('active');
			$('.c_change').attr('data-id', cid);
			$('.c_change').attr('data-rid', rid);
		});
	},
	unbindEvent         :function(){
		$('#change_room .client_list .cspan').off('click');
	}
};
/**
 *
 * @param id  需要换房人的ID
 * @param orid  外面该房间的ID
 * @param keyword  关键字
 */
function change_room(current_cid, current_room_id, keyword){
	Common.ajax({
		data    :{requestType:'get_room_list', id:current_cid, rid:current_room_id, keyword:keyword},
		callback:function(r){
			$('#change_room').modal('show');
			var str = '';
			console.log(r);
			$.each(r, function(index, value){
				console.log(value);
				var strBtn = '';
				/**
				 * -----------------------------房间交换-------------------------
				 *  1、 当房间可容纳人数等于入住人数时，显示住满，只有交换按钮显示，只提供与房间内其他人员交换房的功能。
				 *  2、 当房间可容纳人数大于入住人数时，显示未住满，显示交换按钮和添加按钮，提供交换和添加的功能。
				 *  3、 当入住人数=0时，显示未房间为空，只有添加按钮显示，提供添加的功能。
				 */
				if(Number(value.capacity) === Number(value.client_list.length)){
					$.each(value.client_list, function(index1, value1){
						strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
											  .replace('$cid', value1.id);
					});
					str += ScriptObject.changeFulltemp.replace('$i', index+1).replace('$name', value.name)
									   .replace('$situation', '住满').replace('$clientSpan', strBtn)
									   .replace('$rid', value.id);
				}else if(Number(value.capacity)>Number(value.client_list.length) && Number(value.client_list.length)>0){
					$.each(value.client_list, function(index1, value1){
						strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
											  .replace('$cid', value1.id);
					});
					str += ScriptObject.changeNotFulltemp.replace('$i', index+1).replace('$name', value.name)
									   .replace('$situation', '未住满').replace('$clientSpan', strBtn)
									   .replace('$rid', value.id);
				}else if(value.client_list.length == 0){
					$.each(value.client_list, function(index1, value1){
						strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
											  .replace('$cid', value1.id);
					});
					str += ScriptObject.changeEmptytemp.replace('$i', index+1).replace('$name', value.name)
									   .replace('$situation', '未入住').replace('$clientSpan', strBtn)
									   .replace('$rid', value.id);
				}
			});
			$('#change_client_list').html(str);
			$('.icheck_r').iCheck({
				checkboxClass:'icheckbox_square-blue',
				radioClass   :'iradio_square-blue'
			});
			$('.all_check_r').find('.iCheck-helper').on('click', function(){
				if($(this).parent('.icheckbox_square-blue').hasClass('checked')){
					$('.check_item_r').find('.icheckbox_square-blue').addClass('checked');
				}else{
					$('.check_item_r').find('.icheckbox_square-blue').removeClass('checked');
				}
			});
			ScriptObject.unbindEvent();
			ScriptObject.bindEvent();
			/**
			 * rid : 提供可选的房间ID
			 * cid : 需要换房的客户ID
			 * current_room_id: 需要换房客户的原先房间ID
			 */
			$('.c_add').on('click', function(){
				var rid = $(this).parents('tr').attr('data-id'); // 选择房间的ID
				ManageObject.object.loading.loading();
				Common.ajax({
					data    :{
						requestType:'change_room',
						rid_b      :rid,
						cid_a      :current_cid,
						rid_a      :current_room_id
					},
					callback:function(r){
						ManageObject.object.loading.complete();
						if(r.status){
							ManageObject.object.toast.toast(r.message, '1');
							ManageObject.object.toast.onQuasarHidden(function(){
								$('#change_room').modal('hide');
								location.reload();
							});
						}else{
							ManageObject.object.toast.toast(r.message, '2');
						}
					}
				});
			});
			/**
			 * rid : 提供可选的房间ID
			 * current_cid : 需要换房的客户ID
			 * current_room_id: 需要换房客户的原先房间ID
			 * ocid: 提供的房间中选中的客户ID。。换房
			 */
			$('.c_change').on('click', function(){
				console.log($(this).attr('data-id'));
				if($(this).attr('data-id') != undefined){
					var rid  = $(this).attr('data-rid'); // 选择房间的ID
					var ocid = $(this).attr('data-id'); // 选择房间被选择人员的ID
					console.log(ocid);
					console.log(rid);
					if(ocid == ''){
						ManageObject.object.toast.toast('请选择需要换房的参会人员！');
					}else{
						ManageObject.object.loading.loading();
						Common.ajax({
							data    :{
								requestType:'change_room',
								rid_b      :rid,
								cid_b      :ocid,
								cid_a      :current_cid,
								rid_a      :current_room_id
							},
							callback:function(r){
								console.log(r);
								ManageObject.object.loading.complete();
								if(r.status){
									ManageObject.object.toast.toast(r.message, 1);
									ManageObject.object.toast.onQuasarHidden(function(){
										$('#change_room').modal('hide');
										$('.right_details').width(0);
										location.reload();
									});
								}else{
									ManageObject.object.toast.toast(r.message, 2);
								}
							}
						})
					}
				}else{
					ManageObject.object.toast.toast('请选择交换房客！');
				}
			});
		}
	});
}
$(function(){
	// 创建房间
	$('#distribution_room .btn-save').on('click', function(){
		if(checkAssign()){
			var data = $('#distribution_room form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			})
		}
	});
	/**
	 * 创建房间的操作
	 * 添加入住人员  参会人员按钮
	 */
	$('#distribution_room').find('.create_add_client').on('click', function(){
		getClientAdd(''); // 获取所有的客户列表
	});
	$('#add_recipient').find('.main_search').on('click', function(){
		var keyword = $('#add_recipient').find('input[name=keyword]').val();
		getClientAdd(keyword); // 带关键字的获取客户列表
	});
	$('#add_recipient .search_all').on('click', function(){
		getClientAdd(''); // 获取所有的客户列表
	});
	/**
	 * 房间详情的操作
	 * 添加入住人员  参会人员按钮
	 */
	$('.right_details').find('.add_client').on('click', function(){
		getClientAddDetails(''); // 获取所有的客户列表
	});
	$('#add_recipient2').find('.main_search').on('click', function(){
		var keyword = $('#add_recipient2').find('input[name=keyword]').val();
		getClientAddDetails(keyword); // 带关键字的获取客户列表
	});
	$('#add_recipient2 .search_all').on('click', function(){
		getClientAddDetails(''); // 获取所有的客户列表
	});
	// 多选框点击事件
	$('#add_recipient .check_item_m').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$(this).parent('.icheckbox_square-green').addClass('checked');
		}else{
			$(this).parent('.icheckbox_square-green').removeClass('checked');
		}
		// 选中的人员
		var str = '', i = 0;
		$('#add_recipient .check_item_m  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#add_recipient').find('.selected_attendee').text(i);
	});
	// 保存入住人
	$('#add_recipient .btn_save').on('click', function(){
		var n1  = 0;
		var str = [], nameStr = [];
		$('#add_recipient .selected_person a').each(function(){
			var id   = $(this).attr('data-id');
			var name = $(this).text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		ManageObject.object.loading.loading();
		$('#distribution_room').find('.mr_17').removeClass('hide');
		$('#distribution_room').find('input[name=client]').val(str); // 选择入住的客户
		$('#distribution_room').find('#selected_attendee_count_by_1').text(nameStr);
		ManageObject.object.loading.complete();
		$('#add_recipient').modal('hide')
	});
	/*
	 *  修改/修改入住人员
	 */
	$('#add_recipient2 .check_item_m').find('.iCheck-helper').on('click', function(){
		var n = 0;
		$('#add_recipient .check_item_m').find('.icheckbox_square-green').each(function(){
			n++;
		});
		$('#add_recipient .selected_attendee').text(n);
	});
	// 当前人数
	var m = 0;
	$('#add_recipient2 .check_item_m2').each(function(){
		m++;
	});
	$('#add_recipient2').find('.current_attendee').text(m);
	// 全选checkbox 选择入住人中全选
	$('#add_recipient2 .all_check_m2').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('#add_recipient2 .check_item_m2').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('#add_recipient2 .check_item_m2').find('.icheckbox_square-green').removeClass('checked');
		}
		// 选中的人员
		var str = '', i = 0;
		$('#add_recipient2 .check_item_m2  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#add_recipient2').find('.selected_attendee').text(i);
	});
	// 多选框点击事件
	$('#add_recipient2 .check_item_m2').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$(this).parent('.icheckbox_square-green').addClass('checked');
		}else{
			$(this).parent('.icheckbox_square-green').removeClass('checked');
		}
		// 选中的人员
		var str = '', i = 0;
		$('#alter_recipient .check_item_m  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#alter_recipient').find('.selected_attendee').text(i);
	});
	// 保存入住人
	$('#add_recipient2 .btn_save').on('click', function(){
		var can_live = $('#add_recipient2').find('.can_live_p').text();
		var rid      = $('#add_recipient2').find('input[name=room_id]').val();
		var n1       = 0;
		var str      = [], nameStr = [];
		$('#add_recipient2 .check_item_add2 .icheckbox_square-green.checked').each(function(){
			var id   = $(this).find('.icheck').val();
			var name = $(this).parents('tr').find('.name').text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		if(can_live != 0){
			if(str.length>Number(can_live)){
				ManageObject.object.toast.toast('选择人数不能大于可入住人数！');
				return false;
			}else{
				ManageObject.object.loading.loading();
				Common.ajax({
					data    :{requestType:'check_in', cid:str, rid:rid},
					callback:function(r){
						ManageObject.object.loading.complete();
						if(r.status){
							ManageObject.object.toast.toast(r.message, '1');
							ManageObject.object.toast.onQuasarHidden(function(){
								$('#add_recipient2').modal('hide');
								location.reload(); //刷新
							});
						}else{
							ManageObject.object.toast.toast(r.message, '2');
						}
					}
				});
			}
		}else{
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'check_in', cid:str, rid:rid},
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							$('#add_recipient2').modal('hide');
							location.reload(); //刷新
						});
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			});
		}
	});
	/*
	 *  右侧详情
	 */
	$('.details_btn').on('click', function(){
		/*$('.right_details').animate({width:'480px'})*/
		var current_room_id = $(this).parent('.btn-group').attr('data-id');
		var $right_details  = $('.right_details');
		$right_details.find('#current_room_id').val(current_room_id);
		Common.ajax({
			data    :{requestType:'get_detail', id:current_room_id},
			callback:function(r){
				$right_details.find('.room_code').text(r.name);
				$right_details.find('.capacity').text(r.capacity);
				$right_details.find('.room_type').text(r.type);
				$right_details.find('.price').text(r.price);
				$right_details.find('.create_time').text(r.creatime);
				$right_details.find('.comment').text(r.comment);
				var str = '', i = 0, ii = 0,
					strHistory          = '';
				console.log(r);
				// 房客记录
				$.each(r.history, function(index, value){
					console.log(value.client);
					switch(parseInt(value.process_status)){
						case 0:
							strHistory += ScriptObject.roomDetailsTemp2
													  .replace('$name', value.client).replace('$id', value.cid);
							ii++;
							break;
						case 1:
							str += ScriptObject.roomDetailsTemp
											   .replace('$name', value.client).replace('$id', value.cid);
							i++;
							break;
						case 2:
							strHistory += ScriptObject.roomDetailsTemp3
													  .replace('$name', value.client).replace('$id', value.cid);
							ii++;
							break;
						default:
							break;
					}
				});
				if(Number(r.capacity) != 0){
					$('#add_recipient2').find('input[name=can_live]').val(r.capacity-i);
					if(Number(i)>=Number(r.capacity)){
						$('.right_details').find('.add_client').hide();
					}else{
						$('.right_details').find('.add_client').show();
					}
				}else{
					$right_details.find('.capacity').text('无人员限制'); //无人员限制
					$('#add_recipient2').find('input[name=can_live]').val(0);
					$('#add_recipient2').find('.modal-title span').hide();
					$('.right_details').find('.add_client').show();
				}
				$right_details.find('.room_num').text(i);
				$('#list_c').html(str);
				$('#list_history').html(strHistory);
				// 退房按钮
				leave_btn(current_room_id); //参数：当前房间id
				// 换房按钮
				$('.change_btn').on('click', function(){
					var current_cid = $(this).parents('tr').attr('data-id'); // 需要换房人的ID
					$('#change_room').find('input[name=id]').val(current_cid);
					$('#change_room').find('input[name=orid]').val(current_room_id);
					change_room(current_cid, current_room_id, '');
				});
			}
		});
		$('.add_client').on('click', function(){
			var per = $('#add_recipient2').find('input[name=can_live]').val();
			$('#add_recipient2').modal('show');
			$('#add_recipient2').find('.can_live_p').text(per);
		});
		$('#add_recipient2').find('input[name=room_id]').val(current_room_id);
	});
	// 点击创建房间 获取房间类型及其消息
	$('.create_room').on('click', function(){
		ScriptObject.clientArr = [];
		$('#selected_attendee_count_by_1').html();
		Common.ajax({
			data    :{requestType:'get_room_type_list'},
			callback:function(r){
				var str = '<option value="0">请选择房间类型</option>';
				$.each(r, function(index, value){
					str += ScriptObject.roomTypeTemp.replace('$id', value.value).replace('$name', value.html);
				});
				$('#distribution_room').find('#room_type').html(str);
			}
		});
	});
	// 修改房间内容
	$('.alter_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id'); //房间ID
		$('#alter_modal ').find('input[name=id]').val(id);
		var $this_modal = $('#alter_modal');
		Common.ajax({
			data    :{requestType:'get_room', id:id},
			callback:function(r){
				var str = '<option value="0">请选择房间类型</option>';
				$.each(r.room_type_list, function(index, value){
					str += ScriptObject.roomTypeTemp.replace('$id', value.value).replace('$name', value.html);
				});
				$this_modal.find('#room_type_a').html(str);
				$this_modal.find('#room_code_a').val(r.name);
				$this_modal.find('#room_type_a').val(r.type_code);
				$this_modal.find('#price_a').val(r.price);
				$this_modal.find('#capacity_a').val(r.capacity);
				$this_modal.find('#room_comment_a').val(r.comment);
			}
		});
	});
	//选择房间类型自动获取数据
	$('#distribution_room').find('#room_type').on('change', function(){
		var $this_modal = $('#distribution_room');
		var id          = $(this).find('option:selected').val();
		Common.ajax({
			data    :{requestType:'get_room_type_detail', id:id},
			callback:function(r){
				console.log(r);
				$this_modal.find('#price').val(r.price);
				$this_modal.find('#capacity').val(r.capacity);
			}
		});
	});
	//选择房间类型自动获取数据
	$('#alter_modal').find('#room_type_a').on('change', function(){
		var $this_modal = $('#alter_modal');
		var id          = $(this).find('option:selected').val();
		Common.ajax({
			data    :{requestType:'get_room_type_detail', id:id},
			callback:function(r){
				console.log(r);
				$this_modal.find('#price_a').val(r.price);
				$this_modal.find('#capacity_a').val(r.capacity);
			}
		});
	});
	// 修改房间保存
	$('#alter_modal .btn-save').on('click', function(){
		var data = $('#alter_modal form').serialize();
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
		});
	});
	//换房搜索
	$('#change_room .ajax_search').on('click', function(){
		var keyword = $('#change_room').find('input[name=keyword]').val();
		var id      = $('#change_room').find('input[name=id]').val();
		var orid    = $('#change_room').find('input[name=orid]').val();
		change_room(id, orid, keyword);
	});
	//换房查看全部
	$('#change_room .ajax_search_all').on('click', function(){
		var id   = $('#change_room').find('input[name=id]').val();
		var orid = $('#change_room').find('input[name=orid]').val();
		change_room(id, orid, '');
	});
	// 退房操作
	$('#choose_leave_time .btn-save').on('click', function(){
		var data = $('#choose_leave_time form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					});
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		})
	});
});
function leave_btn(rid){
	$('.leave_btn').on('click', function(){
		var cid  = $(this).parents('tr').attr('data-id');
		var date = new Date();
		time     = date.format('yyyy-MM-dd HH:mm:ss ');
		$('#choose_leave_time').find('input[name=cid]').val(cid);
		$('#choose_leave_time').find('#leave_time').val(time);
		$('#choose_leave_time').find('input[name=rid]').val(rid);
	});
}
function checkAssign(){
	var capacity_val = $('#capacity').val();
	var number       = $('input[name=client]').val();
	if(number != ''){
		var number = number.split(",");
		var len    = number.length;
		if(capacity_val<len){
			ManageObject.object.toast.toast('选择人数不能大于可容纳人数！');
			return false;
		}
	}
	var $room_type = $('#room_type');
	if($room_type.val() == '0'){
		ManageObject.object.toast.toast('请选择房间类型！');
		return false;
	}
	return true;
}
function checkStatus(){
	var val = $('#alter_room').find('#room_type_a option:selected').val();
	if(ScriptObject.status == 1){
		if(ScriptObject.pastNumber != val){
			ManageObject.object.toast.toast('该房间已入住人员，不能修改房间类型！');
			return false;
		}
	}
	return true;
}
function getClientAdd(keyword){
	$('#add_recipient .selected_attendee').text(0);
	var str = '';
	Common.ajax({
		data    :{requestType:'get_client', keyword:keyword},
		callback:function(r){
			console.log(r);
			$.each(r, function(index, value){
				var gender = '';
				if(value.gender == 0){
					gender = '未知';
				}else if(value.gender == 1){
					gender = '男';
				}else if(value.gender == 2){
					gender = '女';
				}
				str += ScriptObject.createAddClient.replace('$id', value.cid).replace('$i', index+1)
								   .replace('$name', value.name)
								   .replace('$gender', gender).replace('$position', value.position)
								   .replace('$mobile', value.mobile).replace('$unit', value.unit)
								   .replace('$clientType', value.type);
			});
			$('#add_recipient').find('#attendee_body').html(str);
			var htm = '';
			if(ScriptObject.clientArr != ''){
				$.each(ScriptObject.clientArr, function(index, value){
					htm += ScriptObject.hasSelectedA.replace('$id', value.id).replace('$name', value.name);
				});
			}
			$('.selected_person').html(htm);
			$('#attendee_body tr').on('click', function(){
				var id        = $(this).attr('data-id');
				var name      = $(this).find('.name').text();
				var clientArr = [];
				$('.selected_person a').each(function(){
					var sid = $(this).attr('data-id');
					clientArr.push(sid);
				});
				if(clientArr.indexOf(id) == -1){
					ScriptObject.clientArr.push({id:id, name:name});
					$('.selected_person').append('<a href="javascript:void(0)" data-id='+id+'>'+name+'</a>');
					console.log(ScriptObject.clientArr);
				}
				$('.selected_person a').off('click');
				$('.selected_person a').on('click', function(){
					var id = $(this).attr('data-id');
					console.log('true');
					console.log(ScriptObject.clientArr);
					$.each(ScriptObject.clientArr, function(index, value){
						console.log(value);
						console.log(value.id);
						if(id == value.id){
							ScriptObject.clientArr.splice(index, 1);
							console.log(ScriptObject.clientArr);
							return false;
						}
					});
					$(this).remove();
				});
			});
			$('.selected_person a').off('click');
			$('.selected_person a').on('click', function(){
				var id = $(this).attr('data-id');
				console.log('true');
				console.log(ScriptObject.clientArr);
				$.each(ScriptObject.clientArr, function(index, value){
					console.log(value);
					console.log(value.id);
					if(id == value.id){
						ScriptObject.clientArr.splice(index, 1);
						console.log(ScriptObject.clientArr);
						return false;
					}
				});
				$(this).remove();
			});
		}
	});
}
function getClientAddDetails(keyword){
	var str = '';
	Common.ajax({
		data    :{requestType:'get_client', keyword:keyword},
		callback:function(r){
			console.log(r);
			$.each(r, function(index, value){
				var gender = '';
				if(value.gender == 0){
					gender = '未知';
				}else if(value.gender == 1){
					gender = '男';
				}else if(value.gender == 2){
					gender = '女';
				}
				var newDate = new Date();
				newDate.setTime(value.sign_time*1000);
				if(value.sign_time != null){
					var signTime = newDate.format('yyyy-MM-dd H:m:s');
				}else{
					var signTime = '';
				}
				str += ScriptObject.createAddClient2.replace('$id', value.cid).replace('$i', index+1)
								   .replace('$name', value.name)
								   .replace('$gender', gender).replace('$position', value.position)
								   .replace('$mobile', value.mobile).replace('$unit', value.unit)
								   .replace('$clientType', value.type);
			});
			$('#add_recipient2').find('#attendee_body_a').html(str);
			$('.check_item_add2').iCheck({
				checkboxClass:'icheckbox_square-green',
				radioClass   :'iradio_square-green'
			});
			$('#add_recipient2 .check_item_add2').find('.iCheck-helper').on('click', function(){
				var n = 0;
				$('#add_recipient2 .check_item_add2').find('.icheckbox_square-green.checked').each(function(){
					n++;
				});
				$('#add_recipient2 .selected_attendee').text(n);
			});
			// 当前人数
			var m = 0;
			$('#add_recipient2 .check_item_add2').each(function(){
				m++;
			});
			$('#add_recipient2').find('.current_attendee').text(m);
			// 全选checkbox 选择入住人中全选
			$('#add_recipient2 .all_check_add2').find('.iCheck-helper').on('click', function(){
				if($(this).parent('.icheckbox_square-green').hasClass('checked')){
					$('#add_recipient2 .check_item_add2').find('.icheckbox_square-green').addClass('checked');
				}else{
					$('#add_recipient2 .check_item_add2').find('.icheckbox_square-green').removeClass('checked');
				}
				// 选中的人员
				var str = '', i = 0;
				$('#add_recipient2 .check_item_add2  .icheckbox_square-green.checked').each(function(){
					var id = $(this).find('.icheck').val();
					str += id+',';
					i++;
				});
				$('#add_recipient2').find('.selected_attendee').text(i);
			});
		}
	});
}