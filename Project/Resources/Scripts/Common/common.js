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
	$('.side-item-link').each(function(){
		if($(this).parent('.side_item').hasClass('active')){
			$(this).find('.arrow').removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-down');
		}else{
			$(this).find('.arrow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-left');
		}
	});

	$('.no_link').on('click',function(){
		if($(this).parent('.side_item').hasClass('cls')){
			$(this).parent('.side_item').find('.nav-second-level').css('height','auto');
			$(this).find('.arrow').removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-down');
			$(this).parent('.side_item').removeClass('cls');
		}else{
			$(this).parent('.side_item').find('.nav-second-level').css('height',0);
			$(this).find('.arrow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-left');
			$(this).parent('.side_item').addClass('cls');
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

	/*
	*	个人中心下拉列表显示/隐藏
	 */
	$('.nav_info .name').hover(function(){
		$(this).find('.hidden_dropDown').removeClass('hide');
	},function(){
		$(this).find('.hidden_dropDown').addClass('hide');
	});


	/*
	 *	右边详情框
	 */
	$('.details_btn').on('click', function(){
		$('.right_details').animate({width:'480px'})
	});
	// 关闭详情
	$('.close_btn').on('click', function(){
		$('.right_details').animate({width:'0'})
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
	},
	
	/*
	 *   ==============================RegExp================================
	 *   RegExp
	 *   正则判断字符串是否邮箱、手机号码、电话、传真、汉字、数字、特殊字符
	 *   ==============================RegExp================================
	 */
	RegExpClass:function(){

		//验证字符串是否为email
		this.isEmail = function(str){
			var emailReg = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*\.[\w-]+$/i;
			return emailReg.test(str);
		};

		//验证字符串是否为手机号码
		this.isMobile = function(str){
			var patrn = /^((13[0-9])|(15[0-35-9])|(18[0,2,3,5-9]))\d{8}$/;
			return patrn.test(str);
		};

		//验证字符串是否为电话或者传真
		this.isTel = function(str){
			var patrn = /^[+]{0,1}(\d){1,3}[ ]?([-]?((\d)|[ ]){1,12})+$/;
			return patrn.test(str);
		};

		//验证字符串是否为汉字
		this.isCN = function(str){
			var p = /^[\u4e00-\u9fa5\w]+$/;
			return p.test(str);
		};

		//验证字符串是否为数字
		this.isNum = function(str){
			var p = /^\d+$/;
			return p.test(str);
		};

		//验证字符串是否含有特殊字符
		this.isUnSymbols = function(str){
			var p = /^[\u4e00-\u9fa5\w \.,()，ê?。¡ê（ê¡§）ê?]+$/;
			return p.test(str);
		};
	},
	
};