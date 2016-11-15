/**
 * Created by qyqy on 2016-11-10.
 */

var ScriptObject = {
	listItemTemp:'<div class=\"list_item open\">\n\t<div class=\"title\">$company($num)<span class=\"icon glyphicon glyphicon-chevron-down\"></span></div>\n\t<ul class=\"list_item_ul clearfix\">$lilist</ul>\n</div>',
	liItemTemp  :'<li class="client_item"><a data-id="$data-id"><span class="name">$name</span></a></li>',
	search      :function(){
		// 搜索
		var self = this;
		$('.main_search').on('click', function(){
			var keyword = $('#keyword').val();
			var str     = '', str2 = '', i = 0;
			Common.ajax({
				data    :{requestType:'search', keyword:keyword},
				callback:function(r){
					console.log(r);
					$.each(r, function(index1, value1){
						console.log(index1);
						console.log(value1);
						$.each(value1, function(index2, value2){
							console.log(index2);
							console.log(value2);
							console.log(value2.code);
							i++
							str2 += self.liItemTemp.replace('$data-id', value2.id).replace('$name', value2.code);
						});
						console.log(str2);
						str += self.listItemTemp.replace('$company', index1).replace('$lilist', str2)
								   .replace('$num', i);
						str2 = '', i = 0;
					});
					console.log(str);
					$('.client_list').html(str);
					$('.client_item').on('click', function(){
						if($(this).hasClass('active')){
							$(this).removeClass('active');
						}else{
							$(this).addClass('active');
						}
					});
				}
			});
		});
	}
};
$(function(){
	ScriptObject.search();
	// 代金券点击
	$('.coupon_list a').on('click', function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
		var arr = [];
		$('.coupon_list a.active').each(function(){
			var id = $(this).attr('data-id');
			arr.push(id);
		});
		$('input[name=coupon_id]').val(arr);
	});
	// 点击Title隐藏或者显示
	$('.list_item .title').on('touchend', function(){
		if($(this).parent('.list_item').hasClass('open')){
			$(this).parent('.list_item').removeClass('open');
			$(this).parent('.list_item').find('.list_item_ul').hide();
			$(this).parent('.list_item').find('.icon').addClass('glyphicon-chevron-up')
				   .removeClass('glyphicon-chevron-down');
		}else{
			$(this).parent('.list_item').addClass('open');
			$(this).parent('.list_item').find('.list_item_ul').show();
			$(this).parent('.list_item').find('.icon').addClass('glyphicon-chevron-down')
				   .removeClass('glyphicon-chevron-up');
		}
	});
	// 代金券Input获取焦点时弹出模态框选择
	$('input[name=coupon_name]').focus(function(){
		$('#mb_layer').removeClass('hide');
	});
	$('.client_item').on('click', function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
	});
	// 代金券选择确认并关闭模态框
	$('.confirm_close').on('click', function(){
		var str = [], strName = [];
		$('.client_item.active').each(function(){
			var id   = $(this).find('a').attr('data-id');
			var name = $(this).find('.name').text();
			str.push(id);
			strName.push(name);
		});
		$('input[name=coupon_id]').val(str);
		$('input[name=coupon_name]').val(strName);
		$('#mb_layer').addClass('hide');
	});
	// 提交按钮
	$('.submit button').on('click', function(){
		var data = $('#form').serialize();
		ThisObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ThisObject.object.loading.complete();
				console.log(r);
				if(r.data.status){
					ThisObject.object.toast.toast('添加成功！');
					window.location.href = r.__return__;
				}else{
					ThisObject.object.toast.toast('添加失败！');
				}
			},
		})
	});
});

