/**
 * Created by qyqy on 2016-11-29.
 */

$(function(){
	var wrap_width  = $('.wrap').height();
	var body_height = $('body').height();
	$('.wrap').css('margin-top', body_height/2-wrap_width/2);
	var form = $('#form').serialize();
	$('#submit').on('click', function(){
		Common.ajax({
			data    :form,
			callback:function(r){
				console.log(r);
			}
		});
	});
});
