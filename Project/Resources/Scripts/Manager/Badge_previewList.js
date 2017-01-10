/**
 * Created by qyqy on 2016-10-10.
 */

var badgeManage = {
	// 绑定事件
	bindEvent :function(){
		var self = this;
		// 选择系统模板的选择
		$('.system_tem_ul > li').on('click', function(){
			$('.system_tem_ul > li').removeClass('active');
			$(this).addClass('active');
			var id = $(this).attr('data-id');
			$('.system_tem').find('input[name=id]').val(id);
		})
	},
	// 计算ul的宽度
	setUlWidth:function(){
		var $system_tem_ul = $('.system_tem_ul');
		var list_w         = $('.system_tem_list').width();
		var li             = $system_tem_ul.find('li');
		var w              = li.width()+2;
		var len            = Math.floor(list_w/w);
		$('.w_box').width(w*len+10*5);
	},
};
$(window).resize(function(){
	badgeManage.setUlWidth();
});
$(function(){
	// 计算胸卡设计的宽度
	badgeManage.bindEvent();
	badgeManage.setUlWidth();
	$('.delete_icon').on('click', function(e){
		e.stopPropagation();
		var id        = $(this).parent('li').attr('data-id');
		var active_id = $('body').attr('data-bid');
		$('#delete_badge').find('#bid').val(id);
		if(active_id == id){
			ManageObject.object.toast.toast('不能删除已选中的胸卡模板！');
		}else{
			$('#delete_badge').modal('show');
		}
	});
});
