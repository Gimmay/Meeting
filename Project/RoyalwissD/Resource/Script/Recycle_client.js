/**
 * Created by qyqy on 2016-10-6.
 */
$(function(){


	// 单个客户恢复
	$('.recover_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#recover_client').find('input[name=id]').val(id);
	});
	// 批量恢复客户
	$('.batch_recover_btn_').on('click', function(){
		var str = '';
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+','
		});
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
			console.log(newStr);
		}
		$('#batch_recover_client').find('input[name=id]').val(newStr);
	});
});