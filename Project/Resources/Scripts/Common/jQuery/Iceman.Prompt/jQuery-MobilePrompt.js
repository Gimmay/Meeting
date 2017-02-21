try{
	/*
	 * 更新日志
	 *
	 * Version 1.00 2017-2-15 11:00
	 * 初始版本
	 *
	 */
	(function($){
		/**
		 * 组件模板
		 *
		 *
		 * @created 2017-2-15 11:00
		 * @updated 2017-2-15 11:00
		 * @author Iceman
		 * @version 1.00
		 * @param options 组件参数
		 * @returns {$.fn.IcemanMobilePrompt}
		 * @constructor
		 */
		$.fn.IcemanMobilePrompt = function(options){
			var defaults = {
				"message":"默认的提示语！！！"
			};
			console.log(options);
			var self           = this;
			var opts           = $.extend(defaults, options);
			/**
			 * 模板
			 * @type {*[]}
			 * @private
			 */
			var _template      = '<div id="m-prompt"><span>#message#</span></div>';
			/**
			 * 绑定事件
			 *
			 * @private
			 */
			var _bindEvent     = function(){
				
			};
			/**
			 * 解绑事件
			 *
			 * @private
			 */
			var _unbindEvent   = function(){
			};
			/**
			 * 初始化组件
			 *
			 * @private
			 */
			var _makeComponent = function(message){
				if($('#m-prompt').length == 0){
					var str = '';
					str += _template.replace('#message#', message);
					$('body').append(str);
				}else{
					$('#m-prompt').find('span').html(message);
				}
				$('#m-prompt').fadeIn('1500');
				setTimeout(function(){
					$('#m-prompt').fadeOut('1500');
				}, 1500);
			};
			_makeComponent(options.message);
			return this;
		}
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库:(');
}