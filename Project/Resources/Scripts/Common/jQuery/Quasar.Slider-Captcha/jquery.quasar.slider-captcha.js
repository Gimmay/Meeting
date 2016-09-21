try{
	/**
	 * Created by Quasar on 2016/5/9.
	 */
	(function($){
		$.fn.QuasarSliderCaptcha = function(options){
			var _this = this;
			var _default = {
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
			var _opts = $.extend(_default, options);
			var $_button = null;
			var _cache = {
				lastPosition :0,
				moveDirection:'L/R'
			};
			//noinspection JSUnusedGlobalSymbols
			this.api = {
				// todo
			};
			var _func = {
				isStart     :function(x){
					return x<=_opts.widthButton;
				},
				isEnd       :function(x){
					return x>=(_opts.widthLayout-_opts.widthButton);
				},
				reset       :function(){
					$_button.animate({left:0}, {
						complete:function(){
							$_button.addClass('slider-captcha-transition-5')
						}
					});
					_cache.lastPosition = 0;
					_cache.moveDirection = 'L';
				},
				goEnd       :function(){
					var self = this;
					$_button.animate({left:(_opts.widthLayout-_opts.widthButton-_opts.borderButton*2)}, {
						complete:function(){
							$_button.addClass('slider-captcha-transition-5')
						}
					});
					_cache.lastPosition = (_opts.widthLayout-_opts.widthButton-_opts.borderButton*2);
					_cache.moveDirection = 'R';
					//noinspection JSUnusedLocalSymbols
					$.ajax({
						url     :_opts.submitUrl,
						data    :{type:'slider', hash:_opts.hash},
						type    :'POST',
						dataType:'json',
						async   :true,
						success :function(r){
							if(r.status){
								$_button.off('mousedown');
								$_button.css('cursor', 'default');
								$(_this).css('background', _opts.bgColorSuccess);
								$((_this.selector)+'>p.slider-captcha-tip').animate({opacity:0}, {
									complete   :function(){
										$(this).html(_opts.tipSuccess).css('color', 'white').animate({opacity:1}, 250);
									}, duration:250
								});
							}else{
								$_button.off('mousedown');
								$_button.css('cursor', 'default');
								$(_this).css('background', _opts.bgColorFailure);
								$((_this.selector)+'>p.slider-captcha-tip').animate({opacity:0}, {
									complete   :function(){
										$(this).html(_opts.tipFailure).css('color', 'white').animate({opacity:1}, 250);
									}, duration:250
								});
							}
						}, error:function(XMLHttpRequest, textStatus, errorThrown){
							self.reset();
							alert('网络错误');
						}
					});
				},
				alterHistory:function(x){
					if(x>_cache.lastPosition) _cache.moveDirection = 'R';
					else if(x<_cache.lastPosition) _cache.moveDirection = 'L';
					_cache.lastPosition = x;
				},
				buildDom    :function(){
					$(_this).addClass('slider-captcha-transition-5 slider-captcha-layout').append('<p class="slider-captcha-tip">'+_opts.tipWords+'</p>\n<div class="slider-captcha-button slider-captcha-transition-5"></div>');
					$_button = $((_this.selector)+'>div.slider-captcha-button');
				},
				bindEvent   :function(){
					var self = this;
					$((_this.selector)+'>div.slider-captcha-button').on('mousedown', function(clickevent){
						$(this).removeClass('slider-captcha-transition-5');
						var offset = $(this).offset();
						if(clickevent.button == 0){
							$(document).on('mousemove', function(movevent){
								var x = movevent.pageX-offset.left;
								$_button.stop();
								self.alterHistory(x);
								if(x<(_opts.widthLayout-_opts.widthButton/2) && x>=_opts.widthButton/2){
									if(self.isStart(x) && _cache.moveDirection == 'L'){
										$(document).off('mousemove');
										self.reset();
									}
									if(self.isEnd(x)){
										$(document).off('mousemove');
										self.goEnd();
									}
									$_button.css('left', x-_opts.widthButton/2);
								}
							});
						}
					});
					$(document).on('mouseup', function(){
						$(this).off('mousemove');
						if(_cache.lastPosition<_opts.widthLayout-_opts.widthButton-_opts.borderButton*2) self.reset();
					});
				},
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