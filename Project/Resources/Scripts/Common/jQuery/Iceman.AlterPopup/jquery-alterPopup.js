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
		 *
		 *
		 * @created 0000-00-00 00:00
		 * @updated 0000-00-00 00:00
		 * @author Iceman
		 * @version 1.00
		 * @param options 组件参数
		 * @returns {$.fn.IcemanAlterPopup}
		 * @constructor
		 */
		$.fn.IcemanAlterProup = function(options){
			var defaults       = {
				html       :'<input type="text" value="">',
				requestType:'alter_directly',
				cid        :'cid',
				eve        :'eve',
				success    :function(r){
				},
				error      :function(){
				}
			};
			var self           = this;
			var opts           = $.extend(defaults, options);
			var _template      = '<div id="alter-popup">\n\t<form action="" class="form-horizontal form" role="form" method="post">\n\t\t<div class="alter-header"><span>修改</span><i class="alter-close">×</i></div>\n\t\t<div class="alter-content">#content#</div>\n\t\t<div class="alter-footer">\n\t\t\t<button type="button" class="alter-submit btn btn-xs btn-default">确认</button>\n\t\t</div>\n\t\t<input type="hidden" name="requestType" value="#requestType#">\n\t\t<input type="hidden" name="cid" value="#cid#">\n\t\t\n\t</form>\n</div>'
			/**
			 * 绑定事件
			 *
			 * @private
			 */
			var _bindEvent     = function(){
				$('#alter-popup').find('.alter-close').on('click', function(){
					var $obj = $('#alter-popup');
					$obj.fadeOut('1500');
				});
				$('#alter-popup').find('.alter-submit').on('click', function(){
					var $obj      = $('#alter-popup');
					var form_data = $obj.find('.form').serialize();
					var tagName   = $obj.find('.alter-content .sub_val').prop('tagName');
					var txt;
					console.log(tagName);
					if(tagName == 'INPUT'){
						txt = $obj.find('.sub_val').val();
						console.log(txt);
					}else{
						txt = $obj.find('.sub_val option:selected').text();
					}
					$.ajax({
						type    :'post',
						data    :form_data,
						dataType:'json',
						success :function(r){
							if(r.status){
								$obj.fadeOut('1500');
								$obj.trigger('qy-success');
								$obj.on('qy-success', opts.success(txt));
							}else{
								$obj.trigger('qy-error');
							}
						}
					});
				});
			};
			/**
			 * 解绑事件
			 *
			 * @private
			 */
			var _unbindEvent   = function(){
				var $obj = $('#alter-popup');
				$obj.find('.alter-close').off('click');
				$obj.find('.alter-submit').off('click');
			};
			/**
			 * 初始化组件
			 *
			 * @private
			 */
			var _makeComponent = function(html, val, cid, eve, success, error){
				if($('#alter-popup').length == 0){
					var str = '';
					str += _template.replace('#content#', html).replace('#requestType#', val).replace('#cid#', cid);
					$('body').append(str);
				}else{
					$('#alter-popup').find('.alter-content').html(html);
					$('#alter-popup').find('input[name=requestType]').val(val);
					$('#alter-popup').find('input[name=cid]').val(cid);
				}
				var $obj = $('#alter-popup');
				$('#alter-popup').find('.sub_val').focus();
				$obj.fadeIn('1500');
				var popHeight = $obj.height();
				var oEvent    = eve || event;
				var oLeft     = oEvent.clientX;
				var oTop      = oEvent.clientY;
				$obj.css({'top':(oTop-popHeight-40)+'px', 'left':(oLeft+20)+'px'});
				$('#alter-popup').on('qy-error', error);
				_unbindEvent();
				_bindEvent();
			};
			this.onSuccess     = function(callback){
				var $obj = $('#alter-popup');
				$obj.off('qy-success').on('qy-success', callback);
			};
			this.onError       = function(callback){
				var $obj = $('#alter-popup');
				$obj.off('qy-error').on('qy-error', callback);
			};
			_makeComponent(opts.html, opts.requestType, opts.cid, opts.eve, opts.success, opts.error);
			return self;
		}
	})(jQuery);
}catch(ReferenceError){
	console.log('缺少引入jQuery库!');
}