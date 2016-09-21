/*
 * 更新日志
 *
 * Version 1.00 2016-09-17 16:25
 * 初始版本
 *
 * Version 1.01 2016-09-18 19:12
 * 统一抛出异常类型
 *
 */
try{
	/**
	 * Loading
	 *
	 * @author Quasar
	 * @version 1.01
	 *
	 * Created by Quasar on 2016-09-06
	 * Updated by Quasar on 2016-09-18 19:12
	 */
	(function($){
		/**
		 * loading提示
		 *
		 * @param options 组件参数。须满足 {theme: val1, size: val2} 的格式。theme 参数控制加载组件主题风格，可选值[0, 47]，默认值 0；size 参数控制加载组件区域大小，默认值 150。
		 * @returns {$.fn.QuasarLoading}
		 * @constructor
		 */
		$.fn.QuasarLoading = function(options){
			var defaults        = {
				theme:0,
				size :150
			};
			var self            = this;
			var $layout         = null, $background = null;
			var opts            = $.extend(defaults, options);
			// 模板
			var template        = [
				"<div class=\"loading-0\">\n\t<div>wait...</div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-1\">\n\t<div class=\"rect1\"></div>\n\t<div class=\"rect2\"></div>\n\t<div class=\"rect3\"></div>\n\t<div class=\"rect4\"></div>\n\t<div class=\"rect5\"></div>\n</div>",
				"<div class=\"loading-2\"></div>",
				"<div class=\"loading-3\">\n\t<div class=\"double-bounce1\"></div>\n\t<div class=\"double-bounce2\"></div>\n</div>",
				"<div class=\"loading-4\">\n\t<div class=\"dot1\"></div>\n\t<div class=\"dot2\"></div>\n</div>",
				"<div class=\"loading-5\">\n\t<div class=\"bounce1\"></div>\n\t<div class=\"bounce2\"></div>\n\t<div class=\"bounce3\"></div>\n</div>",
				"<div class=\"loading-6\"></div>\n",
				"<div class=\"loading-7\">\n\t<div class=\"container1\">\n\t\t<div class=\"circle1\"></div>\n\t\t<div class=\"circle2\"></div>\n\t\t<div class=\"circle3\"></div>\n\t\t<div class=\"circle4\"></div>\n\t</div>\n\t<div class=\"container2\">\n\t\t<div class=\"circle1\"></div>\n\t\t<div class=\"circle2\"></div>\n\t\t<div class=\"circle3\"></div>\n\t\t<div class=\"circle4\"></div>\n\t</div>\n\t<div class=\"container3\">\n\t\t<div class=\"circle1\"></div>\n\t\t<div class=\"circle2\"></div>\n\t\t<div class=\"circle3\"></div>\n\t\t<div class=\"circle4\"></div>\n\t</div>\n</div>",
				"<div class=\"loading-8\">\n\t<div class=\"loader\"></div>\n</div>",
				"<div class=\"loading-9\">\n\t<div class=\"loader\"></div>\n</div>",
				"<div class=\"loading-10\"></div>",
				"<div class=\"loading-11\">\n\t<div class=\"cube\">\n\t\t<div class=\"side1\"></div>\n\t\t<div class=\"side2\"></div>\n\t\t<div class=\"side3\"></div>\n\t\t<div class=\"side4\"></div>\n\t\t<div class=\"side5\"></div>\n\t\t<div class=\"side6\"></div>\n\t</div>\n</div>",
				"<div class=\"loading-12\">\n\t<span></span> <span></span> <span></span> <span></span> <span></span>\n</div>",
				"<div class=\"loading-13\">\n\t<span class=\"throbber-loader\"></span>\n</div>",
				"<div class=\"loading-14\">\n\t<span class=\"gauge-loader\"></span>\n</div>",
				"<div class=\"loading-15\">\n\t<span class=\"timer-loader\"></span>\n</div>",
				"<div class=\"loading-16\">\n\t<span class=\"whirly-loader\"></span>\n</div>",
				"<div class=\"loading-17\">\n\t<span></span>\n\t<span></span>\n</div>",
				"<div class=\"loading-18\"><span></span></div>",
				"<div class=\"loading-19\"></div>",
				"<div class=\"loading-20\"></div>",
				"<div class=\"loading-21\">\n\t<i></i>\n\t<i></i>\n\t<i></i>\n\t<i></i>\n\t<i></i>\n</div>",
				"<div class=\"loading-22\">\n\t<i></i>\n\t<i></i>\n\t<i></i>\n</div>",
				"<div class=\"loading-23\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-24\">\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-25\">\n\t<div></div>\n</div>",
				"<div class=\"loading-26\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-27\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-28\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-29\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-30\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-31\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-32\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-33\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-34\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-35\">\n\t<div></div>\n</div>",
				"<div class=\"loading-36\">\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-37\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-38\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-39\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-40\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-41\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-42\">\n\t<div class=\'loading-42-first\'>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t</div>\n\t<div class=\'loading-42-second\'>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t\t<div></div>\n\t</div>\n</div>",
				"<div class=\"loading-43\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-44\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-45\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-46\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>",
				"<div class=\"loading-47\">\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n\t<div></div>\n</div>"
			];
			/**
			 * 获取窗口可视域大小
			 *
			 * @returns {{width: number, height: number}}
			 */
			var getPageViewSize = function(){
				var winWidth  = 0;
				var winHeight = 0;
				if(window.innerWidth) winWidth = window.innerWidth;
				else if((document.body) && (document.body.clientWidth)) winWidth = document.body.clientWidth;
				if(window.innerHeight) winHeight = window.innerHeight;
				else if((document.body) && (document.body.clientHeight)) winHeight = document.body.clientHeight;
				if(document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth){
					winHeight = document.documentElement.clientHeight;
					winWidth  = document.documentElement.clientWidth;
				}
				return {width:winWidth, height:winHeight};
			};
			/**
			 * 页面初始化时以及窗口大小改变时重置布局层大小
			 */
			var resize          = function(){
				var _resize = function(){
					var view    = getPageViewSize();
					$background = $('#quasar-loading-background');
					$layout     = $background.find('#quasar-loading-layout');
					$background.css('height', view.height);
					$layout.css({width:opts.size, height:opts.size});
				};
				_resize();
				$(window).on('resize', _resize);
			};
			/**
			 * 初始化组件
			 *
			 * @private
			 */
			var makeComponent   = function(){
				var $bg = $('#quasar-loading-background');
				$bg.remove();
				var html = "<div id=\'quasar-loading-background\'>\n\t<div id=\'quasar-loading-layout\'>::template::</div>\n</div>";
				html     = html.replace('::template::', template[opts.theme]);
				$('body').append(html);
				resize();
			};
			makeComponent();
			/**
			 * 开始显示加载组件
			 *
			 * @param alterTheme 是否随机改变主题样式
			 */
			this.loading     = function(alterTheme){
				if(alterTheme){
					var max = template.length;
					var ram = parseInt(max*Math.random());
					$layout.html(template[ram]);
				}
				$background.fadeIn(250);
			};
			/**
			 * 结束显示加载组件
			 */
			this.complete    = function(){
				$background.fadeOut(250);
			};
			/**
			 * 改变加载组件的主题样式
			 *
			 * @param id 样式码，可选值[0, 47]
			 */
			this.changeTheme = function(id){
				$layout.addClass('quasar-loading-switch-theme-hide');
				setTimeout(function(){
					id = id%template.length;
					$layout.html(template[id]).removeClass('quasar-loading-switch-theme-hide')
						   .addClass('quasar-loading-switch-theme-show');
					setTimeout(function(){
						$layout.removeClass('quasar-loading-switch-theme-show');
					}, 250);
				}, 250);
			};
			return this;
		}
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库:(');
}