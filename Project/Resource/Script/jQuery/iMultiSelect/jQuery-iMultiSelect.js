/**
 * Created by Iceman on 2017-4-6.
 */
try{
	(function($){
		/**
		 *
		 * @param options
		 */
		$.fn.iMultiSelect = function(options){
			var _self = this;
			console.log(_self);
			$(this).css({"position":"relative"});
			/**
			 *
			 * @type {{inputId: string, inputName: string, inputClass: string, defaultValue: Array, results: string}}
			 */
			var defaults           = {
				inputId     :"id",
				inputName   :"name",
				inputClass  :"class",
				defaultValue:[],
				results     :''
			};
			var opts               = $.extend(defaults, options);  // 重载原型：options的参数覆盖defaults的参数
			/**
			 * 模板 1
			 * 整个外部框架
			 * @type {string}
			 * @private
			 */
			var _templeteWrap      = "<input type=\"hidden\" id=\"$inputId\" name=\"$inputName\" value=\"$defaultId\">\n<div class=\"iceman-multi-showbox\">\n\t<input type=\"text\" class=\"default-input $inputClass\" value=\"$value\" readonly>\n\t<span class=\"icon-direction icon-caret-down\"><i></i></span>\n</div>\n<div class=\"iceman-multi-dropdown hide\">\n\t<div class=\"iceman-multi-dropdown-header\">\n\t\t<a href=\"javascript:void(0)\" class=\"iceman-multi-dropdown-a check-all clearfix\">\n\t\t\t\t<span class=\"iceman-multi-dropdown-check\">\n\t\t\t\t\t<input type=\"checkbox\" class=\"iceman-multi-dropdown-check-all\">\n\t\t\t\t</span> <span class=\"iceman-multi-dropdown-desc\">全选</span> </a>\n\t</div>\n\t<div class=\"iceman-multi-dropdown-body\">\n\t\t<ul>\n\t\t\t$list\n\t\t</ul>\n\t</div>\n</div>"
			/**
			 * 模板 2
			 * 下拉列表
			 * @type {string}
			 * @private
			 */
			var _templeteList      = "<li class=\"iceman-multi-dropdown-list check-one\" data-id=\"$data-id\">\n\t<a href=\"javascript:void(0)\" class=\"iceman-multi-dropdown-a clearfix\">\n\t\t\t\t\t\t<span class=\"iceman-multi-dropdown-check\">\n\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"iceman-multi-dropdown-check-this\">\n\t\t\t\t\t\t</span> <span class=\"iceman-multi-dropdown-desc\">$text</span> </a>\n</li>"
			var _templeteListEmpty = "<li class=\"iceman-multi-dropdown-list\">\n\t<a href=\"javascript:void(0)\" class=\"iceman-multi-dropdown-a clearfix\">\n\t\t</span> <span class=\"iceman-multi-dropdown-desc\">$text</span> </a>\n</li>"
			var _bindEvent         = function(){
				/**
				 *  点击框显示或者隐藏下拉列表。
				 */
				_self.find(".iceman-multi-showbox").on("click", function(e){
					e.stopPropagation();
					if($(this).parent().find(".iceman-multi-dropdown").hasClass("hide")){
						$(this).parent().find(".iceman-multi-dropdown").removeClass("hide");
					}else{
						$(this).parent().find(".iceman-multi-dropdown").addClass("hide");
					}
				});
				/**
				 * 阻止事件冒泡,避免点击下拉列表页执行显示隐藏的操作。
				 */
				_self.on("click", function(e){
					e.stopPropagation();
				});
				/**
				 * 点击插件外页面隐藏插件下拉列表。
				 */
				$("body").on("click", function(){
					_self.find(".iceman-multi-dropdown").addClass("hide");
				});
				/**
				 * 点击列表li触发checkbox点击事件
				 * 判断改行checkbox是否已选中
				 */
				_self.find(".check-one").on("click", function(){
					if($(this).hasClass("selected")){
						$(this).removeClass("selected").find("input[type=checkbox]").prop("checked", false);
					}else{
						$(this).addClass("selected").find("input[type=checkbox]").prop("checked", true);
					}
					_setVal();
				});
				/**
				 * 点击checkbox 判断是否被选中
				 */
				_self.find(".iceman-multi-dropdown-check-this").on("click", function(e){
					e.stopPropagation();
					if($(this).prop("checked")){
						$(this).parents(".iceman-multi-dropdown-list").addClass("selected");
					}else{
						$(this).parents(".iceman-multi-dropdown-list").removeClass("selected");
					}
					_setVal();
				});
				/**
				 * 全选操作
				 */
				_self.find(".iceman-multi-dropdown-check-all").on("click", function(){
					if($(this).prop("checked")){
						$(".check-one").each(function(){
							$(this).addClass("selected").find("input[type=checkbox]").prop("checked", true);
						});
					}else{
						$(".check-one").each(function(){
							$(this).removeClass("selected").find("input[type=checkbox]").prop("checked", false);
						});
					}
					_setVal();
				});
			};
			var _unbindEvent       = function(){
				_self.find(".iceman-multi-showbox").off("click");
				_self.find(".check-one").off("click");
			};
			var _setVal            = function(){
				var arrTxt = [], arrId = [];
				_self.find(".check-one").each(function(){
					if($(this).find("input[type=checkbox]").prop("checked")){
						var txt = $(this).find(".iceman-multi-dropdown-desc").text();
						var id  = $(this).attr("data-id");
						arrTxt.push(txt);
						arrId.push(id);
					}
				});
				_self.find(".default-input").val(arrTxt);
				_self.find("input[type=hidden]").val(arrId);
			};
			/**
			 * 初始化 写入组件 init
			 *
			 * @param inputId  隐藏提交input的写入ID
			 * @param inputName  隐藏提交input的写入NAME
			 * @param inputClass  显示input的写入CLASS
			 * @param defaultValue  默认的选中的值
			 * @param results  传入的列表值
			 * @private
			 */
			var _makeComponentInit = function(inputId, inputName, inputClass, defaultValue, results){
				_unbindEvent(results);
				var strWrap = '', strList = '';
				if(results != ''){
					$.each(results, function(index, value){
						strList += _templeteList.replace("$data-id", value.id).replace("$text", value.name)
					});
					strWrap = _templeteWrap.replace("$inputId", inputId).replace("$inputName", inputName)
										   .replace("$inputClass", inputClass).replace("$value", defaultValue)
										   .replace("$list", strList);
				}else{
					console.log("暂无数据！");
					strList = _templeteListEmpty.replace("$text", "暂无数据");
					strWrap = _templeteWrap.replace("$inputId", inputId).replace("$inputName", inputName)
										   .replace("$inputClass", inputClass).replace("$value", defaultValue)
										   .replace("$list", strList);
				}
				_self.html(strWrap);
				_bindEvent();
			};
			_makeComponentInit(opts.inputId, opts.inputName, opts.inputClass, opts.defaultValue, opts.results);
			/**
			 * 插件添加数据的方法
			 * @param results
			 */
			/*this.setValue = function(defaultValue, results){
			 _unbindEvent();
			 var strWrap = '', strList = '', strVal = [], strId = [];
			 $.each(results, function(index, value){
			 strList += _templeteList.replace("$data-id", value.id).replace("$text", value.name)
			 });
			 strWrap = _templeteWrap.replace("$list", strList);
			 $.each(defaultValue, function(index, value){
			 strVal.push(value.name);
			 strId.push(value.id);
			 });
			 strWrap = _templeteWrap.replace("$value", strVal).replace("$defaultId", strId)
			 .replace("$list", strList);
			 _self.html(strWrap);
			 _bindEvent();
			 };*/
			return _self;
		};
	})(jQuery);
}catch(e){
	console.log(e);
}
