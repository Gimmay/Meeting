/**
 * Created by qyqy on 2016-10-25.
 */

var ScriptObject = {
	roomDetailsTemp  :'<tr data-id="$id">'+
	'	<td>$n</td>'+
	'	<td>$name</td>'+
	'	<td>'+
	'	<div class="btn-group">'+
	'	<button type="button" class="btn btn-default btn-sm leave_btn" data-toggle="modal" data-target="#choose_leave_time">退房</button>'+
	'	<button type="button" class="btn btn-default btn-sm change_btn">换房</button>'+
	'	</div></td></tr>',
	roomDetailsTemp2 :'<tr data-id="$id">'+
	'	<td>$n</td>'+
	'	<td>$name</td>'+
	'	<td>'+
	'	<div class="btn-group">'+
	'	<button type="button" class="btn btn-default btn-sm leave_btn" disabled>已退房</button>'+
	'	</div></td></tr>',
	roomDetailsTemp3 :'<tr data-id="$id">'+
	'	<td>$n</td>'+
	'	<td>$name</td>'+
	'	<td>'+
	'	<div class="btn-group">'+
	'	<button type="button" class="btn btn-default btn-sm leave_btn" disabled>已换房</button>'+
	'	</div></td></tr>',
	changeFulltemp   :'<tr data-id="$rid"><td>$i</td><td>$code</td><td>$situation</td><td><div class="client_list">$clientSpan<span class="c_change">交换</span></div></td></tr>',
	changeEmptytemp  :'<tr data-id="$rid"><td>$i</td><td>$code</td><td>$situation</td><td><div class="client_list"><span class="c_add">添加</span></div></td></tr>',
	changeNotFulltemp:'	<tr data-id="$rid"><td>$i</td><td>$code</td><td>$situation</td><td><div class="client_list">$clientSpan<span class="c_add">添加</span><span class="c_change">交换</span></div></td></tr>',
	clientBtnTemp    :'<span class="cspan" data-id="$cid">$name</span>',
	bindEvent        :function(){
		$('#change_room .client_list .cspan').on('click', function(){
			$('#change_room .client_list .cspan').removeClass('active');
			$(this).addClass('active');
		});
	},
	unbindEvent      :function(){
		$('#change_room .client_list .cspan').off('click');
	}
};
function change_room(){
	$('.change_btn').on('click', function(){
		var id   = $(this).parents('tr').attr('data-id'); // 需要换房人的ID
		var orid = $(this).parents('.right_details').find('#orid').val();
		Common.ajax({
			data    :{requestType:'change_room', id:id, rid:orid},
			callback:function(r){
				console.log(r);
				$('#change_room').modal('show');
				var str = '';
				$.each(r, function(index, value){
					var strBtn = ''
					/**
					 * -----------------------------房间交换-------------------------
					 *  1、 当房间可容纳人数等于入住人数时，显示住满，只有交换按钮显示，只提供与房间内其他人员交换房的功能。
					 *  2、 当房间可容纳人数大于入住人数时，显示未住满，显示交换按钮和添加按钮，提供交换和添加的功能。
					 *  3、 当入住人数=0时，显示未房间为空，只有添加按钮显示，提供添加的功能。
					 */
					if(Number(value.capacity) === Number(value.client.length)){
						$.each(value.client, function(index1, value1){
							strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
												  .replace('$cid', value1.jid);
						});
						str += ScriptObject.changeFulltemp.replace('$i', index+1).replace('$code', value.code)
										   .replace('$situation', '住满').replace('$clientSpan', strBtn)
										   .replace('$rid', value.id);
					}else if(Number(value.capacity)>Number(value.client.length)>0){
						$.each(value.client, function(index1, value1){
							console.log(value1);
							strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
												  .replace('$cid', value1.jid);
						});
						str += ScriptObject.changeNotFulltemp.replace('$i', index+1).replace('$code', value.code)
										   .replace('$situation', '未住满').replace('$clientSpan', strBtn)
										   .replace('$rid', value.id);
					}else if(Number(value.client.length) == 0){
						$.each(value.client, function(index1, value1){
							console.log(value1);
							strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
												  .replace('$cid', value1.jid);
						});
						str += ScriptObject.changeEmptytemp.replace('$i', index+1).replace('$code', value.code)
										   .replace('$situation', '未入住').replace('$clientSpan', strBtn)
										   .replace('$rid', value.id);
					}
				});
				$('#change_client_list').html(str);
				ScriptObject.unbindEvent();
				ScriptObject.bindEvent();
				/**
				 * rid : 提供可选的房间ID
				 * cid : 需要换房的客户ID
				 * orid: 需要换房客户的原先房间ID
				 */
				$('.c_add').on('click', function(){
					var rid = $(this).parents('tr').attr('data-id'); // 选择房间的ID
					Common.ajax({
						data    :{requestType:'room_add', rid:rid, cid:id, orid:orid},
						callback:function(r){
							console.log(r);
							if(r.status){
								ThisObject.object.toast.toast('换房成功');
								$('#change_room').modal('hide');
								location.reload();
							}else{
								ThisObject.object.toast.toast('换房失败');
							}
						}
					});
				});
				/**
				 * rid : 提供可选的房间ID
				 * cid : 需要换房的客户ID
				 * orid: 需要换房客户的原先房间ID
				 * ocid: 提供的房间中选中的客户ID。。换房
				 */
				$('.c_change').on('click', function(){
					var rid  = $(this).parents('tr').attr('data-id'); // 选择房间的ID
					var ocid = $(this).siblings('.active').attr('data-id'); // 选择房间被选择人员的ID
					ThisObject.object.loading.loading();
					Common.ajax({
						data    :{requestType:'room_change', rid:rid, ocid:ocid, cid:id, orid:orid},
						callback:function(r){
							ThisObject.object.loading.complete();
							if(r.status){
								ThisObject.object.toast.toast('换房成功');
								$('#change_room').modal('hide');
								$('.right_details').width(0);
								location.reload();
							}else{
								ThisObject.object.toast.toast('换房失败');
							}
						}
					})
				});
			}
		});
	});
}
$(function(){
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 单个删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_room ').find('input[name=id]').val(id);
	});
	// 批量删除
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_room').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_delete_room').modal('show')
		}else{
			ThisObject.object.toast.toast('请选择房间！');
		}
		$('#batch_delete_room').find('input[name=id]').val(newStr);
	});
	/*
	 * 添加入住人员
	 */
	$('#add_recipient .check_item_m').find('.iCheck-helper').on('click', function(){
		var n = 0;
		$('#add_recipient .check_item_m').find('.icheckbox_square-green').each(function(){
			n++;
		});
		$('#add_recipient .selected_attendee').text(n);
	});
	// 当前人数
	var m = 0;
	$('#add_recipient .check_item_m').each(function(){
		m++;
	});
	$('#add_recipient').find('.current_attendee').text(m);
	// 全选checkbox 选择入住人中全选
	$('#add_recipient .all_check_m').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('#add_recipient .check_item_m').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('#add_recipient .check_item_m').find('.icheckbox_square-green').removeClass('checked');
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
		$('#add_recipient .check_item_m .icheckbox_square-green.checked').each(function(){
			var id   = $(this).find('.icheck').val();
			var name = $(this).parents('tr').find('.name').text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		ThisObject.object.loading.loading();
		$('#distribution_room').find('.mr_17').removeClass('hide');
		$('#distribution_room').find('input[name=person]').val(str);
		$('#distribution_room').find('#selected_attendee_count_by_1').text(nameStr);
		ThisObject.object.loading.complete();
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
		var room_id  = $('#add_recipient2').find('input[name=room_id]').val();
		var n1       = 0;
		var str      = [], nameStr = [];
		$('#add_recipient2 .check_item_m2 .icheckbox_square-green.checked').each(function(){
			var id   = $(this).find('.icheck').val();
			var name = $(this).parents('tr').find('.name').text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		/*ThisObject.object.loading.loading();*/
		console.log(str);
		if(str.length>Number(can_live)){
			ThisObject.object.toast.toast('选择人数不能大于可入住人数！');
			return false;
		}else{
			Common.ajax({
				data    :{requestType:'choose_client_2', id:str, rid:room_id},
				callback:function(r){
					console.log(r);
					if(r.status){
						$('#add_recipient2').modal('hide');
						ThisObject.object.toast.toast('添加成功！');
						location.replace(document.referrer); //刷新
					}else{
						ThisObject.object.toast.toast('添加失败！');
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
		var id             = $(this).parent('.btn-group').attr('data-id');
		var room_code      = $(this).parents('tr').find('.room_code').text();
		var capacity       = $(this).parents('tr').find('.capacity').text();
		var room_type      = $(this).parents('tr').find('.room_type').text();
		var price          = $(this).parents('tr').find('.price').text();
		var client_type    = $(this).parents('tr').find('.client_type').text();
		var come_time      = $(this).parents('tr').find('.come_time').text();
		var comment        = $(this).parents('tr').find('.comment').text();
		var $right_details = $('.right_details');
		$right_details.find('.room_code').text(room_code);
		$right_details.find('.capacity').text(capacity);
		$right_details.find('.room_type').text(room_type);
		$right_details.find('.price').text(price);
		$right_details.find('.client_type').text(client_type);
		$right_details.find('.create_time').text(come_time);
		$right_details.find('#orid').val(id);
		Common.ajax({
			data    :{requestType:'details', id:id},
			callback:function(r){
				var str = '', i = 0;
				console.log(r);
				$.each(r, function(index, value){
					if(value.occupancy_status == 1){
						if(!value.leave_time){
							str += ScriptObject.roomDetailsTemp.replace('$n', index+1).replace('$name', value.name)
											   .replace('$id', value.id);
							i++;
						}else{
							str += ScriptObject.roomDetailsTemp2.replace('$n', index+1).replace('$name', value.name)
											   .replace('$id', value.id);
						}
					}else{
						str += ScriptObject.roomDetailsTemp3.replace('$n', index+1).replace('$name', value.name)
										   .replace('$id', value.id);
					}
				});
				$('#add_recipient2').find('input[name=can_live]').val(capacity-i);
				$right_details.find('.room_num').text(i);
				if(Number(i)>=Number(capacity)){
					$('.right_details').find('.add').hide();
				}else{
					$('.right_details').find('.add').show();
				}
				$('#list_c').html(str);
				leave_btn();
				change_room();
			}
		});
		$('.add').on('click', function(){
			var per = $('#add_recipient2').find('input[name=can_live]').val();
			$('#add_recipient2').modal('show');
			$('#add_recipient2').find('.can_live_p').text(per);
		});
		$('#add_recipient2').find('input[name=room_id]').val(id);
	});
	$('.alter_btn').on('click', function(){
		var id        = $(this).parent('.btn-group').attr('data-id');
		var room_type = $(this).parents('tr').find('.room_type').text();
		$('#alter_room ').find('input[name=id]').val(id);
		choose($(this));
	});
	//选择房间类型自动获取数据
	$('#distribution_room').find('#room_type').on('change', function(){
		var id = $(this).find('option:selected').val();
		Common.ajax({
			data    :{requestType:'get_type', id:id},
			callback:function(r){
				console.log(r);
				$('#distribution_room').find('#price').val(r.price);
				$('#distribution_room').find('#capacity').val(r.capacity);
			}
		});
	});
	//选择房间类型自动获取数据
	$('#alter_room').find('#room_type_a').on('change', function(){
		var id = $(this).find('option:selected').val();
		Common.ajax({
			data    :{requestType:'get_type', id:id},
			callback:function(r){
				console.log(r);
				$('#alter_room').find('#price_a').val(r.price);
				$('#alter_room').find('#capacity_a').val(r.capacity);
			}
		});
	});
});
function choose(e){
	var room_code   = e.parents('tr').find('.room_code').text();
	var room_type   = e.parents('tr').find('.room_type').text();
	var capacity    = e.parents('tr').find('.capacity').text();
	var price       = e.parents('tr').find('.price').text();
	var client_type = e.parents('tr').find('.client_type').text();
	var come_time   = e.parents('tr').find('.come_time').text();
	var comment     = e.parents('tr').find('.comment').text();
	var $alter_room = $('#alter_room');
	$alter_room.find('#room_code_a').val(room_code);
	$alter_room.find('#room_type_a').val(room_type);
	$alter_room.find('#client_type_a').val(client_type);
	$alter_room.find('#capacity_a').val(capacity);
	$alter_room.find('#price_a').val(price);
	$alter_room.find('#come_time_a').val(come_time);
	$alter_room.find('#room_code_a').val(room_code);
}
function leave_btn(){
	$('.leave_btn').on('click', function(){
		var id = $(this).parents('tr').attr('data-id');
		$('#choose_leave_time').find('input[name=id]').val(id);
	});
}
function checkAssign(){
	var capacity_v = $('#capacity').val();
	var number     = $('input[name=person]').val();
	var a          = number.split(",");
	var len        = a.length;
	if(capacity_v<len){
		ThisObject.object.toast.toast('选择人数不能大于可容纳人数！');
		return false;
	}
	return true;
}