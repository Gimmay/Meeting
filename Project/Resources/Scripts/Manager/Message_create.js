/**
 * Created by qyqy on 2016-9-29.
 */
$(function(){
	$('.filed_w').find('button').on('click',function(){
		var text = $(this).text();
		$('#textarea_edit').insertContent(text);
		var textarea_length = $('#textarea_edit').val().length;
		$('.words_num').text(textarea_length);
	});
})