/**
 * Created by Iceman on 2017-3-15.
 * 共用的操作 禁启用操作、批量禁启用操作、批量删除操作、删除操作、表格列表全选
 */

$(function(){
	/**
	 * 禁启用操作
	 */
	$('.btn-apply').on('click', function(){
		var status = $(this).parent().attr('data-status');
		var id     = $(this).parent().attr('data-id');
		ManageObject.object.loading.loading();
		if(status == '1'){
			Common.ajax({
				data    :{requestType:'disable', id:id, status:status},
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			})
		}else if(status == '0'){
			Common.ajax({
				data    :{requestType:'enable', id:id, apply:status},
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			})
		}
	});
	/**
	 * 批量启用
	 */
	$('.batch_enable_btn').on('click', function(){
		var str = [], i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		str = str.join(',');
		if(str != ''){
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'enable', id:str},
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		}else{
			ManageObject.object.toast.toast('请选择列表项！');
		}
	});
	/**
	 * 批量禁用
	 */
	$('.batch_disable_btn').on('click', function(){
		var str = [], i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		str = str.join(',');
		if(str != ''){
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'disable', id:str},
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		}else{
			ManageObject.object.toast.toast('请选择列表项！');
		}
	});
	/**
	 * 删除操作
	 */
	$('.delete_btn').on('click', function(){
		var id = $(this).parent().attr('data-id');
		$('#delete_modal').find('input[name=id]').val(id);
		$('#delete_modal .btn-save').on('click', function(){
			var data = $('#delete_modal form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		});
	});
	/**
	 *  批量删除项目
	 */
	$('.batch_delete_btn').on('click', function(){
		var str = [], i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str.push(id);
			i++;
		});
		$('#batch_delete_modal').find('.sAmount').text(i);
		if(str != ''){
			$('#batch_delete_modal').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择列表项！');
		}
		$('#batch_delete_modal').find('input[name=id]').val(str);
		$('#batch_delete_modal .btn-save').on('click', function(){
			var data = $('#batch_delete_modal form').serialize();
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :data,
				callback:function(res){
					ManageObject.object.loading.complete();
					if(res.status){
						ManageObject.object.toast.toast(res.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						});
					}else{
						ManageObject.object.toast.toast(res.message, '2');
					}
				}
			});
		});
	});
	/**
	 * 全选checkbox
	 * 1、全选选中事件 ifChecked: 所有列表都选中
	 * 2、全选未选中事件 ifUnchecked: 所有列表都取消选中
	 */
	$('.all_check .icheck').on('ifChecked', function(event){
		$('.check_item .icheck').iCheck('check');
	});
	$('.all_check .icheck').on('ifUnchecked', function(event){
		$('.check_item .icheck').iCheck('uncheck');
	});
	/**
	 * 配置
	 */
	$('#configure_modal .btn-save').on('click', function(){
		var data = $('#configure_modal form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(res){
				ManageObject.object.loading.complete();
				if(res.status){
					ManageObject.object.toast.toast(res.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					});
				}else{
					ManageObject.object.toast.toast(res.message, '2');
				}
			}
		});
	});
	/**
	 * 搜索功能
	 */
	var quasar_script = document.getElementById('quasar_script');
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	/**
	 * 列表搜索
	 */
	$('#main_search').on('click', function(){
		var keyword = $('#keyword').val();
		var new_url = url_object.delUrlParam('p');
		if(keyword == ''){
			new_url = url_object.delUrlParam('keyword', new_url);
		}else{
			keyword = encodeURI(keyword);
			keyword = encodeURI(keyword);
			new_url = url_object.setUrlParam('keyword', keyword, new_url);
		}
		location.replace(new_url);
	});
	/**
	 * 回车搜索
	 */
	$('#keyword').on('keydown', function(e){
		if(e.keyCode == 13){
			var keyword = $('#keyword').val();
			var new_url = url_object.delUrlParam('p');
			if(keyword == ''){
				new_url = url_object.delUrlParam('keyword', new_url);
			}else{
				keyword = encodeURI(keyword);
				keyword = encodeURI(keyword);
				new_url = url_object.setUrlParam('keyword', keyword, new_url);
			}
			location.replace(new_url);
		}
	});
});