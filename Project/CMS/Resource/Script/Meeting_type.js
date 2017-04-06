/**
 * Created by gimmay on 2017/3/2.
 */

$(function(){
	showItem();
	$('.footer_nav span').on('click', function(){
		$('.footer_nav span').removeClass('active');
		$(this).addClass('active');
		if($(this).hasClass('all')){
			showType('all');
		}else if($(this).hasClass('gimmay')){
			showType('gimmay');
		}else if($(this).hasClass('royalwiss')){
			showType('royalwiss');
		}else if($(this).hasClass('wayne')){
			showType('wayne');
		}else if($(this).hasClass('gaea')){
			showType('gaea');
		}
		showItem();
	});
});
function showType(cl){
	$('.icon_item').removeClass('show');
	$('.icon_item').each(function(){
		if($(this).hasClass(cl)){
			$(this).addClass('show');
		}
	})
}
function showItem(){
	var len = $('.icon_item.show').length;
	//var width = $('.launchpad').width();
	if(len>9){
		$('.icon_item.show').css({width:25+'%'});
	}else{
		$('.icon_item.show').css({width:33.3333+'%'});
	}
}

