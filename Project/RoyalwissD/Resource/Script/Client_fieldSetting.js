/**
 * Created by Iceman on 2017-3-11.
 */
var ThisObject = {
	bindEvent  :function(){
		/**
		 *  字段扩展选择
		 */
		$('.btn-item button').on('click', function(){
			fieldsExtendSelect($(this));
		});
		/**
		 * 去除已选字段
		 */
		$('#selected_fields_form').find('.f_delete').on('click', function(){
			deleteSelectedField($(this));
		});
		/**
		 * 已选字段编辑
		 */
		$('#selected_fields_form').find('.f_edit').on('click', function(){
			editSelectedField($(this));
		});
		/*/!**
		 * 删除扩展字段
		 *   暂时关闭改功能 2017/3/13
		 *!/
		 $('.btn-item .b_delete').on('click', function(){
		 var _self = $(this);
		 deleteSelectedField(_self);
		 });*/
	},
	unbindEvent:function(){
		$('.btn-item button').off('click');
		$('#selected_fields_form').find('.f_delete').off('click');
		$('#selected_fields_form').find('.f_edit').off('click');
		$('.btn-item .b_delete').off('click');
		$('#delete_selected_field_modal .btn-save').off('click');
	}
};
$(function(){
	ThisObject.bindEvent();
	/**
	 * 自增字段保存
	 */
	$('#add_field_modal').find('.btn-save').on('click', function(){
		var text = $('#field_name').val();
		var data = $('#add_field_modal').find('form').serialize(); // 表单序列化
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(e){
				ManageObject.object.loading.complete();
				if(e.status){
					$('.increment_fields')
					.append('<div class="col-sm-2 btn-item mb_10">\n\t<button type="button" class="btn btn-sm btn-default btn-block">'+text+'</button>\n\t<span class="b_delete glyphicon glyphicon-remove"></span>\n</div>');
					$('#add_field_modal').modal('hide');
					ThisObject.unbindEvent();
					ThisObject.bindEvent();
				}
			}
		});  // ajax 提交字段
	});
	/**
	 * 根据浏览器窗口分辨率判断字段显示列数
	 */
	if(1599>window.innerWidth){
		$('.base_fields').find('.btn-item').each(function(){
			$(this).addClass('col-sm-4').removeClass('col-sm-2 col-sm-3');
		});
		$('.increment_fields').find('.btn-item').each(function(){
			$(this).addClass('col-sm-4').removeClass('col-sm-2 col-sm-3');
		});
	}
});
/**
 * 字段扩展选择
 * @param e
 */
function fieldsExtendSelect(e){
	var name = e.attr('data-name');
	if(!e.parent().hasClass('active')){  // 如果active类不存在，则添加acrtive类，更改样式。
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'field_extend_select', name:name},
			callback:function(res){
				ManageObject.object.loading.complete();
				if(res.status){
					var text = e.text();
					e.parent().addClass('active');
					e.addClass('btn-info').removeClass('btn-default');
					// 将选择的扩展字段添加到字段预览框。
					$('.selected_fields').find('#selected_fields_form')
										 .append("<div class=\"form-group\">\n\t<label for=\"text\" class=\"col-sm-12 mb_5\">"+text+"</label>\n\t<div class=\"col-sm-10\">\n\t\t<input type=\"email\" class=\"form-control\" id=\"text\" readonly=\"readonly\">\n\t</div>\n\t<div class=\"col-sm-2\">\n\t\t<span class=\"f_edit\">\n\t\t\t<i class=\"glyphicon glyphicon-edit\"></i>\n\t\t</span>\n\t\t<span class=\"f_delete\">\n\t\t\t<i class=\"glyphicon glyphicon-trash\"></i>\n\t\t</span>\n\t</div>\n</div>");
					ThisObject.unbindEvent();
					ThisObject.bindEvent();
					ManageObject.object.toast.toast('操作成功！');
				}
			}
		});
	}
}
/**
 * 去除已选字段函数
 * @param e
 */
function deleteSelectedField(e){
	$('#delete_selected_field_modal').modal('show');
	var name = e.parents('.form-group').find('input').attr('name');
	ThisObject.unbindEvent();
	ThisObject.bindEvent();
	$('#delete_selected_field_modal .btn-save').on('click', function(){
		ManageObject.object.loading.loading();
		Common.ajax({  // ajax提交
			data    :{requestType:'delete_selected_field', name:name},
			callback:function(res){
				ManageObject.object.loading.complete();
				if(res.status){
					$('.btn-item ').each(function(){   //遍历字段扩展的内容，与删除的内容对比，相等则去除选择样式
						var btn_name = $(this).find('button').attr('data-name');
						if(name == btn_name){
							$(this).removeClass('active').find('button').removeClass('btn-info')
								   .addClass('btn-default');
						}
					});
					e.parents('.form-group').remove();
					$('#delete_selected_field_modal').modal('hide');
					ManageObject.object.toast.toast('操作成功！');
				}
			}
		});
	});
}
/**  暂时移除
 * 删除扩展字段函数
 * @param e
 */
/*function deleteExtendField(e){
 var name = e.parents('.form-group').find('input').attr('name');
 $('#delete_extend_field_modal').modal('show');
 $('#delete_extend_field_modal .btn-save').on('click', function(){
 e.parent().remove();
 $('#delete_extend_field_modal').modal('hide');
 });
 }*/
/**
 * 编辑已选字段函数
 * @param e
 */
function editSelectedField(e){
	var name      = e.parents('.form-group').find('input').attr('name');
	var necessary = e.parents('.form-group').find('input').attr('data-is-necessary');
	if(necessary == '1'){
		$('#edit_selected_field .icheckbox_square-green').addClass('checked');
		$('#edit_selected_field .icheckbox_square-green').find('input').prop('checked', true);
	}
	$('#edit_selected_field').find('input[name=name]').val(name);
	$('#edit_selected_field').modal('show');
	/**
	 * 字段编辑
	 */
	$('#edit_selected_field .btn-save').off().on('click', function(){
		var form_data = $('#edit_selected_field form').serialize();
		console.log(form_data);
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :form_data,
			callback:function(res){
				ManageObject.object.loading.complete();
				console.log($('#edit_selected_field').find('input[name=is_necessary]').val());
				if(res.status){
					$('#edit_selected_field').modal('hide');
					ManageObject.object.toast.toast('编辑成功！');
					if(necessary == '1'){
						e.parents('.form-group').find('.color-red').remove();
						e.parents('.form-group').find('input').attr('data-is-necessary', '0');
					}else if(necessary == '0'){
						e.parents('.form-group').find('input').attr('data-is-necessary', '1');
						e.parents('.form-group').find('label').append('<b class="color-red">*</b>');
					}
				}else{
					ManageObject.object.toast.toast('编辑失败！');
				}
			}
		});
	});
}