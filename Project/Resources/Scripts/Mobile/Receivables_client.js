/**
 * Created by 1195 on 2016-11-10.
 */
$(function(){
	$('.receivables_item .link').on('click', function(){
		$('#mb_layer').show().animate({width:'375px'});
	});
	$('.mb_close').on('touchend', function(){
		$('#mb_layer').hide();
	})
	var body_w = $('body').width();
	$('.r_details_wp').width(body_w);
});

