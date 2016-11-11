/**
 * Created by qyqy on 2016-10-10.
 */

var badgeManage = {
	// 绑定事件
	bindEvent    :function(){
		var self = this;
		//上传背景图片
		$('#updateBackground').on('change', function(){
			$('#submit_bg').trigger('click');
		});
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
			$('.cart_view .cart_view_item').eq(index).addClass('hide');
		});
		// 保存胸卡模板
		$('#keep_badge_temp').on('click', function(){
			var tempHtml        = $('.cart_view').html();
			var width           = $('.temp_width').val();
			var height          = $('.temp_height').val();
			var client_name_id  = $('.vote_list').find('.client_name').attr('data-id');
			var qrcode          = $('.vote_list').find('.QRcode').attr('data-id');
			var meeting_name_id = $('.vote_list').find('.meeting_name').attr('data-id');
			var time_id         = $('.vote_list').find('.time').attr('data-id');
			var sign_place_id   = $('.vote_list').find('.sign_place').attr('data-id');
			var unit_id         = $('.vote_list').find('.unit').attr('data-id');
			var brief_id        = $('.vote_list').find('.brief').attr('data-id');
			var badge_name      = $('input[name=badge_name]').val();
			var data            = {
				temp      :tempHtml,
				name      :badge_name,
				attributes:{
					width :width,
					height:height,
					column:{
						clientName :client_name_id,
						qrcode     :qrcode,
						meetingName:meeting_name_id,
						meetingTime:time_id,
						signPlace  :sign_place_id,
						unit       :unit_id,
						brief      :brief_id
					}
				}
			};
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'create', data:data},
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast("保存成功");
						setTimeout(function(){
							//location.replace(location.href);
						}, 1000)
					}else{
						ManageObject.object.toast.toast("保存失败");
					}
				}
			});
		});
		// 系统模板 or 设计模板
		$('.nav_tab').find('.nav_tab_li').on('click', function(){
			$('.nav_tab').find('.nav_tab_li').removeClass('active');
			$(this).addClass('active');
			var index = $(this).index();
			$('.tab_c').addClass('hide');
			$('.tab_c').eq(index).removeClass('hide');
		});
		// 选择系统模板的选择
		$('.system_tem_ul > li').on('click', function(){
			$('.system_tem_ul > li').removeClass('active');
			$(this).addClass('active');
			var id = $(this).attr('data-id');
			$('.system_tem').find('input[name=id]').val(id);
		})
		// 选择胸卡字体颜色
		$('.choose-color').on('change', function(){
			var color = $(this).val();
			$('.badge_muban').find('.cart_view_item').css('color', color);
		});
	},
	// 计算胸卡设计（右侧）的宽度
	setWidth     :function(){
		var $badge_box = $('.badge_box');
		var $cart_view = $('.cart_view');
		var $cart_set  = $('.cart_set');
		// 右边自定义宽度
		$cart_set.outerWidth($badge_box.width()-$cart_view.outerWidth());
	},
	// 设计胸卡模板的宽高
	setTemplateWH:function(width, height){
		$('.badge_muban').width(width).height(height);
		badgeManage.setWidth();
	},
	// 上传背景
	upLoadBg     :function(){
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
	},
	// 初始化关键字ID
	initKeyDateId:function(){
		var $vote_list = $('.vote_list');
		if($vote_list.find('.client_name').hasClass('no_choose')){
			$vote_list.find('.client_name').attr('data-id', 0)
		}else{
			$vote_list.find('.client_name').attr('data-id', 1)
		}
		if($vote_list.find('.QRcode').hasClass('no_choose')){
			$vote_list.find('.QRcode').attr('data-id', 0)
		}else{
			$vote_list.find('.QRcode').attr('data-id', 1)
		}
		if($vote_list.find('.meeting_name').hasClass('no_choose')){
			$vote_list.find('.meeting_name').attr('data-id', 0)
		}else{
			$vote_list.find('.meeting_name').attr('data-id', 1)
		}
		if($vote_list.find('.time').hasClass('no_choose')){
			$vote_list.find('.time').attr('data-id', 0)
		}else{
			$vote_list.find('.time').attr('data-id', 1)
		}
		if($vote_list.find('.sign_place').hasClass('no_choose')){
			$vote_list.find('.sign_place').attr('data-id', 0)
		}else{
			$vote_list.find('.sign_place').attr('data-id', 1)
		}
		if($vote_list.find('.unit').hasClass('no_choose')){
			$vote_list.find('.unit').attr('data-id', 0)
		}else{
			$vote_list.find('.unit').attr('data-id', 1)
		}
		if($vote_list.find('.brief').hasClass('no_choose')){
			$vote_list.find('.brief').attr('data-id', 0)
		}else{
			$vote_list.find('.brief').attr('data-id', 1)
		}
	},
	// 计算ul的宽度
	setUlWidth   :function(){
		var $system_tem_ul = $('.system_tem_ul');
		var li             = $system_tem_ul.find('li');
		var w              = li.outerWidth(true);
		var len            = li.length;
		$system_tem_ul.width(w*len);
	},
	// 模板
	voteTemp     :'<li><span>$name</span><i class="glyphicon glyphicon-trash"></i></li>',
};
$(window).resize(function(){
	badgeManage.setWidth();
});
$(function(){

	// 计算胸卡设计的宽度
	badgeManage.setWidth();
	badgeManage.bindEvent();
	badgeManage.setUlWidth();
});
