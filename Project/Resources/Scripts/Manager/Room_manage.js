/**
 * Created by qyqy on 2016-10-25.
 */

/*var ScriptObject = {
 attendeeTableTemp:'<tr>\n\t<td class="check_item_m">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$name</td>\n\t<td>$gender</td>\n\t<td>$phoneNumber</td>\n\t<td>$club</td>\n\t<td>$createTime</td>\n</tr>',
 };*/
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
		$('#delete_hotel ').find('input[name=id]').val(id);
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
	$('.details_btn').on('click',function(){
		$('.right_details').animate({width:'480px'})
	});
	$('.close_btn').on('click',function(){
		$('.right_details').animate({width:'0'})
	});
	$('.details_btn').on('click',function(){
		var id =$(this).parent('.btn-group').attr('data-id');
		Common.ajax({
			data:{requestType:'details',id:id},
			callback:function(r){
				console.log(r);
			}
		});
	});

});

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