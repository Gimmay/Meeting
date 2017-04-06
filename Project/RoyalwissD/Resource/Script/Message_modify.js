/**
 * Created by qyqy on 2016-9-29.
 */
var ThisObject = {
	attendeeTableTemp:'<tr>\n\t<td class="check_item">\n\t\t<input type="checkbox" class="icheck" value="$id" placeholder="">\n\t</td>\n\t<td>$name</td>\n\t<td>$gender</td>\n\t<td>$phoneNumber</td>\n\t<td>$unit</td>\n\t<td>$createTime</td>\n</tr>',
};
$(function(){
	// 初始化文字数量
	(function(){
		var len = $('#textarea_edit').val().length;
		var num = Math.ceil(len/70);
		$('.words_num').text(len);
		$('.mes_num').text(num);
	})();
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
	// 保存模板
	$('.save_mes').on('click', function(){
		var data = $('.mes_form form').serialize();
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
	var textarea = $('#textarea_edit').val();
	if(textarea != ''){
		$('.show_sms_content_container').removeClass('hide');
		$('.show_sms_content_text').text(textarea);
	}
});
function relationMes(txt){
	$('.show_sms_content_container').removeClass('hide').find('.show_sms_content_text').text(txt);
}


