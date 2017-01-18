/**
 * Created by qyqy on 2016-10-25.
 */

var ScriptObject = {
	roomDetailsTemp     :'<tr data-id="$id">'+
	'	<td>$n</td>'+
	'	<td>$name</td>'+
	'	<td>'+
	'	<div class="btn-group">'+
	'	<button type="button" class="btn btn-default btn-sm leave_btn" data-toggle="modal" data-target="#choose_leave_time">退房</button>'+
	'	<button type="button" class="btn btn-default btn-sm change_btn">换房</button>'+
	'	</div></td></tr>',
	roomDetailsTemp2    :'<tr data-id="$id">'+
	'	<td>$n</td>'+
	'	<td>$name</td>'+
	'	<td>'+
	'	<div class="btn-group">'+
	'	<button type="button" class="btn btn-default btn-sm leave_btn" disabled>已退房</button>'+
	'	</div></td></tr>',
	roomDetailsTemp3    :'<tr data-id="$id">'+
	'	<td>$n</td>'+
	'	<td>$name</td>'+
	'	<td>'+
	'	<div class="btn-group">'+
	'	<button type="button" class="btn btn-default btn-sm leave_btn" disabled>已换房</button>'+
	'	</div></td></tr>',
	changeFulltemp      :'<tr data-id="$rid"><td>$i</td><td>$code</td><td>$situation</td><td><div class="client_list">$clientSpan<span class="c_change">交换</span></div></td></tr>',
	changeEmptytemp     :'<tr data-id="$rid"><td>$i</td><td>$code</td><td>$situation</td><td><div class="client_list"><span class="c_add">添加</span></div></td></tr>',
	changeNotFulltemp   :'<tr data-id="$rid"><td>$i</td><td>$code</td><td>$situation</td><td><div class="client_list">$clientSpan<span class="c_add">添加</span><span class="c_change">交换</span></div></td></tr>',
	clientBtnTemp       :'<span class="cspan" data-id="$cid">$name</span>',
	roomTypeTemp        :'<option value="$id">$name($surplusNumber/$totalNumber)</option>',
	roomTypeSelectedTemp:'<option value="$id" selected>$name($surplusNumber/$totalNumber)</option>',
	createAddClient     :'<tr data-id="$id">\n\t<td>$i</td>\n\t<td>$unit</td>\n\t<td class="name">$name</td>\n\t<td>$clientType</td>\n\t<td>$gender</td>\n\t<td>$position</td>\n\t<td>$mobile</td>\n\t<td>$signDate</td>\n</tr>',
	createAddClient2    :'<tr>\n\t<td class="check_item_add2">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$i</td>\n\t<td>$unit</td>\n\t<td class="name">$name</td>\n\t<td>$clientType</td>\n\t<td>$gender</td>\n\t<td>$position</td>\n\t<td>$mobile</td>\n\t<td>$signDate</td>\n</tr>',
	createAddEmployee   :'<tr data-id="$id"><td>$num</td><td class="name">$name</td><td>$gender</td><td>$unit</td><td>$position</td><td>$mobile</td></tr>\n',
	createAddEmployee2  :'<tr><td class="check_item_employee2"><input type="checkbox" class="icheck" value="$id" placeholder=""></td><td>$num</td><td class="name">$name</td><td>$gender</td><td>$unit</td><td>$position</td><td>$mobile</td></tr>',
	hasSelectedA        :'<a href=\"javascript:void(0)\" data-id="$id">$name</a>',
	status              :0,
	pastNumber          :0,
	clientArr           :[],
	employeeArr         :[],
	bindEvent           :function(){
		$('#change_room .client_list .cspan').on('click', function(){
			$('#change_room .client_list .cspan').removeClass('active');
			$(this).addClass('active');
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
function change_room(id, orid, keyword){
	Common.ajax({
		data    :{requestType:'change_room', id:id, rid:orid, keyword:keyword},
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
				if(Number(value.capacity) === Number(value.client.length)){
					$.each(value.client, function(index1, value1){
						strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
											  .replace('$cid', value1.cid);
					});
					str += ScriptObject.changeFulltemp.replace('$i', index+1).replace('$code', value.code)
									   .replace('$situation', '住满').replace('$clientSpan', strBtn)
									   .replace('$rid', value.id);
				}else if(Number(value.capacity)>Number(value.client.length) && Number(value.client.length)>0){
					$.each(value.client, function(index1, value1){
						strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
											  .replace('$cid', value1.cid);
					});
					str += ScriptObject.changeNotFulltemp.replace('$i', index+1).replace('$code', value.code)
									   .replace('$situation', '未住满').replace('$clientSpan', strBtn)
									   .replace('$rid', value.id);
				}else if(value.client.length == 0){
					$.each(value.client, function(index1, value1){
						strBtn += ScriptObject.clientBtnTemp.replace('$name', value1.name)
											  .replace('$cid', value1.cid);
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
}
$(function(){
	//导入excel
	$('#excel_file').on('change', function(){
		//ManageObject.object.loading.loading();
		getIframeData();
	});
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
	/**
	 * 创建房间的操作
	 * 添加入住人员  参会人员按钮
	 */
	$('#distribution_room').find('.create_add_client').on('click', function(){
		getClientAdd(''); // 获取所有的客户列表
	});
	$('#distribution_room').find('.create_add_employee').on('click', function(){
		getEmployeeAdd(''); // 获取所有的员工列表
	});
	$('#add_recipient').find('.main_search').on('click', function(){
		var keyword = $('#add_recipient').find('input[name=keyword]').val();
		getClientAdd(keyword); // 带关键字的获取客户列表
	});
	$('#add_recipient .search_all').on('click', function(){
		getClientAdd(''); // 获取所有的客户列表
	});
	$('#add_recipient_employee').find('.main_search').on('click', function(){
		var keyword = $('#add_recipient_employee').find('input[name=keyword]').val();
		getEmployeeAdd(keyword); // 带关键字的获取客户列表
	});
	$('#add_recipient_employee .search_all').on('click', function(){
		getEmployeeAdd(''); // 获取所有的客户列表
	});
	/**
	 * 房间详情的操作
	 * 添加入住人员  参会人员按钮
	 */
	$('.right_details').find('.add_client').on('click', function(){
		getClientAddDetails(''); // 获取所有的客户列表
	});
	$('.right_details').find('.add_employee').on('click', function(){
		getEmployeeAddDetails(''); // 获取所有的客户列表
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
		ThisObject.object.loading.loading();
		$('#distribution_room').find('.mr_17').removeClass('hide');
		$('#distribution_room').find('input[name=client]').val(str);
		$('#distribution_room').find('#selected_attendee_count_by_1').text(nameStr);
		var employeeArr = $('#distribution_room').find('input[name=employee]').val();
		var narr        = '';
		if(employeeArr.length == 0){
			narr = str;
		}else{
			narr = str.concat(employeeArr);
		}
		$('#distribution_room').find('input[name=person]').val(narr);
		ThisObject.object.loading.complete();
		$('#add_recipient').modal('hide')
	});
	// 多选框点击事件 >>> 内部员工
	$('#add_recipient_employee .check_item_employee').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$(this).parent('.icheckbox_square-green').addClass('checked');
		}else{
			$(this).parent('.icheckbox_square-green').removeClass('checked');
		}
		// 选中的人员
		var str = '', i = 0;
		$('#add_recipient_employee .check_item_employee  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#add_recipient_employee').find('.selected_attendee').text(i);
	});
	// 保存入住人  >>> 内部员工
	$('#add_recipient_employee .btn_save').on('click', function(){
		var n1  = 0;
		var str = [], nameStr = [];
		$('#add_recipient_employee .selected_person_e a').each(function(){
			var id   = $(this).attr('data-id');
			var name = $(this).text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		ThisObject.object.loading.loading();
		$('#distribution_room').find('.mr_17').removeClass('hide');
		$('#distribution_room').find('input[name=employee]').val(str);
		$('#distribution_room').find('#selected_attendee_count_by_2').text(nameStr);
		var clientArr = $('#distribution_room').find('input[name=client]').val();
		var narr      = '';
		if(clientArr.length == 0){
			narr = str;
		}else{
			narr = str.concat(clientArr);
		}
		$('#distribution_room').find('input[name=person]').val(narr);
		ThisObject.object.loading.complete();
		$('#add_recipient_employee').modal('hide');
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
		$('#add_recipient2 .check_item_add2 .icheckbox_square-green.checked').each(function(){
			var id   = $(this).find('.icheck').val();
			var name = $(this).parents('tr').find('.name').text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		/*ThisObject.object.loading.loading();*/
		if(str.length>Number(can_live)){
			ThisObject.object.toast.toast('选择人数不能大于可入住人数！');
			return false;
		}else{
			Common.ajax({
				data    :{requestType:'choose_client_2', id:str, rid:room_id},
				callback:function(r){
					if(r.status){
						$('#add_recipient2').modal('hide');
						ThisObject.object.toast.toast('添加成功！');
						location.reload(); //刷新
					}else{
						ThisObject.object.toast.toast('添加失败！');
					}
				}
			});
		}
	});
	// 保存入住人
	$('#add_recipient2_employee .btn_save').on('click', function(){
		var can_live = $('#add_recipient2_employee').find('.can_live_p').text();
		var room_id  = $('#add_recipient2_employee').find('input[name=room_id]').val();
		var n1       = 0;
		var str      = [], nameStr = [];
		$('#add_recipient2_employee .check_item_employee2 .icheckbox_square-green.checked').each(function(){
			var id   = $(this).find('.icheck').val();
			var name = $(this).parents('tr').find('.name').text();
			str.push(id);
			nameStr.push(name);
			n1++;
		});
		/*ThisObject.object.loading.loading();*/
		if(str.length>Number(can_live)){
			ThisObject.object.toast.toast('选择人数不能大于可入住人数！');
			return false;
		}else{
			Common.ajax({
				data    :{requestType:'choose_employee_2', id:str, rid:room_id},
				callback:function(r){
					if(r.status){
						$('#add_recipient2_employee').modal('hide');
						ThisObject.object.toast.toast('添加成功！');
						location.reload(); //刷新
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
					switch(parseInt(value.occupancy_status)){
						case 0:
							str += ScriptObject.roomDetailsTemp3.replace('$n', index+1).replace('$name', value.name)
											   .replace('$id', value.id);
							break;
						case 1:
							str += ScriptObject.roomDetailsTemp.replace('$n', index+1).replace('$name', value.name)
											   .replace('$id', value.id);
							i++;
							break;
						case 2:
							str += ScriptObject.roomDetailsTemp2.replace('$n', index+1).replace('$name', value.name)
											   .replace('$id', value.id);
							break;
						default:
							break;
					}
				});
				$('#add_recipient2').find('input[name=can_live]').val(capacity-i);
				$('#add_recipient2_employee').find('input[name=can_live]').val(capacity-i);
				$right_details.find('.room_num').text(i);
				if(Number(i)>=Number(capacity)){
					$('.right_details').find('.add_client').hide();
					$('.right_details').find('.add_employee').hide();
				}else{
					$('.right_details').find('.add_client').show();
					$('.right_details').find('.add_employee').show();
				}
				$('#list_c').html(str);
				leave_btn();
				$('.change_btn').on('click', function(){
					var id   = $(this).parents('tr').attr('data-id'); // 需要换房人的ID
					var orid = $(this).parents('.right_details').find('#orid').val(); //
					$('#change_room').find('input[name=id]').val(id);
					$('#change_room').find('input[name=orid]').val(orid);
					change_room(id, orid, '');
				});
			}
		});
		$('.add_client').on('click', function(){
			var per = $('#add_recipient2').find('input[name=can_live]').val();
			$('#add_recipient2').modal('show');
			$('#add_recipient2').find('.can_live_p').text(per);
		});
		$('.add_employee').on('click', function(){
			var per = $('#add_recipient2_employee').find('input[name=can_live]').val();
			$('#add_recipient2_employee').modal('show');
			$('#add_recipient2_employee').find('.can_live_p').text(per);
		});
		$('#add_recipient2').find('input[name=room_id]').val(id);
		$('.add_employee').on('click', function(){
			var per = $('#add_recipient2_employee').find('input[name=can_live]').val();
			$('#add_recipient2_employee').modal('show');
			$('#add_recipient2_employee').find('.can_live_p').text(per);
		});
		$('#add_recipient2_employee').find('input[name=room_id]').val(id);
	});
	// 点击创建房间 获取房间类型及其消息
	$('.create_room').on('click', function(){
		ScriptObject.clientArr   = [];
		ScriptObject.employeeArr = [];
		$('#selected_attendee_count_by_1').html();
		$('#selected_attendee_count_by_2').html();
		Common.ajax({
			data    :{requestType:'get_room_type'},
			callback:function(r){
				var str = '<option>请选择房间类型</option>';
				$.each(r.data, function(index, value){
					str += ScriptObject.roomTypeTemp.replace('$id', value.id).replace('$name', value.name)
									   .replace('$surplusNumber', value.surplus).replace('$totalNumber', value.number);
				});
				$('#distribution_room').find('#room_type').html(str);
			}
		});
	});
	// 修改房间内容
	$('.alter_btn').on('click', function(){
		var id        = $(this).parent('.btn-group').attr('data-id'); //房间ID
		var room_type = $(this).parents('tr').find('.room_type').text();  // 房间类型
		$('#alter_room ').find('input[name=id]').val(id);
		choose($(this));
		Common.ajax({
			data    :{requestType:'get_room_type', id:id},
			callback:function(r){
				if(r.status){
					ScriptObject.status = 1;
				}else{
					ScriptObject.status = 0;
				}
				var str = '<option>请选择房间类型</option>';
				$.each(r.data, function(index, value){
					if(room_type == value.name){
						str += ScriptObject.roomTypeSelectedTemp.replace('$id', value.id).replace('$name', value.name)
										   .replace('$surplusNumber', value.surplus)
										   .replace('$totalNumber', value.number);
					}else{
						str += ScriptObject.roomTypeTemp.replace('$id', value.id).replace('$name', value.name)
										   .replace('$surplusNumber', value.surplus)
										   .replace('$totalNumber', value.number);
					}
				});
				$('#alter_room').find('#room_type_a').html(str);
				ScriptObject.pastNumber = $('#alter_room').find('#room_type_a option:selected').val();
			}
		});
	});
	//选择房间类型自动获取数据
	$('#distribution_room').find('#room_type').on('change', function(){
		var id = $(this).find('option:selected').val();
		Common.ajax({
			data    :{requestType:'get_type', id:id},
			callback:function(r){
				$('#distribution_room').find('#price').val(r.price);
				$('#distribution_room').find('#capacity').val(r.capacity);
				$('#distribution_room').find('#surplus').val(r.surplus);
			}
		});
	});
	//选择房间类型自动获取数据
	$('#alter_room').find('#room_type_a').on('change', function(){
		var id = $(this).find('option:selected').val();
		Common.ajax({
			data    :{requestType:'get_type', id:id},
			callback:function(r){
				$('#alter_room').find('#price_a').val(r.price);
				$('#alter_room').find('#capacity_a').val(r.capacity);
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
});
function choose(e){
	var room_code   = e.parents('tr').find('.room_code').text();
	var room_type   = e.parents('tr').find('.room_type').text();
	var capacity    = e.parents('tr').find('.capacity').text();
	var price       = e.parents('tr').find('.price').text();
	var client_type = e.parents('tr').find('.client_type').text();
	var comment     = e.parents('tr').find('.comment').text();
	var $alter_room = $('#alter_room');
	$alter_room.find('#room_code_a').val(room_code);
	$alter_room.find('#room_type_a').val(room_type);
	$alter_room.find('#client_type_a').val(client_type);
	$alter_room.find('#capacity_a').val(capacity);
	$alter_room.find('#price_a').val(price);
	$alter_room.find('#room_comment_a').val(comment);
}
function leave_btn(){
	$('.leave_btn').on('click', function(){
		var id   = $(this).parents('tr').attr('data-id');
		var date = new Date();
		time     = date.format('yyyy-MM-dd HH:mm:ss ');
		$('#choose_leave_time').find('input[name=id]').val(id);
		$('#choose_leave_time').find('#leave_time').val(time);
	});
}
function checkAssign(){
	var capacity_v = $('#capacity').val();
	var number     = $('input[name=person]').val();
	var n          = number.charAt(number.length-1);
	if(n == ','){
		number = number.substring(0, number.length-1)
	}
	var surplus = $('#distribution_room').find('#surplus').val();
	var a       = number.split(",");
	var len     = a.length;
	if(capacity_v<len){
		ThisObject.object.toast.toast('选择人数不能大于可容纳人数！');
		return false;
	}
	if(surplus == 0){
		ThisObject.object.toast.toast('该房间类型房间数不足！');
		return false;
	}
	return true;
}
function checkStatus(){
	var val = $('#alter_room').find('#room_type_a option:selected').val();
	if(ScriptObject.status == 1){
		if(ScriptObject.pastNumber != val){
			ThisObject.object.toast.toast('该房间已入住人员，不能修改房间类型！');
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
				if(value.sign_time != null){
					var signTime = Common.getLocalTime(value.sign_time);
				}else{
					var signTime = '';
				}
				str += ScriptObject.createAddClient.replace('$id', value.cid).replace('$i', index+1)
								   .replace('$name', value.name)
								   .replace('$gender', gender).replace('$position', value.position)
								   .replace('$mobile', value.mobile).replace('$unit', value.unit)
								   .replace('$signDate', signTime).replace('$clientType', value.type);
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
			/*$('.check_item_add').iCheck({
			 checkboxClass:'icheckbox_square-green',
			 radioClass   :'iradio_square-green'
			 });*/
			/*	$('#add_recipient .check_item_add').find('.iCheck-helper').on('click', function(){
			 var n = 0, arr = [], str = '';
			 $('#add_recipient .check_item_add').find('.icheckbox_square-green.checked').each(function(){
			 n++;
			 });
			 var id   = $(this).parent('.icheckbox_square-green').find('.icheck').val();
			 var name = $(this).parents('tr').find('.name').text();
			 if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			 if(ScriptObject.clientArr.indexOf(id) == -1){
			 ScriptObject.clientArr.push(id);
			 $('.selected_person').append('<a href="javascript:void(0)" data-id='+id+'>'+name+'</a>');
			 }
			 }else{
			 if(ScriptObject.clientArr.indexOf(id) == 0){
			 ScriptObject.clientArr.push(id);
			 $('.selected_person a').each(function(){
			 var tid = $(this).attr('data-id');
			 if(tid == id){
			 $(this).remove();
			 }
			 });
			 }
			 Array.prototype.indexOf = function(val){
			 for(var i = 0; i<this.length; i++){
			 if(this[i] == val) return i;
			 }
			 return -1;
			 };
			 Array.prototype.remove  = function(val){
			 var index = this.indexOf(val);
			 if(index> -1){
			 this.splice(index, 1);
			 }
			 };
			 console.log(id);
			 console.log(ScriptObject.clientArr);
			 ScriptObject.clientArr.remove(id);
			 }
			 //alert(ScriptObject.clientArr);
			 /!*str      = ScriptObject.hasSelectedA.replace('$id', id).replace('$name', name);*!/
			 $('#add_recipient').find('.selected_person').append(str);
			 $('#add_recipient .selected_attendee').text(n);
			 });
			 // 当前人数
			 var m = 0;
			 $('#add_recipient .check_item_add').each(function(){
			 m++;
			 });
			 $('#add_recipient').find('.current_attendee').text(m);
			 // 全选checkbox 选择入住人中全选
			 $('#add_recipient .all_check_add').find('.iCheck-helper').on('click', function(){
			 if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			 $('#add_recipient .check_item_add').find('.icheckbox_square-green').addClass('checked');
			 }else{
			 $('#add_recipient .check_item_add').find('.icheckbox_square-green').removeClass('checked');
			 }
			 // 选中的人员
			 var str = '', i = 0, htm = '';
			 $('#add_recipient .check_item_add  .icheckbox_square-green.checked').each(function(){
			 var id = $(this).find('.icheck').val();
			 str += id+',';
			 i++;
			 var id   = $(this).find('.icheck').val();
			 var name = $(this).parents('tr').find('.name').text();
			 htm += ScriptObject.hasSelectedA.replace('$id', id).replace('$name', name)
			 });
			 $('#add_recipient').find('.selected_person').html(htm);
			 $('#add_recipient').find('.selected_attendee').text(i);
			 });*/
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
				if(value.sign_time != null){
					var signTime = Common.getLocalTime(value.sign_time);
				}else{
					var signTime = '';
				}
				str += ScriptObject.createAddClient2.replace('$id', value.cid).replace('$i', index+1)
								   .replace('$name', value.name)
								   .replace('$gender', gender).replace('$position', value.position)
								   .replace('$mobile', value.mobile).replace('$unit', value.unit)
								   .replace('$signDate', signTime).replace('$clientType', value.type);
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
function getEmployeeAdd(keyword){
	var str = '';
	$('#add_recipient_employee .selected_attendee').text('0');
	Common.ajax({
		data    :{requestType:'get_employee', keyword:keyword},
		callback:function(r){
			$.each(r, function(index, value){
				var gender = '';
				if(value.gender == 0){
					gender = '未知';
				}else if(value.gender == 1){
					gender = '男';
				}else if(value.gender == 2){
					gender = '女';
				}
				if(value.sign_time != null){
					var signTime = Common.getLocalTime(value.sign_time);
				}else{
					var signTime = '';
				}
				str += ScriptObject.createAddEmployee.replace('$num', index+1)
								   .replace('$name', value.name)
								   .replace('$id', value.cid).replace('$position', value.position)
								   .replace('$mobile', value.mobile).replace('$company', value.company)
								   .replace('$gender', gender).replace('$department', value.d_name)
								   .replace('$unit', value.unit);
			});
			$('#add_recipient_employee').find('#employee_body').html(str);
			var htm = '';
			if(ScriptObject.employeeArr != ''){
				$.each(ScriptObject.employeeArr, function(index, value){
					htm += ScriptObject.hasSelectedA.replace('$id', value.id).replace('$name', value.name);
				});
			}
			$('.selected_person_e').html(htm);
			$('#employee_body tr').on('click', function(){
				var id        = $(this).attr('data-id');
				var name      = $(this).find('.name').text();
				var clientArr = [];
				$('.selected_person_e a').each(function(){
					var sid = $(this).attr('data-id');
					clientArr.push(sid);
				});
				if(clientArr.indexOf(id) == -1){
					ScriptObject.employeeArr.push({id:id, name:name});
					$('.selected_person_e').append('<a href="javascript:void(0)" data-id='+id+'>'+name+'</a>');
					console.log(ScriptObject.employeeArr);
				}
				$('.selected_person_e a').off('click');
				$('.selected_person_e a').on('click', function(){
					var id = $(this).attr('data-id');
					console.log('true');
					console.log(ScriptObject.employeeArr);
					$.each(ScriptObject.employeeArr, function(index, value){
						console.log(value);
						console.log(value.id);
						if(id == value.id){
							ScriptObject.employeeArr.splice(index, 1);
							console.log(ScriptObject.employeeArr);
							return false;
						}
					});
					$(this).remove();
				});
			});
			$('.selected_person_e a').off('click');
			$('.selected_person_e a').on('click', function(){
				var id = $(this).attr('data-id');
				console.log('true');
				console.log(ScriptObject.employeeArr);
				$.each(ScriptObject.employeeArr, function(index, value){
					console.log(value);
					console.log(value.id);
					if(id == value.id){
						ScriptObject.employeeArr.splice(index, 1);
						console.log(ScriptObject.employeeArr);
						return false;
					}
				});
				$(this).remove();
			});
			/*	$('.check_item_employee').iCheck({
			 checkboxClass:'icheckbox_square-green',
			 radioClass   :'iradio_square-green'
			 });
			 $('#add_recipient_employee .check_item_employee').find('.iCheck-helper').on('click', function(){
			 var n = 0;
			 $('#add_recipient_employee .check_item_employee').find('.icheckbox_square-green.checked')
			 .each(function(){
			 n++;
			 });
			 $('#add_recipient_employee .selected_attendee').text(n);
			 });
			 // 当前人数
			 var m = 0;
			 $('#add_recipient_employee .check_item_employee').each(function(){
			 m++;
			 });
			 $('#add_recipient_employee').find('.current_attendee').text(m);
			 // 全选checkbox 选择入住人中全选
			 $('#add_recipient_employee .all_check_employee').find('.iCheck-helper').on('click', function(){
			 if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			 $('#add_recipient_employee .check_item_employee').find('.icheckbox_square-green')
			 .addClass('checked');
			 }else{
			 $('#add_recipient_employee .check_item_employee').find('.icheckbox_square-green')
			 .removeClass('checked');
			 }
			 // 选中的人员
			 var str = '', i = 0;
			 $('#add_recipient_employee .check_item_employee  .icheckbox_square-green.checked').each(function(){
			 var id = $(this).find('.icheck').val();
			 str += id+',';
			 i++;
			 });
			 $('#add_recipient_employee').find('.selected_attendee').text(i);
			 });*/
		}
	});
}
function getEmployeeAddDetails(keyword){
	var str = '';
	Common.ajax({
		data    :{requestType:'get_employee', keyword:keyword},
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
				if(value.sign_time != null){
					var signTime = Common.getLocalTime(value.sign_time);
				}else{
					var signTime = '';
				}
				str += ScriptObject.createAddEmployee2.replace('$num', index+1)
								   .replace('$name', value.name)
								   .replace('$id', value.cid).replace('$position', value.position)
								   .replace('$mobile', value.mobile).replace('$company', value.company)
								   .replace('$gender', gender).replace('$department', value.d_name)
								   .replace('$unit', value.unit);
			});
			$('#add_recipient2_employee').find('#employee_body2').html(str);
			$('.check_item_employee2').iCheck({
				checkboxClass:'icheckbox_square-green',
				radioClass   :'iradio_square-green'
			});
			$('#add_recipient2_employee .check_item_employee2').find('.iCheck-helper').on('click', function(){
				var n = 0;
				$('#add_recipient2_employee .check_item_employee2').find('.icheckbox_square-green.checked')
																   .each(function(){
																	   n++;
																   });
				$('#add_recipient2_employee .selected_attendee').text(n);
			});
			// 当前人数
			var m = 0;
			$('#add_recipient2_employee .check_item_employee2').each(function(){
				m++;
			});
			$('#add_recipient2_employee').find('.current_attendee').text(m);
			// 全选checkbox 选择入住人中全选
			$('#add_recipient2_employee .all_check_employee2').find('.iCheck-helper').on('click', function(){
				if($(this).parent('.icheckbox_square-green').hasClass('checked')){
					$('#add_recipient2_employee .check_item_employee2').find('.icheckbox_square-green')
																	   .addClass('checked');
				}else{
					$('#add_recipient2_employee .check_item_employee2').find('.icheckbox_square-green')
																	   .removeClass('checked');
				}
				// 选中的人员
				var str = '', i = 0;
				$('#add_recipient2_employee .check_item_employee2  .icheckbox_square-green.checked').each(function(){
					var id = $(this).find('.icheck').val();
					str += id+',';
					i++;
				});
				$('#add_recipient2_employee').find('.selected_attendee').text(i);
			});
		}
	});
}
// 收款导入excel
function getIframeData(){
	var data = new FormData($('#file_form')[0]);
	console.log(data);
	$.ajax({
		url        :'',
		type       :'POST',
		data       :data,
		dataType   :'JSON',
		cache      :false,
		processData:false,
		contentType:false
	}).done(function(data){
		console.log(data);
		if(data.status){
			ThisObject.object.toast.toast(data.message);
			location.reload(); // 刷新本页面
		}else{
			ThisObject.object.toast.toast('导入失败，请按照Excel模板格式填写。');
		}
	});
	return false;
}