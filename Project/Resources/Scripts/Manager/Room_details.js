/**
 * Created by qyqy on 2016-10-25.
 */

var ThisObject = {
	attendeeTableTemp:'<tr>\n\t<td class="check_item_m">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$name</td>\n\t<td>$gender</td>\n\t<td>$phoneNumber</td>\n\t<td>$club</td>\n\t<td>$createTime</td>\n</tr>',
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
	// 全选checkbox 选择入住人中全选
	$('.all_check_m').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item_m').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item_m').find('.icheckbox_square-green').removeClass('checked');
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
			ManageObject.object.toast.toast('请选择！');
		}
		$('#batch_delete_room').find('input[name=id]').val(newStr);
	});
	// 搜索条件 入住人员
	$('.btn_search_add').on('click', function(){
		$('.selected_attendee').text('0');
		var data = $('#search_attendee_form').serialize();
		console.log(data);
		data += '&requestType=search';
		Common.ajax({
			data    :data,
			async   :false,
			callback:function(data){
				//ManageObject.object.loading.complete();
				console.log(data);
				var str    = '';
				var number = 0;
				$.each(data, function(index, value){
					var gender = '';
					if(value.gender == 0){
						gender = '未知'
					}else if(value.gender == 1){
						gender = '男'
					}else if(value.gender == 2){
						gender = '女'
					}
					var createTime = new Date(parseInt(value.creatime)*1000).toLocaleString().replace(/:\d{1,2}$/, ' ');
					var signTime   = new Date(parseInt(value.creatime)*1000).toLocaleString().replace(/:\d{1,2}$/, ' ');
					str += ThisObject.attendeeTableTemp.replace('$name', value.name).replace('$gender', gender)
									 .replace('$phoneNumber', value.mobile).replace('$club', value.club)
									 .replace('$createTime', createTime).replace('$id', value.cid);
					number++;
				})
				$('.current_attendee').text(number);
				$('#attendee_body').html(str);
				$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				});
				$('.check_item').find('.iCheck-helper').on('click', function(){
					var n = 0;
					$('.check_item').find('.icheckbox_square-green.checked').each(function(){
						n++;
					});
					$('.selected_attendee').text(n);
				});
				// 全选checkbox
				$('.all_check').find('.iCheck-helper').on('click', function(){
					if($(this).parent('.icheckbox_square-green').hasClass('checked')){
						$('.selected_attendee').text(number);
						$('.check_item').find('.icheckbox_square-green').addClass('checked');
					}else{
						$('.check_item').find('.icheckbox_square-green').removeClass('checked');
					}
				});
				$('.btn_save').on('click', function(){
					var n1 = 0;
					$('.check_item').find('.icheckbox_square-green.checked').each(function(){
						var str = '';
						$('.check_item .icheckbox_square-green.checked').each(function(){
							var id = $(this).find('.icheck').val();
							str += id+','
						});
						var s, newStr = "";
						s             = str.charAt(str.length-1);
						if(s == ","){
							for(var i = 0; i<str.length-1; i++){
								newStr += str[i];
							}
						}
						;
						n1++;
						$('input[name=selected_p]').val(newStr);
						CreateObject.object.toast.toast("添加成功");
						$('#add_recipient').modal('hide')
						$('#selected_attendee_count_by_0').text(n1);
					});
				});
			}
		});
	});
});