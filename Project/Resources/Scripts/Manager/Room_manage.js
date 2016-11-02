/**
 * Created by qyqy on 2016-10-25.
 */

var ScriptObject = {
 roomDetailsTemp:'<tr data-id="$id">' +
 '	<td>$n</td>' +
 '	<td>$name</td>' +
 '	<td>' +
 '	<div class="btn-group">' +
 '	<button type="button" class="btn btn-default btn-sm leave_btn" data-toggle="modal" data-target="#choose_leave_time">退房</button>' +
 '	<button type="button" class="btn btn-default btn-sm change_btn">换房</button>' +
 '	</div></td></tr>',
	roomDetailsTemp2:'<tr data-id="$id">' +
	'	<td>$n</td>' +
	'	<td>$name</td>' +
	'	<td>' +
	'	<div class="btn-group">' +
	'	<button type="button" class="btn btn-default btn-sm leave_btn" disabled>已退房</button>' +
	'	</div></td></tr>',
 };
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
			ThisObject.object.toast.toast('请选择！');
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
	*  修改入住人员
	 */
	$('#alter_recipient .check_item_m').find('.iCheck-helper').on('click', function(){
		var n = 0;
		$('#add_recipient .check_item_m').find('.icheckbox_square-green').each(function(){
			n++;
		});
		$('#add_recipient .selected_attendee').text(n);
	});
	// 当前人数
	var m = 0;
	$('#alter_recipient .check_item_m').each(function(){
		m++;
	});
	$('#alter_recipient').find('.current_attendee').text(m);
	// 全选checkbox 选择入住人中全选
	$('#alter_recipient .all_check_m').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('#alter_recipient .check_item_m').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('#alter_recipient .check_item_m').find('.icheckbox_square-green').removeClass('checked');
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
	// 多选框点击事件
	$('#alter_recipient .check_item_m').find('.iCheck-helper').on('click', function(){
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
	$('#alter_recipient .btn_save').on('click', function(){
		var n1  = 0;
		var str = [], nameStr = [];
		$('#alter_recipient .check_item_m .icheckbox_square-green.checked').each(function(){
			var id   = $(this).find('.icheck').val();
			var name = $(this).parents('tr').find('.name').text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		ThisObject.object.loading.loading();
		$('#alter_room').find('.mr_17').removeClass('hide');
		$('#alter_room').find('input[name=person]').val(str);
		$('#alter_room').find('#selected_attendee_count_by_alter').text(nameStr);
		ThisObject.object.loading.complete();
		$('#alter_recipient').modal('hide')
	});

	/*
	 *  右侧详情
	 */
	$('.close_btn').on('click',function(){
		$('.right_details').animate({width:'0'})
	});
	$('.details_btn').on('click',function(){
		$('.right_details').animate({width:'480px'})
		var id =$(this).parent('.btn-group').attr('data-id');
		var room_code = $(this).parents('tr').find('.room_code').text();
		var capacity = $(this).parents('tr').find('.capacity').text();
		var room_type = $(this).parents('tr').find('.room_type').text();
		var price = $(this).parents('tr').find('.price').text();
		var client_type = $(this).parents('tr').find('.client_type').text();
		var come_time = $(this).parents('tr').find('.come_time').text();
		var comment = $(this).parents('tr').find('.comment').text();
		var $right_details = $('.right_details');
		$right_details.find('.room_code').text(room_code);
		$right_details.find('.capacity').text(capacity);
		$right_details.find('.room_type').text(room_type);
		$right_details.find('.price').text(price);
		$right_details.find('.client_type').text(client_type);
		$right_details.find('.come_time').text(come_time);
		$right_details.find('.comment').text(comment);
		Common.ajax({
			data:{requestType:'details',id:id},
			callback:function(r){
				console.log(r);
				var str='',i=0;
				$.each(r,function(index,value){
					if(!value.leave_time){
						str+=ScriptObject.roomDetailsTemp.replace('$n',index+1).replace('$name',value.name).replace('$id',value.id);
						i++;
					}else{
						str+=ScriptObject.roomDetailsTemp2.replace('$n',index+1).replace('$name',value.name).replace('$id',value.id);
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
		$('.add').on('click',function(){
			var per = $('#add_recipient2').find('input[name=can_live]').val();
			$('#add_recipient2').modal('show');
			$('#add_recipient2').find('.can_live_p').text(per);
		});
	});
	$('.alter_btn').on('click',function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#alter_room ').find('input[name=id]').val(id);
		choose($(this));
	});
});
function choose(e){
	var room_code = e.parents('tr').find('.room_code').text();
	var room_type = e.parents('tr').find('.room_type').text();
	var capacity = e.parents('tr').find('.capacity').text();
	var price = e.parents('tr').find('.price').text();
	var client_type = e.parents('tr').find('.client_type').text();
	var come_time = e.parents('tr').find('.come_time').text();
	var comment = e.parents('tr').find('.comment').text();
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
	$('.leave_btn').on('click',function(){
		var id = $(this).parents('tr').attr('data-id');
		$('#choose_leave_time').find('input[name=id]').val(id);
	});
}

function change_room(){
	$('.change_btn').on('click',function(){
		var id = $(this).parents('tr').attr('data-id');
		Common.ajax({
			data:{requestType:'change_room',id:id},
			callback:function(r){
				console.log(r);
				$('#change_room').modal('show');
				var str='';
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
}

function checkAssign(){
	var capacity_v = $('#capacity').val();
	var number = $('input[name=person]').val();
	var a = number.split(",");
	var len = a.length;
	if(capacity_v < len){
		ThisObject.object.toast.toast('入住人数不能大于可容纳人数！');
		return false;
	}
	return true;
}