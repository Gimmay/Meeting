/**
 * Created by qyqy on 2016-9-29.
 */
var ThisObject = {
	attendeeTableTemp:'<tr>\n\t<td class="check_item">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$name</td>\n\t<td>$gender</td>\n\t<td>$phoneNumber</td>\n\t<td>$unit</td>\n\t<td>$createTime</td>\n</tr>',
};
$(function(){

	// 计算文字数量
	$('.filed_w').find('button').on('click', function(){
		var text = $(this).text();
		$('#textarea_edit').insertContent(text);
		var textarea_length = $('#textarea_edit').val().length;
		$('.words_num').text(textarea_length);
		var text    = $('#textarea_edit').val();
		var mes_num = Math.ceil(textarea_length/70);
		$('.mes_num').text(mes_num);
		relationMes(text);
	});
	$('#textarea_edit').on('keyup', function(){
		var textarea_length = $('#textarea_edit').val().length;
		$('.words_num').text(textarea_length);
		var text    = $('#textarea_edit').val();
		var mes_num = Math.ceil(textarea_length/60);
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
	// 保存模板
	$('.save_mes').on('click', function(){
		var data = $('form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, 1);
					ManageObject.object.toast.onQuasarHidden(function(){
						location.href = r.nextPath;
					});
				}else{
					ManageObject.object.toast.toast(r.message, 2);
				}
			}
		})
	});
	var textarea = $('#textarea_edit').val();
	if(textarea != ''){
		$('.show_sms_content_container').removeClass('hide');
		$('.show_sms_content_text').text(textarea);
	}
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
						location.href = r.nextPath;
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
}

