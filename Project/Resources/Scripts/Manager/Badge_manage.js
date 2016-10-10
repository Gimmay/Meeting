/**
 * Created by qyqy on 2016-10-10.
 */

$(function(){

	// 计算胸卡设计的宽度
	setWidth();

});


// 计算胸卡设计的宽度
function setWidth(){
	var $badge_box = $('.badge_box');
	var $cart_view = $('.cart_view');
	var $cart_set =  $('.cart_set');
	// 右边自定义宽度
	$cart_set.outerWidth($badge_box.width() - $cart_view.outerWidth());
}