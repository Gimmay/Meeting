/*
 * 更新日志
 *
 * Version 1.00 2016-09-18 20:31
 * 初始版本
 *
 * Version 1.02 2016-09-18 21:32
 * 1、增加组件参数 canCancel 用于控制是否允许点击当前选中元素时取消该元素选中状态
 * 2、增加数据参数 class 用于给每个按钮单独控制 class
 *
 */
try{
	/**
	 * Bootstrap按钮组
	 *
	 * @author Quasar
	 * @version 1.02
	 *
	 * Created by Quasar on 2016-09-18 18:19
	 * Updated by Quasar on 2016-09-18 21:32
	 */
	(function($){
		/**
		 * Bootstrap按钮组
		 *
		 * 参数说明。
		 * 1、组件参数
		 *    classStyle 参数会应用到每个按钮组的 class 属性中，默认值 'btn btn-default'
		 *    data 参数会指定按钮的携带数据，数据必须保证以标准的 JSON 对象数组或 JSON 字符串格式传递，每一组数据必须是 {value: val1, html: val2 [, current: val3, classStyle: val4]} 的格式（每组数据中的参数详见第2条说明），默认值为空数组 []。
		 *    defaultValue 参数会以默认值形式传递到该组件的表单元素，默认值为空字符串 ''
		 *    multiple 参数会指定是否开启复选模式，默认值为 false
		 *    separator 参数会指定该组件在复选模式下的表单元素的值的分隔字符，默认值为 ','
		 *    canCancel 参数会指定该组件在单选模式下是否取消已选项的状态，默认值为 false
		 * 2、数据参数。
		 *    value 参数指定该按钮的值
		 *    html 参数指定该按钮显示的文本
		 *    current 参数会指定是否默认为选择项
		 *    classStyle 参数会在默认类上增加指定类
		 *
		 * @param options 组件参数
		 * @returns {$.fn.QuasarButtonGroup}
		 * @constructor
		 */
		$.fn.QuasarButtonGroup = function(options){
			var defaults      = {
				classStyle  :'btn btn-default',
				data        :[],
				name        :'quasar_button_group',
				defaultValue:'',
				multiple    :false,
				separator   :',',
				canCancel   :false
			};
			var self          = this;
			var opts          = $.extend(defaults, options);
			/**
			 * 绑定事件
			 */
			var bindEvent     = function(){
				$(function(){
					var $buttons = $(self).find('button[data-value]');
					var $hidden  = $(self).find('input[name='+opts.name+'][type=hidden]');
					$buttons.on('touchend mouseup', function(){
						var value_str = $hidden.val();
						var value     = $(this).attr('data-value');
						var value_arr = [];
						if(opts.multiple){
							var flag = 0;
							if(value_str != '' && value_str != undefined){
								value_arr = value_str.split(opts.separator);
								for(var i = 0; i<value_arr.length; i++){
									if(value == value_arr[i]){
										value_arr.splice(i, 1);
										flag = 1;
										break;
									}
								}
							}else flag = 0;
							if(flag == 1){
								$(this).removeClass('active');
								value_str = value_arr.join(opts.separator);
							}else{
								$(this).addClass('active');
								value_arr.push(value);
								value_str = value_arr.join(opts.separator);
							}
							$hidden.val(value_str).attr('value', value_str);
						}
						else{
							if($(this).hasClass('active') && opts.canCancel){
								$buttons.removeClass('active');
								$hidden.val(undefined).attr('value', undefined);
							}else{
								$buttons.removeClass('active');
								$(this).addClass('active');
								$hidden.val(value).attr('value', value);
							}
						}
						$(self).trigger('quasar.event.change');
					});
				});
			};
			/**
			 * 解绑事件
			 */
			var unbindEvent   = function(){
				$(self).find('button[data-value]').off('touchend mouseup');
			};
			/**
			 * 初始化组件
			 *
			 * @private
			 */
			var makeComponent = function(){
				unbindEvent();
				$(self).html('');
				var html    = "<input name=\'::NAME::\' type=\'hidden\' value=\'::DEFAULT_VALUE::\'>\n<div class=\"btn-group\">\n\t::BUTTON::\n</div>";
				var buttons = '';
				if((typeof opts.data) == 'string'){
					try{
						opts.data = JSON.parse(opts.data);
					}catch(Error){
						throw new TypeError('Wrong data format');
					}
				}
				else if((typeof opts.data) == 'object'){
				}
				else throw new TypeError('Wrong data format');
				for(var key in opts.data){
					var tmp = "<button data-value=\"::VALUE::\" class=\"::CLASS::\" type='button'>::HTML::</button>";
					//noinspection JSUnfilteredForInLoop
					if(opts.data[key].hasOwnProperty('value')){
						//noinspection JSUnfilteredForInLoop
						tmp = tmp.replace('::VALUE::', opts.data[key].value);
					}else continue;
					//noinspection JSUnfilteredForInLoop
					if(opts.data[key].hasOwnProperty('html')){
						//noinspection JSUnfilteredForInLoop
						tmp = tmp.replace('::HTML::', opts.data[key].html);
					}else continue;
					var self_class;
					//noinspection JSUnfilteredForInLoop
					if(opts.data[key].hasOwnProperty('classStyle')){
						//noinspection JSUnfilteredForInLoop
						self_class = opts.data[key].classStyle;
					}else self_class = null;
					var class_style;
					var active = false;
					//noinspection JSUnfilteredForInLoop
					if(opts.data[key].hasOwnProperty('current') && opts.data[key].current == true) active = true;
					class_style = (self_class === null ? opts.classStyle : self_class)+(active ? ' active' : '');
					tmp         = tmp.replace('::CLASS::', class_style);
					buttons += tmp;
				}
				html = html.replace('::BUTTON::', buttons).replace('::NAME::', opts.name)
						   .replace('::DEFAULT_VALUE::', opts.defaultValue);
				self.append(html);
			};
			makeComponent();
			bindEvent();
			/**
			 * 定义改变事件
			 *
			 * @param callback 改变时自定义的回调函数
			 */
			this.onQuasarChange = function(callback){
				$(self).off('quasar.event.change').on('quasar.event.change', callback);
			};
			return this;
		}
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库:(');
}