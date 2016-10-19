try{
	/*
	 * 更新日志
	 *
	 * Version 1.00 2016-08-22 22:43
	 * 初始版本
	 *
	 * Version 1.11 2016-08-24 00:14
	 * 1、增加默认值设定
	 * 2、增加列表项点击后触发事件
	 *
	 * Version 1.35 2016-08-24 10:14
	 * 1、解绑事件统一封装，绑定事件便对应相应的解绑事件
	 * 2、更新列表时进行解绑操作
	 * 3、新增属性 selectedItem 用于记录选取的列表项
	 * 4、选取列表项时会为其增加名为 selected 的类，若组件初始化时指定了默认值，且默认值在列表源数据中存在，则对应的列表项的类也会标注为 selected
	 * 5、新增6个 get/set 方法（getValue(), getHtml(), getExtend(), setValue(), setHtml(), setExtend()），用于获取/设定值、显示值以及扩展字段
	 *
	 * Version 1.40 2016-08-24 12:47
	 * 1、新增存储列表对象、输入框、隐藏框的私有属性，并增加其 get 方法，用于获取封装好的该对象的 jQuery 对象
	 * 2、若指定默认值，且和数据源中的数据匹配则为属性 selectedItem 写入对应的列表项
	 * 3、新增获取数据源的 get 方法
	 * 4、update 方法中更新配置项中 data 参数的值
	 *
	 * Version 1.50 2016-08-28 17:37
	 * 1、添加注释
	 * 2、添加选择事件，支持自定义回调函数
	 *
	 * Version 1.51 2016-09-03 15:58
	 * 更改默认 CSS 属性（对象元素 position : relative，数据列表 position : absolute、z-index : 10
	 *
	 * Version 1.60 2016-09-03 17:40
	 * 1、增加对搜索框键入的内容进行检索的功能
	 * 2、增加参数用于控制是否在列表中添加一条空项目
	 * 3、增加 ext 字段和 value 字段的判断逻辑
	 *
	 * Version 1.61 2016-09-09 16:13
	 * 1、增加参数 justInput 用于指定是否具有 input 特性
	 * 2、根据 justInput 参数值确定隐藏域在搜索框值改变时的值
	 *
	 * Version 1.70 2016-09-10 15:20
	 * 1、列表元素由 div 替换为 ul+li
	 * 2、input 输入框换成 div
	 * 3、手动实现 placeholder
	 * 4、屏蔽在输入框上的回车事件
	 *
	 * Version 1.80 2016-09-11 11:08
	 * 1、增加关闭按钮，改变列表布局
	 * 2、取消对输入框的 blur 事件
	 * 3、修复诸多问题以及重定义了默认样式
	 * 4、新增参数 listNumber 用于控制列表最大可显示的项目数
	 * 5、屏蔽回车事件
	 *
	 * Version 1.81 2016-09-18 19:08
	 * 统一异常抛出类型
	 *
	 * Version 1.82 2016-09-27 01:18
	 * 重新将输入框的失焦事件写回 用于处理在 justInput 参数未开启情况下的自定义输入情况
	 *
	 * Version 1.90 2016-10-16 18:16
	 * 1、解决存在 placeholder 且开启 justInput 模式下获取输入框内容不准确的问题
	 * 2、更新 getHtml() 方法
	 * 3、解决初始化 DOM 元素时的属性 bug
	 * 4、重新定义触发列表隐藏的事件
	 *
	 * Version 2.00 2016-10-16 20:29
	 * 删除操作栏以及关闭按钮
	 *
	 * Version 2.10 2016-10-17 23:41
	 * 1、重新定义触发列表隐藏事件为 body 点击事件
	 * 2、输入框 focus 事件被 click 事件取代并阻止事件冒泡
	 *
	 * Version 2.22 2016-10-18 15:23
	 * 1、添加键盘的上、下、回车键事件，用于键盘控制插件
	 * 2、添加 getIndex() 方法用于获取列表项的索引值
	 * 3、添加 setItemByIndex() 方法用于根据索引值设定列表项
	 * 4、添加 isHidden() 方法用于检测列表是否隐藏
	 * 5、输入框回车事件会触发名为 quasar.select.enter 的事件，被选取 hover 的列表项动作捕获
	 *
	 * Version 2.30 2016-10-19 01:37
	 * 1、删除 justInput 参数
	 * 2、添加 insert 按键事件，用于控制插件是否可任意键入值
	 * 3、新增 inputObject 属性，用于指向输入框对象
	 */
	(function($){
		/**
		 * 自定义 input+ul+li 列表组件
		 * *支持关键词搜索
		 * *支持数据自定义更新
		 * *支持 form 提交
		 *
		 * 重要说明：
		 * 1、初始化/更新列表数据时，数据必须保证以标准的 JSON 对象数组或 JSON 字符串格式传递。其中 ext 和 keyword 键非必要，其中 ext 为扩展信息，可供使用者配合业务流程自行使用；keyword 的信息会参与关键词索引。
		 * 2、参数说明。
		 *    data Array|String  （一维 JSON 数组或标准的 JSON 字符串）列表数据源，每一组数据必须是 {value: val1, html: val2 [, ext: val3, keyword: val4]} 的格式，默认值为空数组 []。
		 *    name String  指定列表组件的提交键名，默认值为 'quasarSelect'。
		 *    classStyle String  应用到搜索框的 class 属性中，默认值为空字符串 ''。
		 *    placeholder String  指定搜索框的提示语，默认值为 'Search...'。
		 *    idInput String  指定搜索框的 id 属性，默认值为 'quasar-select'。
		 *    idHidden String  指定隐藏表单元素的 id，默认值为 'quasar-select-form'。
		 *    defaultValue String  指定隐藏表单元素的值，默认值为空字符串 ''。
		 *    defaultHtml String  指定搜索框的值，默认值为空字符串 ''。
		 *    hasEmptyItem boolean  指定是否在列表头添加一条空项目，默认值为 true。
		 *    emptyItemHtml String  指定列表头添加的空项目的显示值，默认值为 '---请选择列表项---'。
		 *    listNumber int  指定列表可显示的最大项目条数，默认值为 10。
		 *    request JSON  请求数据的设定参数，详情在以下第三条说明。
		 * 3、request 参数可在 request 方法调用时被重写，也可在组件初始化时便设定好。
		 *    url String  请求地址，默认值为空字符串 ''。
		 *    type String  请求方式，可用值为 'post' 或 'get' ，默认值为 'post'。
		 *    data Array|String  （一维 JSON 数组或标准的 JSON 字符串）列表数据源，格式参照第2条说明中的 data 参数，默认值为空数组 []。
		 *    async boolean  指明请求是异步还同步，可用值为 true 或 false，默认值为 true。
		 *    callback function()  在请求成功后被首先调用的匿名函数，并将请求的返回数据传入匿名函数的第一个参数。
		 *
		 * @updated 2016-10-19 01:37
		 * @created 2016-05-10
		 * @author Quasar
		 * @version 2.30
		 * @param options 组件参数（JSON对象，索引值详见以上说明）
		 * @returns {$.fn.QuasarSelect}
		 * @constructor
		 */
		$.fn.QuasarSelect = function(options){
			var self                = this;
			var defaults            = {
				data         :[],
				name         :'quasarSelect',
				classStyle   :'',
				placeholder  :'Search...',
				idInput      :'quasar-select',
				idHidden     :'quasar-select-form',
				defaultValue :'',
				defaultHtml  :'',
				hasEmptyItem :true,
				emptyItemHtml:'---Select item---',
				listNumber   :10,
				request      :{
					url     :'',
					type    :'POST',
					data    :{},
					async   :true,
					callback:function(){
					}
				}
			};
			var opts                = $.extend(defaults, options);
			// save selector
			this.selectedItem       = null;
			var $_dataList          = null;
			var $_dataItems         = null;
			var $_inputVisible      = null;
			var $_inputHidden       = null;
			var _insertMode         = false;
			/**
			 * 处理提示语的显隐
			 *
			 * @param show 是否显示
			 * @private
			 */
			var _handlerPlaceholder = function(show){
				/**
				 * 创建提示语元素
				 *
				 * @private
				 */
				var _createDom = function(){
					$_inputVisible.find('span.quasar-select-input-placeholder').remove();
					var length            = $_inputVisible.text().length;
					var placeholder_style = (length<=0) ? "" : "style='display: none'";
					$_inputVisible.prepend("<span class='quasar-select-input-placeholder' "+placeholder_style+">"+opts.placeholder+"</span>");
				};
				_createDom();
				var length = $_inputVisible.text().length-$_inputVisible.find('span.quasar-select-input-placeholder')
																		.html().length;
				if(show){
					if(length>0) $_inputVisible.find('span.quasar-select-input-placeholder').hide();
					else $_inputVisible.find('span.quasar-select-input-placeholder').show();
				}else $_inputVisible.find('span.quasar-select-input-placeholder').hide();
			};
			/**
			 * 初始化组件
			 *
			 * @private
			 */
			var _makeComponent      = function(){
				// init
				$(self).html('');
				$(self).css('position', 'relative');
				// make input
				var placeholder_style = (opts.defaultHtml == '') ? "" : "style='display: none'";
				var input_visible     = "<div contenteditable='true' tabindex='1' class=\'quasar-select-input "+opts.classStyle+"\' id=\'"+opts.idInput+"\' data-ext=\'\'><span class='quasar-select-input-placeholder' "+placeholder_style+">"+opts.placeholder+"</span>"+opts.defaultHtml+"</div>";
				var input_hidden      = "<input type=\'hidden\' name=\'"+opts.name+"\' id=\'"+opts.idHidden+"\' value='"+opts.defaultValue+"'>";
				$(self).append(input_hidden);
				$(self).append(input_visible);
				// make ul+li data list (include bind event)
				self.update(opts.data);
			};
			/**
			 * 初始化私有对象
			 *
			 * @private
			 */
			var _initSelector       = function(){
				$_dataItems    = $(self).find('li.quasar-select-data-item');
				$_dataList     = $(self).find('ul.quasar-select-data-list');
				$_inputVisible = $(self).find('#'+opts.idInput);
				$_inputHidden  = $(self).find('input#'+opts.idHidden);
			};
			/**
			 * 绑定事件
			 *
			 * @private
			 */
			var _bindEvent          = function(){
				$(function(){
					var _handlerInput = function(){
						var value      = self.getHtml();
						var value_form = $_inputHidden.val();
						/**
						 * 若非 input 模式则对自定义输入的值进行匹配处理
						 *
						 * @param index
						 */
						var handler    = function(index){
							$_inputVisible.text(opts.data[index].html);
							if(opts.data[i].value != value_form){
								$_inputHidden.val(opts.data[index].value);
								if(opts.data[index].hasOwnProperty('ext')){ //noinspection JSUnresolvedVariable
									$_inputVisible.attr('data-ext', opts.data[index].ext);
								}
								self.selectedItem = $(self)
								.find('li.quasar-select-data-item[data-value='+opts.data[index].value+']')[0];
								$(self).find('li.quasar-select-data-item').removeClass('selected');
								$(self).find('li.quasar-select-data-item[data-value='+opts.data[index].value+']')
									   .addClass('selected');
							}
						};
						if(_insertMode){
							$(self).find('li.quasar-select-data-item').removeClass('selected');
							self.selectedItem = null;
							$_inputHidden.val(value);
						}else{
							for(var i = 0; i<opts.data.length; i++){
								//noinspection JSUnresolvedVariable
								if(opts.data[i].keyword.indexOf(value) != -1){
									handler(i);
									return true;
								}
								if(opts.data[i].html.indexOf(value) != -1){
									handler(i);
									return true;
								}
							}
							$(self).find('li.quasar-select-data-item').removeClass('selected');
							$_inputHidden.val('');
							$_inputVisible.text('').attr({'data-ext':''});
							self.selectedItem = null;
						}
					};
					/**
					 * 为输入框添加聚焦、键入、失焦事件
					 */
					$_inputVisible.on('click', function(e){
						e.stopPropagation(); // 主要用于配合body的click事件
						_handlerPlaceholder(false);
						self.showList();
					}).on('keyup', function(e){
						var keyword = self.getHtml();
						self.search(keyword);
					}).on('keydown', function(e){
						switch(parseInt(e.keyCode)){
							case 45:
								_insertMode = !_insertMode;
								if(_insertMode) $_inputVisible.addClass('insert-mode');
								else $_inputVisible.removeClass('insert-mode');
								break;
							case 38: // up
								_moveToLast();
								break;
							case 40: // down
								_moveToNext();
								break;
							case 13: // enter
								e.preventDefault();
								$(self).trigger('quasar.select.enter');
								return false;
								break;
						}
					}).on('blur', function(){
						_handlerPlaceholder(true);
						_handlerInput();
					});
					/**
					 * 添加点击非插件区域事件
					 */
					$('body').on('click touchend', function(){
						self.hideList();
					});
					$_dataList.on('keydown', function(e){
						switch(parseInt(e.keyCode)){
							case 45:
								_insertMode = !_insertMode;
								if(_insertMode) $_inputVisible.addClass('insert-mode');
								else $_inputVisible.removeClass('insert-mode');
								break;
							case 38: // up
								_moveToLast();
								break;
							case 40: // down
								_moveToNext();
								break;
							case 13: // enter
								e.preventDefault();
								return _selectHoverItem();
								break;
						}
					});
					$(self).on('quasar.select.enter', function(){
						_selectHoverItem();
					});
					/**
					 * 为列表项添加点击、触摸事件
					 *
					 * *注：
					 * 1、事件处理完毕会触发名为 'quasar.event.select' 的事件，使用者可以自由根据此事件写入自己需要的业务逻辑
					 * 2、点取列表项会为其添加名为 selected 的类
					 */
					$(self).find('li.quasar-select-data-item').on('click touchend', function(){
						var value = $(this).attr('data-value'), html = $(this).html(), ext = $(this).attr('data-ext');
						$_inputVisible.text(html).attr({'data-ext':ext ? ext : ''});
						$_inputHidden.val(value);
						self.hideList();
						$(self).find('li.quasar-select-data-item').removeClass('selected');
						self.selectedItem = this;
						$(this).addClass('selected');
						$(this).trigger('quasar.event.select');
					}).on('mouseenter', function(){
						$_dataItems.removeClass('hover');
						$(this).addClass('hover');
					}).on('mouseleave', function(){
						$_dataItems.removeClass('hover');
					});
				});
			};
			/**
			 * 解绑事件
			 *
			 * @private
			 */
			var _unbindEvent        = function(){
				$(self).find('#'+opts.idInput).off('click keydown keyup blur');
				$(self).find('li.quasar-select-data-item')
					   .off('click touchend quasar.event.select mouseenter mouseleave');
				$(self).find('ul.quasar-select-data-list').off('keydown');
				$(self).find('span.quasar-select-data-operation-close').off('click touchend');
				$(self).off('quasar.select.enter');
			};
			/**
			 * 移动到下一个选取项
			 *
			 * @private
			 */
			var _moveToNext         = function(){
				var hidden_mode = self.isHidden();
				var index       = hidden_mode ? self.getIndex() : self.getIndex(true);
				var result      = hidden_mode ? self.setItemByIndex(index+1) : self.setItemByIndex(index+1, true);
				$_dataList.scrollTop(30*(index-opts.listNumber+2));
				return result;
			};
			/**
			 * 移动到上一个选取项
			 *
			 * @private
			 */
			var _moveToLast         = function(){
				var index;
				index = self.isHidden() ? self.getIndex() : self.getIndex(true);
				if(index === 0) return false;
				else{
					var result = self.isHidden() ? self.setItemByIndex(index-1) : self.setItemByIndex(index-1, true);
					$_dataList.scrollTop(30*(index-1));
					return result;
				}
			};
			/**
			 * 选择hover的选择项
			 *
			 * @private
			 */
			var _selectHoverItem    = function(){
				var $object = $(self).find('li.quasar-select-data-item.hover'), ext = $(this).attr('data-ext');
				$_inputHidden.val($object.attr('data-value'));
				$_inputVisible.text($object.text()).attr({'data-ext':ext ? ext : ''});
				self.hideList();
				$(self).find('li.quasar-select-data-item').removeClass('selected');
				self.selectedItem = $object;
				$object.addClass('selected');
				$object.trigger('quasar.event.select');
				return false;
			};
			/**
			 * 更新数据
			 *
			 * @param data 列表数据源。数据类型必须为标准的JSON字符串或JSON对象数组。数据格式 [{value: val11, html: val12 [, ext: val13, keyword: val41]}, {value: val21, html: val22 [, ext: val23, keyword: val24]}, ...]
			 */
			this.update = function(data){
				_unbindEvent();
				$(self).find('ul.quasar-select-data-list').remove();
				var ul_style    = "style=\'max-height: "+opts.listNumber*30+"px\'";
				var data_layout = "<ul class=\'quasar-select-data-list\' tabindex=\'2\' "+ul_style+">\n\t::DATA::\n</ul>";
				if((typeof data) == 'string'){
					try{
						data = JSON.parse(data);
					}catch(Error){
						throw new TypeError('Wrong data format');
					}
				}
				if((typeof data) == 'object'){
					opts.data = data;
					var html  = opts.hasEmptyItem ? "<li data-empty='1' class='quasar-select-data-item'>"+opts.emptyItemHtml+"</li>" : '';
					for(var key in data){
						var tmp = "<li data-ext='::DATA-EXT::' data-value='::DATA-VALUE::' data-keyword='::DATA-KEYWORD::' class='quasar-select-data-item::SELECTED::'>::HTML::</li>";
						//noinspection JSUnfilteredForInLoop
						if(data[key].hasOwnProperty('ext')){
							//noinspection JSUnfilteredForInLoop
							tmp = tmp.replace('::DATA-EXT::', data[key]['ext']);
						}else tmp = tmp.replace('::DATA-EXT::', '');
						//noinspection JSUnfilteredForInLoop
						if(data[key].hasOwnProperty('keyword')){
							//noinspection JSUnfilteredForInLoop
							tmp = tmp.replace('::DATA-KEYWORD::', data[key]['keyword']);
						}else tmp = tmp.replace('::DATA-KEYWORD::', '');
						//noinspection JSUnfilteredForInLoop
						if(data[key].hasOwnProperty('value') && data[key].hasOwnProperty('html')){
							//noinspection JSUnfilteredForInLoop
							tmp = tmp.replace('::DATA-VALUE::', data[key]['value'])
									 .replace('::HTML::', data[key]['html']);
						}else continue;
						//noinspection JSUnfilteredForInLoop
						if(opts.defaultValue == data[key]['value'] && opts.defaultHtml == data[key]['html']) tmp = tmp.replace('::SELECTED::', ' selected');
						else tmp = tmp.replace('::SELECTED::', '');
						html += tmp;
					}
					data_layout = data_layout.replace('::DATA::', html);
					$(self).append(data_layout);
					var $selected_item = $(self).find('li.quasar-select-data-item.selected');
					if($selected_item) self.selectedItem = document.querySelector($selected_item.selector);
					_bindEvent();
				}
				else throw new TypeError('Wrong data format');
			};
			_makeComponent();
			_initSelector();
			/**
			 * 请求数据并更新列表
			 *
			 * @param options 提交参数。参数格式 {url: val1, type: val2, data: val3, async: val4, callback: function(){}}
			 */
			this.request = function(options){
				opts.request = $.extend(opts.request, options);
				$.ajax({
					url     :opts.request.url,
					type    :opts.request.type,
					data    :opts.request.data,
					async   :opts.request.async,
					dataType:'json',
					success :function(data){
						opts.request.callback(data);
						self.update(data);
					}
				});
			};
			/**
			 * 搜索数据
			 *
			 * @param keyword 关键词
			 */
			this.search = function(keyword){
				keyword = keyword.toString();
				$(this).find('li.quasar-select-data-item').show().each(function(){
					var is_delete    = 1;
					var data_keyword = $(this).attr('data-keyword');
					var data_html    = $(this).html().toString().toLocaleLowerCase();
					keyword          = keyword.toLocaleLowerCase();
					if(!data_keyword) data_keyword = '';
					else data_keyword = data_keyword.toString().toLocaleLowerCase();
					if(data_html.indexOf(keyword) != -1) is_delete = 0;
					if(is_delete == 1 && (data_keyword.indexOf(keyword) != -1)) is_delete = 0;
					if(is_delete == 1) $(this).hide();
				});
			};
			/**
			 * 显示列表
			 */
			this.showList = function(){
				$_dataList.slideDown();
			};
			/**
			 * 隐藏列表
			 */
			this.hideList = function(){
				$_dataList.slideUp();
			};
			/**
			 * 获取选择项相对列表的索引位置
			 *
			 * @param hover hover模式而非selected模式
			 * @returns {number}
			 */
			this.getIndex = function(hover){
				if(hover == undefined) hover = false;
				var cur_value = hover ? $(self).find('li.quasar-select-data-item.hover')
											   .attr('data-value') : $_inputHidden.val();
				var result    = -1;
				if(cur_value == '' || cur_value == undefined){
					if(opts.hasEmptyItem) return 0;
					else return -1;
				}
				if(self.isHidden()){
					$.each(opts.data, function(key, value){
						var object_value = value.value.toString();
						if(object_value == cur_value) result = key;
					});
				}
				else{
					$(self).find('li.quasar-select-data-item:visible').each(function(index, element){
						var li_value = element.getAttribute('data-value');
						if(cur_value == li_value) result = index;
					});
				}
				return result;
			};
			/**
			 * 将插件选取项设定为索引值对应的列表项
			 *
			 * @param index 索引值
			 * @param hover 设定为hover而非selected
			 * @returns {boolean}
			 */
			this.setItemByIndex = function(index, hover){
				if(hover == undefined) hover = false;
				var data = [];
				if(self.isHidden()){
					$_dataItems.removeClass('hover');
					data = opts.data[index];
					if(data == undefined) return false;
					$_inputHidden.val(data.value);
					$_inputVisible.text(data.html).attr({'data-ext':data['ext'] ? data['ext'] : ''});
					$_dataItems.trigger('quasar.event.select');
					$(self).find('li.quasar-select-data-item').removeClass('selected');
				}else{
					if(!hover){
						data = $(self).find('li.quasar-select-data-item:visible')[index];
						if(data == undefined) return false;
						$_dataItems.removeClass('selected');
						$_inputHidden.val(data.getAttribute('data-value'));
						$_inputVisible.text(data.innerText);
						$(data).addClass('selected');
						self.hideList();
					}else{
						data = $(self).find('li.quasar-select-data-item:visible')[index];
						if(data == undefined) return false;
						$_dataItems.removeClass('hover');
						$(data).addClass('hover');
					}
				}
				return true;
			};
			/**
			 * 获取值
			 *
			 * @returns string
			 */
			this.getValue = function(){
				return $_inputHidden.val();
			};
			/**
			 * 获取显示字符
			 *
			 * @returns string
			 */
			this.getHtml = function(){
				var html = $_inputVisible.html();
				html     = html.replace(new RegExp('<span[A-Za-z=\"\'_\\-:; ]+>'+opts.placeholder+'</span>'), '');
				html     = html.replace(new RegExp('<[A-Za-z=\"\'_\\-:;/ ]+>'), '');
				return html;
			};
			/**
			 * 获取扩展字符
			 *
			 * @returns string
			 */
			this.getExtend = function(){
				return $_inputVisible.attr('data-ext');
			};
			/**
			 * 设定值
			 *
			 * @param val
			 */
			this.setValue = function(val){
				$_inputHidden.val(val);
			};
			/**
			 * 设定显示字符
			 *
			 * @param html
			 */
			this.setHtml = function(html){
				$_inputVisible.text(html);
			};
			/**
			 * 设定扩展字符
			 *
			 * @param extend
			 */
			this.setExtend = function(extend){
				$_inputVisible.attr('data-ext', extend);
			};
			/**
			 * 获取数据源
			 *
			 * @returns {*}
			 */
			this.getData = function(){
				return opts.data;
			};
			/**
			 * 判断列表是否隐藏
			 */
			this.isHidden = function(){
				return $(self).find('ul.quasar-select-data-list:visible').length == 0
			};
			/**
			 * 定义选择列表事件
			 *
			 * @param callback 自定义回调函数
			 */
			this.onQuasarSelect = function(callback){
				$_dataItems.off('quasar.event.select').on('quasar.event.select', callback);
			};
			/**
			 * 输入框对象
			 */
			this.inputObject = $_inputVisible;
			return this;
		};
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库:(');
}