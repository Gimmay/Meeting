/**
 * Created by 1195 on 2016-10-25.
 */


$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 单个删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_hotel ').find('input[name=id]').val(id);
	});
	// 批量删除
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_hotel').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_delete_hotel').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择！');
		}
		$('#batch_delete_hotel').find('input[name=id]').val(newStr);
	});
	// 修改
	$('.alter_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#alter_hotel ').find('input[name=id]').val(id);
		var hotel_name = $(this).parents('tr').find('.hotel_name').text();
		var level      = $(this).parents('tr').find('.level').text();
		var type       = $(this).parents('tr').find('.type').text();
		var address    = $(this).parents('tr').find('.address').text();
		var contact    = $(this).parents('tr').find('.contact').text();
		$('#alter_hotel').find('#name_a').val(hotel_name);
		$('#alter_hotel').find('#level_a').val(level);
		$('#alter_hotel').find('#type_a').val(type);
		$('#alter_hotel').find('#address_a').val(address);
		$('#alter_hotel').find('#contact_a').val(contact);
		Common.ajax({
			data    :{requestType:'get_hotel', id:id},
			callback:function(r){
				$('#alter_hotel').find('#brief_a').val(r.brief);
				$('#alter_hotel').find('#room_comment_a').val(r.comment);
			}
		})
	});
	// 点击过滤标签-全部
	$('#filter_btn_all').on('click', function(){
		var new_url = url_object.delUrlParam('type');
		location.replace(new_url);
	});
	// 点击过滤标签-使用中
	$('#filter_btn_using').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'using');
		location.replace(new_url);
	});
	// 点击过滤标签-未使用
	$('#filter_btn_not_use').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'not_use');
		location.replace(new_url);
	});
	// 点击过滤标签-已结束
	$('#filter_btn_finish').on('click', function(){
		var new_url = url_object.setUrlParam('type', 'finish');
		location.replace(new_url);
	});
	// 选择酒店
	$('.choose_hotel').on('click', function(){
		var type = $(this).attr('data-type');
		var id   = $(this).attr('data-id');
		ManageObject.object.loading.loading();
		/**
		 * step 1:该按钮已被选中，type值为1，状态已选择，点击后取消选择
		 * step 2:该按钮未被选中，type值为0，状态未选择，点击后选择
		 */
		var self_e = $(this);
		if($(this).hasClass('active')){
			Common.ajax({
				data    :{requestType:'cancel_choose', type:type, id:id},
				callback:function(r){
					ManageObject.object.loading.complete();
					console.log(r);
					if(r.status){
						ManageObject.object.toast.toast('取消选择成功！');
						self_e.removeClass('active');
						self_e.attr('data-type', '0').text('选择');
					}else{
						ManageObject.object.toast.toast('取消选择失败！');
					}
				}
			});
		}else{
			Common.ajax({
				data    :{requestType:'choose', type:type, id:id},
				callback:function(r){
					ManageObject.object.loading.complete();
					console.log(r);
					if(r.status){
						ManageObject.object.toast.toast('选择成功！');
						self_e.addClass('active');
						self_e.attr('data-type', '1').text('已选择');
					}else{
						ManageObject.object.toast.toast('取消选择失败！');
					}
				}
			});
		}
	});
});