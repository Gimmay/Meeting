try{
	/*
	 * 更新日志
	 *
	 * Version 1.00 2016-08-25 15:23
	 * 初始版本
	 *
	 * Version 1.20 2016-08-28 17:23
	 * 1、插件参数由原来的 class 改成 classStyle
	 * 2、新增自定义显示事件，并支持自定义回调函数
	 * 3、新增自定义隐藏事件，并支持自定义回调函数
	 *
	 * Version 1.21 2016-09-18 19:12
	 * 统一抛出异常类型
	 *
	 * Version 1.30 2016-11-20 14:12
	 * 新增组件参数用于控制显示和隐藏事件的处理
	 *
	 */
	(function($){
		/**
		 * Toast弹窗提示
		 *
		 * 参数说明。
		 *   time number  指定弹窗持续显示的时间，单位秒，默认值为 3。
		 *   text String  指定弹窗显示的文本，默认值为 'Quasar Toast'。
		 *   classStyle String  指定弹窗组件的类属性，默认值为 ''。
		 *   fadeDuration number  控制弹窗淡出淡入时长，默认值为 0.5。
		 *   onDisplay function  指定组件显示时的处理。
		 *   onHidden function  指定组件隐藏时的处理。
		 *
		 * @created 2016-08-23 15:25
		 * @updated 2016-11-20 14:12
		 * @author Quasar
		 * @version 1.30
		 * @param options 组件参数（JSON对象，索引值详见以上的参数说明）
		 * @returns {$.fn.QuasarToast}
		 * @constructor
		 */
		$.fn.QuasarToast = function(options){
			var defaults       = {
				time        :3,
				text        :'Quasar Toast',
				classStyle  :'',
				fadeDuration:0.5,
				onDisplay   :function(){
				},
				onHidden    :function(){
				}
			};
			var self           = this;
			var opts           = $.extend(defaults, options);
			var $_obj          = null;
			/**
			 * 初始化组件
			 *
			 * @private
			 */
			var _makeComponent = function(){
				$('#quasar-toast-dialog').remove();
				var html = "<div id='quasar-toast-dialog' class='"+opts.classStyle+"'>"+opts.text+"</div>";
				$('body').append(html);
			};
			/**
			 * 绑定事件
			 *
			 * @private
			 */
			var _bindEvent     = function(){
				_onQuasarDisplay();
				_onQuasarHidden();
			};
			/**
			 * 初始化组件对象
			 *
			 * @private
			 */
			var _initObject    = function(){
				$_obj = $('#quasar-toast-dialog');
			};
			/**
			 * 显示弹窗
			 * *弹窗显示完毕后会触发组件元素 #quasar-toast-dialog 的 quasar.hidden 事件
			 *
			 * @param text 提示语
			 * @param time 弹窗持续显示时间
			 * @return object
			 */
			this.toast = function(text, time){
				if(arguments[0] == undefined) throw new TypeError("Required parameter is missing");
				if(arguments[1] == undefined) time = opts.time;
				options   = {
					time:time,
					text:text
				};
				opts      = $.extend(opts, options);
				var count = 0;
				$_obj.trigger('quasar.event.display');
				$_obj.html(text).fadeIn(opts.fadeDuration*1000);
				var interval_object = setInterval(function(){
					count++;
					if(count == opts.time){
						$_obj.fadeOut(opts.fadeDuration*1000);
						clearInterval(interval_object);
						$_obj.trigger('quasar.event.hidden');
					}
				}, 1000);
				return self;
			};
			_makeComponent();
			_initObject();
			/**
			 * 定义显示事件
			 *
			 * @param callback 开始显示时自定义的回调函数
			 */
			this.onQuasarDisplay = function(callback){
				$_obj.off('quasar.event.display').on('quasar.event.display', callback);
			};
			/**
			 * 在组件初始化绑定显示时的处理
			 *
			 * @private
			 */
			var _onQuasarDisplay = function(){
				$_obj.off('quasar.event.display').on('quasar.event.display', opts.onDisplay);
			};
			/**
			 * 定义隐藏事件
			 *
			 * @param callback 开始隐藏时自定义的回调函数
			 */
			this.onQuasarHidden = function(callback){
				$_obj.off('quasar.event.hidden').on('quasar.event.hidden', callback);
			};
			/**
			 * 在组件初始化绑定隐藏时的处理
			 *
			 * @private
			 */
			var _onQuasarHidden = function(){
				$_obj.off('quasar.event.hidden').on('quasar.event.hidden', opts.onHidden);
			};
			_bindEvent();

			return this;
		}
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库:(');
}