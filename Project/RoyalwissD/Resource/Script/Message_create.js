/**
 * Created by qyqy on 2016-9-29.
 */
var ThisObject = {
	attendeeTableTemp:'<tr>\n\t<td class="check_item">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$name</td>\n\t<td>$gender</td>\n\t<td>$phoneNumber</td>\n\t<td>$unit</td>\n\t<td>$createTime</td>\n</tr>',
};
$(function(){

	//计算消息文本字数
	$('.filed_w').find('button').on('click', function(){
		var $textarea_obj = $('#textarea_edit');
		var text          = $(this).text();
		$textarea_obj.insertContent(text);
		var textarea_length = $textarea_obj.val().length;
		$('.words_num').text(textarea_length);
		text        = $textarea_obj.val();
		var mes_num = Math.ceil(textarea_length/70);
		$('.mes_num').text(mes_num);
		relationMes(text);
	});
	// 计算文字数量
	$('#textarea_edit').on('keyup', function(){
		var $textarea_obj   = $('#textarea_edit');
		var textarea_length = $textarea_obj.val().length;
		$('.words_num').text(textarea_length);
		var text    = $textarea_obj.val();
		var mes_num = Math.ceil(textarea_length/70);
		$('.mes_num').text(mes_num);
		relationMes(text);
	});
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 选择创建方式
	$('#style').on('change', function(){
		if($(this).find('option:selected').val() == 1){
			$('.mes_people').removeClass('hide');
			$('.save_mes').addClass('hide');
			$('.send_mes').removeClass('hide');
			$('input[name=requestType]').val('send');
		}else{
			$('.mes_people').addClass('hide');
			$('.save_mes').removeClass('hide');
			$('.send_mes').addClass('hide');
			$('input[name=requestType]').val('create');
		}
	});
	// 搜索条件的收件人
	$('.btn_search_add').on('click', function(){
		$('.selected_attendee').text('0');
		var data = $('#search_attendee_form').serialize();
		data += '&requestType=search';
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			async   :false,
			callback:function(data){
				ManageObject.object.loading.complete();
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
									 .replace('$phoneNumber', value.mobile).replace('$unit', value.unit)
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
						ManageObject.object.toast.toast("添加成功");
						$('#add_recipient').modal('hide')
						$('#selected_attendee_count_by_0').text(n1);
					});
				});
			}
		});
	});
	// 保存模板
	$('.save_mes').on('click', function(){
		var data = $('#submit_message').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, 1);
					ManageObject.object.toast.onQuasarHidden(function(){
						location.href = r.nextPage;
					});
				}else{
					ManageObject.object.toast.toast(r.message, 2);
				}
			}
		})
	});
});
function relationMes(txt){
	$('.show_sms_content_container').removeClass('hide').find('.show_sms_content_text').text(txt);
};
