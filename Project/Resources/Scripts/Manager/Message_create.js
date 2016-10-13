/**
 * Created by qyqy on 2016-9-29.
 */
$(function(){
	$('.filed_w').find('button').on('click',function(){
		var text = $(this).text();
		$('#textarea_edit').insertContent(text);
		var textarea_length = $('#textarea_edit').val().length;
		$('.words_num').text(textarea_length);
		var text = $('#textarea_edit').val();
		var mes_num = Math.ceil(textarea_length/60);
		$('.mes_num').text(mes_num);
		relationMes(text);
	});
	$('#textarea_edit').on('keyup',function(){
		var textarea_length = $('#textarea_edit').val().length;
		$('.words_num').text(textarea_length);
		var text = $('#textarea_edit').val();
		var mes_num = Math.ceil(textarea_length/60);
		$('.mes_num').text(mes_num);
		relationMes(text);
	});

});

function relationMes(txt){
	$('.show_sms_content_container').removeClass('hide').find('.show_sms_content_text').text(txt);
};
