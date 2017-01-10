try{
	/*
	 * 更新日志
	 *
	 * Version 1.00 0000-00-00 00:00
	 * 初始版本
	 *
	 */
	(function($){
		/**
		 * 组件模板
		 *步骤插件
		 *
		 * @created 2016-12-29 8:00
		 * @updated  2016-12-29 15:00
		 * @author Iceman
		 * @version 1.00
		 * @param options 组件参数 {'message':'xxxxxxxxx!'}
		 * @returns {$.fn.Iceman}
		 * @constructor
		 */
		$.fn.IcemanStep = function(options){
			var self     = this;
			var event    = $(this);
			var defaults = {
				message:["第一个", "第二个", "第三个"],
				index  :2
			};
			var opts     = $.extend(defaults, options);
			console.log(opts);
			/**
			 * 模板
			 * @type {*[]}
			 * @private
			 */
			var _templateDefault    = "<li class=\"step-default #class\">#title<span class=\"arrow_step\"><span class=\"arrow-pre\"></span><span class=\"arrow-next\"></span></span></li>";
			var _templateEnd        = "<li class=\"step-default step-next\">#title</li>";
			var _templateEndSpecial = "<li class=\"step-default step-current\">#title</li>";
			var _templateNext       = "<li class=\"step-default step-next\">#title<span class=\"arrow_step\"><span class=\"arrow-pre\"></span><span class=\"arrow-next\"></span></span></li>";
			/**
			 * 初始化组件
			 * @param message  传入的步骤数组
			 * @param index 选择的位置
			 * @private
			 */
			var _makeComponent      = function(message, index){
				var str = '<ul class="step-container">';
				var len = message.length;
				$.each(message, function(dex, value){
					if(len>2){
						if(!(len == (dex+1))){
							if((dex+1) == index){
								str += _templateDefault.replace('#title', value).replace('#class', 'step-current');
							}else if((dex+1)>index){
								str += _templateNext.replace('#title', value).replace('#class', 'step-next');
							}else if((dex+1)<index){
								str += _templateDefault.replace('#title', value).replace('#class', 'step-pass');
							}
						}else{
							str += _templateEnd.replace('#title', value);
						}
					}else{
						if(dex == 0){
							str += _templateDefault.replace('#title', value).replace('#class', 'step-pass');
						}else{
							console.log('gan');
							str += _templateEndSpecial.replace('#title', value).replace('#class', 'step-current');
						}
					}
				});
				str = str+'</ul>';
				event.html(str); // 写入HTML
				/**
				 * 根据步骤的长度设定每个步骤框的宽度
				 */
				(function(){
					event.find('.step-default').width(1/len*100+'%');
				})();
			};
			_makeComponent(opts.message, opts.index);
			return self;
		}
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库！');
}