/*
 Create by Quasar on 2016-7-25 14:27
 */
$(function(){

	/*
	 *  页面左侧的导航栏
	 *  点击二级导航打开或者关闭
	 *  图标变化
	 *  打开二级导航的同时其他兄弟元素导航关闭二级导航
	 * */
	$('.side-item-link').each(function(){
		if($(this).parent('.side_item').hasClass('active')){
			$(this).find('.arrow').removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-down');
		}else{
			$(this).find('.arrow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-left');
		}
	});
	/*
	 *  分配角色
	 *  所有角色框中，点击成为已选角色
	 *  已选角色中点击删除已选角色
	 */
	$('.all_area a').on('click', function(){
		var id          = $(this).attr('data-id');
		var value       = $(this).text();
		var select_role = $('.select_area a');
		var tem         = '<a class=\"btn btn-default btn-sm\" href="javascript:void(0)" role="button" data-id='+id+'>'+value+'</a>';
		if(select_role.length>0){
		}else{
		}
	});
});
var Common = {
	ajax:function(options){
		var self   = this;
		var result = null;
		if(!options.hasOwnProperty('type')){
			options.type = 'post';
		}
		if(!options.hasOwnProperty('url')){
			options.url = '';
		}
		if(!options.hasOwnProperty('data')){
			options.data = {};
		}
		if(!options.hasOwnProperty('callback')){
			options.callback = function(){
			};
		}
		if(!options.hasOwnProperty('async')){
			options.async = true;
		}
		$.ajax({
			type    :options.type,
			url     :options.url,
			data    :options.data,
			dataType:'json',
			async   :options.async,
			success :function(r){
				options.callback(r);
				result = r;
			},
			error   :function(){
			}
		});
		return result;
	},
	
	/*
	 *   ==============================RegExp================================
	 *   RegExp
	 *   正则判断字符串是否邮箱、手机号码、电话、传真、汉字、数字、特殊字符
	 *   ==============================RegExp================================
	 */
	RegExpClass:function(){

		//验证字符串是否为email
		this.isEmail = function(str){
			var emailReg = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*\.[\w-]+$/i;
			return emailReg.test(str);
		};

		//验证字符串是否为手机号码
		this.isMobile = function(str){
			var patrn = /^((13[0-9])|(15[0-35-9])|(18[0,2,3,5-9]))\d{8}$/;
			return patrn.test(str);
		};

		//验证字符串是否为电话或者传真
		this.isTel = function(str){
			var patrn = /^[+]{0,1}(\d){1,3}[ ]?([-]?((\d)|[ ]){1,12})+$/;
			return patrn.test(str);
		};

		//验证字符串是否为汉字
		this.isCN = function(str){
			var p = /^[\u4e00-\u9fa5\w]+$/;
			return p.test(str);
		};

		//验证字符串是否为数字
		this.isNum = function(str){
			var p = /^\d+$/;
			return p.test(str);
		};

		//验证字符串是否含有特殊字符
		this.isUnSymbols = function(str){
			var p = /^[\u4e00-\u9fa5\w \.,()，ê?。¡ê（ê¡§）ê?]+$/;
			return p.test(str);
		};
	},

	/*
	 *   ==============================文件上传================================
	 *   文件上传
	 *   index1、index2 = -1 是表示未找到
	 *   初始path 格式  C:\fakepath\reset.css
	 *   lastIndexOf() 方法可返回一个指定的字符串值最后出现的位置，在一个字符串中的指定位置从后向前搜索。
	 *   getFileName==得到文件的名字（不包含路径）
	 *   getFileType==得到文件的类型（后缀）
	 *   judgeFileType==判断上传文件的格式是否符合所需
	 *   ==============================文件上传================================
	 */
	FileUploadClass:function(){

		// 得到上传文件的名字
		this.getFileName = function(selector){
			var path = selector.val();
			var index1 = path.lastIndexOf('/');
			var index2 = path.lastIndexOf('\\');
			var index = Math.max(index1, index2);
			if(index < 0){
				return path;
			}else{
				return path.substring(index + 1);
			}
		};

		// 得到上传文件的类型格式（后缀：css、js、png、jpg）
		this.getFileType = function(fileName){
			var index = fileName.lastIndexOf(".");
			if(index >= 0) {
				return fileName.substr(index+1);
			}
		};

		// 判断文件类型是否正确
		// fileType ：文件的类型（css,jpg,png）
		// judgeType : 自定义的文件类型（数组格式）
		// 如果上传文件的格式合法，则返回true。反之返回false。
		this.judgeFileType = function(fileType,judgeType){

			var flag = false; // 状态
			for(var i=0;i<judgeType.length;i++){
				if(fileType == judgeType[i]){
					flag = true; //一旦找到合适的，立即退出循环
					break
				}
			}
			//条件判断
			if(flag){
				return true;
			}else{
				return false;
			}
		};

		this.uploadFile = function(url,data){
			$.ajax({
				type:'POST',
				url: url,
				enctype: 'multipart/form-data',
				data: {
					file: filename,
				},
				success: function (r) {
					return r;
				},
				error:function(){
				}
			});
		}
	},

};