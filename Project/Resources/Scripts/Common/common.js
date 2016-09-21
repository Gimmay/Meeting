/*
 Create by Quasar on 2016-7-25 14:27
 */
$(function(){

	/*
	 *  页面左侧的导航栏
	 *  点击二级导航打开或者关闭
	 *  图标变化
	 *  打开二级导航的同时其他兄弟元素导航关闭二级导航
	 * */
	$('.side-item-link').on('click', function(){
		if($(this).parent('.side_item').hasClass('active')){
			if($(this).parent('.side_item').hasClass('has_child')){
				$(this).find('.arrow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-left');
			}
		}else{
			if($(this).parent('.side_item').hasClass('has_child')){
				$(this).find('.arrow').removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-down');
			}
		}
	});
	/*
	 *  分配角色
	 *  所有角色框中，点击成为已选角色
	 *  已选角色中点击删除已选角色
	 */
	$('.all_area a').on('click', function(){
		var id          = $(this).attr('data-id');
		var value       = $(this).text();
		var select_role = $('.select_area a');
		var tem         = '<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id='+id+'>'+value+'</a>';
		if(select_role.length>0){
		}else{
		}
	});
});
var Common = {
	ajax:function(options){
		var self   = this;
		var result = null;
		if(!options.hasOwnProperty('type')){
			options.type = 'post';
		}
		if(!options.hasOwnProperty('url')){
			options.url = '';
		}
		if(!options.hasOwnProperty('data')){
			options.data = {};
		}
		if(!options.hasOwnProperty('callback')){
			options.callback = function(){
			};
		}
		if(!options.hasOwnProperty('async')){
			options.async = true;
		}
		$.ajax({
			type    :options.type,
			url     :options.url,
			data    :options.data,
			dataType:'json',
			async   :options.async,
			success :function(r){
				options.callback(r);
				result = r;
			},
			error   :function(){
			}
		});
		return result;
	}
};