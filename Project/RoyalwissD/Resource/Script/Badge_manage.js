/**
 * Created by qyqy on 2016-10-10.
 */

var badgeManage = {
	// 绑定事件
	bindEvent    :function(){
		var self = this;
		//上传背景图片
		/*$('#updateBackground').on('change', function(){
		 $('#submit_bg').trigger('click');
		 });*/
		// 宽高设置
		$('.size_confirm').on('click', function(){
			var temp_width  = $('.temp_width').val();
			var temp_height = $('.temp_height').val();
			self.setTemplateWH(temp_width, temp_height);
		});
		// 胸卡内容关键字选择
		$('.keyword').on('click', function(){
			var index = $(this).index();
			if($(this).hasClass('no_choose')){
				$(this).removeClass('no_choose');
				$('.cart_view .cart_view_item').eq(index).removeClass('hide');
			}
			self.initKeyDateId();
		});
		// 点击回收站按钮
		$('.keyword i').on('click', function(e){
			var index = $(this).parent('.keyword ').index();
			e.stopPropagation();
			$(this).parent('.keyword').addClass('no_choose');
			$(this).parent('.keyword').attr('data-id', '0');
			$('.cart_view .cart_view_item').eq(index).addClass('hide');
		});
		// 保存胸卡模板
		$('#keep_badge_temp').on('click', function(){
			var tempHtml       = $('.cart_view').html(); //左侧胸卡图案的html代码
			var client_name_id = $('.vote_list').find('.client_name').attr('data-id');  //客户姓名
			var qrcode         = $('.vote_list').find('.QRcode').attr('data-id');  //二维码
			var unit_id        = $('.vote_list').find('.unit').attr('data-id');  //单位
			var group_id       = $('.vote_list').find('.group').attr('data-id');  //组号
			var badge_name     = $('input[name=badge_name]').val(); // 胸卡名称
			var data           = {
				temp      :tempHtml,
				name      :badge_name,
				attributes:{
					column:{
						clientName:client_name_id,
						qrcode    :qrcode,
						unit      :unit_id,
						group     :group_id
					}
				}
			};
			console.log(data);
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'create', data:data},
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, 1);
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(r.message, 2);
					}
				}
			});
		});
		// 选择胸卡字体颜色
		/*$('.choose-color').on('change', function(){
		 var color = $(this).val();
		 $('.badge_muban').find('.cart_view_item').css('color', color);
		 });*/
	},
	// 计算胸卡设计（左侧）的宽度
	setWidth     :function(){
		var $badge_box = $('.badge_box');
		var $cart_view = $('.cart_view');
		var $cart_set  = $('.cart_set');
		// 右边自定义宽度
		$cart_set.outerWidth($badge_box.width()-$cart_view.outerWidth());
	},
	// 设计胸卡模板的宽高
	/*setTemplateWH:function(width, height){
	 $('.badge_muban').width(width).height(height);
	 badgeManage.setWidth();
	 },*/
	// 上传背景
	/*upLoadBg     :function(){
	 var data = new FormData($('#uploadBgForm')[0]);
	 $.ajax({
	 url        :'',
	 type       :'POST',
	 data       :data,
	 dataType   :'JSON',
	 cache      :false,
	 processData:false,
	 contentType:false
	 }).done(function(data){
	 var imgUrl = data.imageUrl;
	 $('#badge_bg').attr('src', imgUrl);
	 });
	 return false;
	 },*/
	// 初始化关键字ID
	initKeyDateId:function(){
		var $vote_list = $('.vote_list');
		$vote_list.find('.keyword').each(function(){
			if($(this).hasClass('no_choose')){
				$(this).attr('data-id', 0)
			}else{
				$(this).attr('data-id', 1)
			}
		});
	},
};
$(window).resize(function(){
	badgeManage.setWidth();
});
$(function(){

	// 计算胸卡设计的宽度
	badgeManage.setWidth();
	badgeManage.bindEvent();
});
