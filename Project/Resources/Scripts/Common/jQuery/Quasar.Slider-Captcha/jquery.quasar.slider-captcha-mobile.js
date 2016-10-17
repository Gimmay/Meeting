try{
	/*
	 * 更新日志
	 *
	 * Version 1.00 2016-09-22 01:24
	 * 初始版本
	 *
	 */
	(function($){
		/**
		 * 滑动验证组件
		 *
		 * 参数说明。
		 *   widthLayout number  指定滑动区域的宽度（废弃），默认值为 250。
		 *   widthButton number  指定滑块的宽度（废弃），默认值为 50。
		 *   borderLayout number  滑动区域边框值（废弃），默认值为 1。
		 *   borderButton number  滑块边框值（废弃），默认值为 1。
		 *   bgColorSuccess String  验证成功滑动区域颜色，默认值为 'rgba(100, 200, 50, 0.9)'。
		 *   bgColorFailure String  验证失败滑动区域颜色，默认值为 'rgba(200, 50, 50, 0.9)'。
		 *   submitUrl String  提交地址，默认值为 ''。
		 *   tipWords String  滑动区域提示语，默认值为 '向右滑动滑块'。
		 *   tipSuccess String  验证成功提示语，默认值为 '验证成功:)'。
		 *   tipFailure String  验证成功提示语，默认值为 '验证失败:('。
		 *   hash String  验证hash值，默认值为 ''。
		 *
		 * Created by Quasar on 2016-05-09
		 * Updated by Quasar on 2016-09-22 01:24
		 *
		 * @author Quasar
		 * @version 1.00
		 * @param options 组件参数（JSON对象，索引值详见以上的参数说明）
		 * @returns {$.fn.QuasarSliderCaptcha}
		 * @constructor
		 */
		$.fn.QuasarSliderCaptcha = function(options){
			var self = this;
			var defaults = {
				widthLayout   :250,
				widthButton   :50,
				borderLayout  :1,
				borderButton  :1,
				bgColorSuccess:'rgba(100, 200, 50, 0.9)',
				bgColorFailure:'rgba(200, 50, 50, 0.9)',
				submitUrl     :'',
				tipWords      :'向右滑动滑块',
				tipSuccess    :'验证成功:)',
				tipFailure    :'验证失败:(',
				hash          :''
			};
			var opts = $.extend(defaults, options);
			var $_button = null;
			/**
			 * 用于缓存记录
			 * * lashPosition 记录最后的移动位置。
			 * * moveDirection 记录移动方向。
			 *
			 * @type {{lastPosition: number, moveDirection: string}}
			 * @private
			 */
			var _cache = {
				lastPosition :0,
				moveDirection:'L/R'
			};
			var _func = {
				/**
				 * 判断是否回到始发位置。
				 *
				 * @param x 相对横坐标
				 * @returns {boolean}
				 */
				isStart     :function(x){
					return x<=opts.widthButton;
				},
				/**
				 * 判断是否到最终位置。
				 *
				 * @param x 相对横坐标
				 * @returns {boolean}
				 */
				isEnd       :function(x){
					return x>=(opts.widthLayout-opts.widthButton);
				},
				/**
				 * 重置到始发位置
				 */
				reset       :function(){
					$_button.animate({left:0}, {
						complete:function(){
							$_button.addClass('slider-captcha-transition-5')
						}
					});
					_cache.lastPosition = 0;
					_cache.moveDirection = 'L';
				},
				/**
				 * 去到最终位置
				 *
				 * *这里会向提交地址进行验证，包括提交一个键名为'type'值为'quasar_slider_verify'，以及一个键名为'hash'的 hash 值
				 * *在引入该组件的页面应该在服务端生成一个 hash 值然后缓存起来，将其输出到组件参数(hash)中，待提交时，需要服务端端比对提交 hash 值和服务端的缓存值相等
				 * *若验证成功需要服务端返回 JSON 字符串，其中携带键名为 status 的状态值，1或0，true或false，表示验证结果
				 */
				goEnd       :function(){
					$_button.animate({left:(opts.widthLayout-opts.widthButton-opts.borderButton*2)}, {
						complete:function(){
							$_button.addClass('slider-captcha-transition-5')
						}
					});
					_cache.lastPosition = (opts.widthLayout-opts.widthButton-opts.borderButton*2);
					_cache.moveDirection = 'R';
					//noinspection JSUnusedLocalSymbols
					$.ajax({
						url     :opts.submitUrl,
						data    :{type:'quasar_slider_verify', hash:opts.hash},
						type    :'POST',
						dataType:'json',
						async   :true,
						success :function(r){
							if(r.status){
								$_button.off('touchstart');
								$_button.css('cursor', 'default');
								$(self).css('background', opts.bgColorSuccess);
								$((self.selector)+'>p.slider-captcha-tip').animate({opacity:0}, {
									complete   :function(){
										$(this).html(opts.tipSuccess).css('color', 'white').animate({opacity:1}, 250);
									}, duration:250
								});
							}else{
								$_button.off('touchstart');
								$_button.css('cursor', 'default');
								$(self).css('background', opts.bgColorFailure);
								$((self.selector)+'>p.slider-captcha-tip').animate({opacity:0}, {
									complete   :function(){
										$(this).html(opts.tipFailure).css('color', 'white').animate({opacity:1}, 250);
									}, duration:250
								});
							}
						}, error:function(XMLHttpRequest, textStatus, errorThrown){
							_func.reset();
							alert('网络错误');
						}
					});
				},
				/**
				 * 写入缓存变量
				 *
				 * @param x 相对横坐标
				 */
				alterHistory:function(x){
					if(x>_cache.lastPosition) _cache.moveDirection = 'R';
					else if(x<_cache.lastPosition) _cache.moveDirection = 'L';
					_cache.lastPosition = x;
				},
				/**
				 * 生成 DOM 元素
				 */
				buildDom    :function(){
					$(self).addClass('slider-captcha-transition-5 slider-captcha-layout').append('<p class="slider-captcha-tip">'+opts.tipWords+'</p>\n<div class="slider-captcha-button slider-captcha-transition-5"></div>');
					$_button = $((self.selector)+'>div.slider-captcha-button');
				},
				/**
				 * 绑定事件
				 */
				bindEvent   :function(){
					$((self.selector)+'>div.slider-captcha-button').on('touchstart', function(){
						$(this).removeClass('slider-captcha-transition-5');
						var offset = $(this).offset();
						$(document).on('touchmove', function(movevent){
							//noinspection JSUnresolvedVariable
							var x = movevent.originalEvent.changedTouches[0].clientX-offset.left;
							$_button.stop();
							_func.alterHistory(x);
							if(x<(opts.widthLayout-opts.widthButton/2) && x>=opts.widthButton/2){
								if(_func.isStart(x) && _cache.moveDirection == 'L'){
									$(document).off('touchmove');
									_func.reset();
								}
								if(_func.isEnd(x)){
									$(document).off('touchmove');
									_func.goEnd();
								}
								$_button.css('left', x-opts.widthButton/2);
							}
						});
					});
					$(document).on('touchend', function(){
						$(this).off('touchmove');
						if(_cache.lastPosition<opts.widthLayout-opts.widthButton-opts.borderButton*2) _func.reset();
					});
				},
				/**
				 * 开始初始化
				 */
				init        :function(){
					this.buildDom();
					this.bindEvent();
				}
			};
			_func.init();
			return this;
		};
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库:(');
}