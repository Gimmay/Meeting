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
	// 修改
	$('.alter_btn').on('click', function(){
		var id          = $(this).parent('.btn-group').attr('data-id');
		var $this_modal = $('#alter_hotel');
		$this_modal.find('input[name=id]').val(id);
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_hotel', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				$this_modal.find('#name_a').val(r.name);
				$this_modal.find('#level_a').val(r.level);
				$this_modal.find('#type_a').val(r.type);
				$this_modal.find('#address_a').val(r.address);
				$this_modal.find('#contact_a').val(r.contact);
				$this_modal.find('#brief_a').val(r.brief);
				$this_modal.find('#room_comment_a').val(r.comment);
				$this_modal.find('.btn-save').on('click', function(){
					var data = $this_modal.find('form').serialize();
					ManageObject.object.loading.loading();
					Common.ajax({
						data    :data,
						callback:function(r){
							ManageObject.object.loading.complete();
							if(r.status){
								ManageObject.object.toast.toast(r.message, '1');
								ManageObject.object.toast.onQuasarHidden(function(){
									location.reload();
								});
							}else{
								ManageObject.object.toast.toast(r.message, '2');
							}
						}
					});
				});
			}
		})
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
	// 创建酒店
	$('#create_hotel .btn-save').on('click', function(){
		if(checkIsEmpty()){
			var data = $('#create_hotel form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			});
		}
	});
});
function checkIsEmpty(){
	var name = $('#create_hotel').find('#name').val();
	if(name == ''){
		ManageObject.object.toast.toast('酒店名称不能为空！');
		return false;
	}
	return true;
}
function checkIsEmptyAlter(){
	var name = $('#alter_hotel').find('#name_a').val();
	if(name == ''){
		ManageObject.object.toast.toast('酒店名称不能为空！');
		return false;
	}
	return true;
}