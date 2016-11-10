/**
 * Created by 1195 on 2016-11-5.
 */
$(function(){
	$('.list_item .title').on('touchend', function(){
		if($(this).parent('.list_item').hasClass('open')){
			$(this).parent('.list_item').removeClass('open');
			$(this).parent('.list_item').find('.list_item_ul').hide();
			$(this).parent('.list_item').find('.icon').addClass('glyphicon-chevron-up')
				   .removeClass('glyphicon-chevron-down');
		}else{
			$(this).parent('.list_item').addClass('open');
			$(this).parent('.list_item').find('.list_item_ul').show();
			$(this).parent('.list_item').find('.icon').addClass('glyphicon-chevron-down')
				   .removeClass('glyphicon-chevron-up');
		}
	});
})
