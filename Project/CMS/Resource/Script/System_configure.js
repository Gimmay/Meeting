$(function() {
    /** 点击保存按钮 触发*/
	$('#create_configure .btn_save').click(function(){
        var title =   $("#title").val();
		var name = $("#name").val();
		var conf_type = $(" input[ name='conf_type']:checked ").val();
		var order = $("#order").val();
		var description = $("#description").val();
		Common.ajax({
			data    :{requestType:'add_system_config', conf_title:title,conf_name:name,conf_type:conf_type,orders:order,description:description},
			callback:function(data){
				if(data.status){
					$('#create_configure').modal('hide');
					ConfigObject.object.toast.toast('配置项添加成功！');
					location.reload();
				}else{
					$('#create_configure').modal('hide');
					ConfigObject.object.toast.toast('配置项添加失败！');
					location.reload();
				}
			}
		})
	});
	/** 点击删除按钮 给 隐藏框赋值 所赋值为 配置项 id*/
	$('.delete_btn').click(function(){
		var id =$(this).attr("id");
		$('#need_delete_id').val(id);
	});
	$('#delete_configure .btn_delete').click(function(){
		var need_delete_id = $("#need_delete_id").val();
		Common.ajax({
			data    :{requestType:'delete_config', id:need_delete_id},
			callback:function(data){
				if(data.status){
					$('#delete_configure').modal('hide');
					ConfigObject.object.toast.toast('配置删除成功！');
					setTimeout("location.reload();",1000)

				}else{
					$('#delete_configure').modal('hide');
					ConfigObject.object.toast.toast('配置删除失败！');
					setTimeout("location.reload();",1000)
				}
			}
		})
	});
	/** 修改配置项 获取id*/
	$('.authorize_btn').click(function(){
		var id =$(this).attr("data-id");
		Common.ajax({
			data    :{requestType:'find_config_by_id', id:id},
			callback:function(data){
				if(data.status){//给修改框各项 赋值
					//$('#delete_configure').modal('hide');
					//ConfigObject.object.toast.toast(data.data.conf_name);
					$('#edit_id').val(data.data.id);
					$('#edit_title').val(data.data.conf_title);
					$('#edit_name').val(data.data.conf_name);
					$('#edit_order').val(data.data.orders);
					$('#edit_description').val(data.data.description);
					//setTimeout("location.reload();",1000)

				}else{
					//$('#delete_configure').modal('hide');
					//ConfigObject.object.toast.toast('配置删除失败！');
					//setTimeout("location.reload();",1000)
				}
			}
		})
		//$('#need_delete_id').val(id);
		//alert(id);
	});
	/** 修改框 点击保存按钮 触发*/
	$('#edit_configure .btn_save').click(function(){
		var edit_id =   $("#edit_id").val();
		var edit_title =   $("#edit_title").val();
		var edit_name = $("#edit_name").val();
		var edit_conf_type = $(" input[ name='edit_conf_type']:checked ").val();
		if(edit_conf_type == undefined){
			ConfigObject.object.toast.toast('请选择配置类型！');
			return false;
		}
		var edit_order = $("#edit_order").val();
		var edit_description = $("#edit_description").val();
		Common.ajax({
			data    :{requestType:'save_edit_config', id:edit_id,conf_title:edit_title,conf_name:edit_name,conf_type:edit_conf_type,orders:edit_order,description:edit_description},
			callback:function(data){
				if(data.status){
					$('#edit_configure').modal('hide');
					ConfigObject.object.toast.toast('配置项添加成功！');
					setTimeout("location.reload();",1000)
				}else{
					$('#edit_configure').modal('hide');
					ConfigObject.object.toast.toast('配置项添加失败！');
					setTimeout("location.reload();",1000)
				}
			}
		})
	});
});
/** 改变排序*/
function changeOrder(obj,id){
	var orders = $(obj).val();
	Common.ajax({
		data    :{requestType:'change_config_order', id:id,orders:orders},
		callback:function(data){
			if(data.status){
				ConfigObject.object.toast.toast('排序更改成功！');
				setTimeout("location.reload();",1000)

			}else{
				ConfigObject.object.toast.toast('排序更改失败！');
				setTimeout("location.reload();",1000)
			}
		}
	})
}
/** uploadify  uploader 处理路径*/
$(function() {
	$('#file_upload').uploadify({
		'buttonText' : '上传图片',
		'formData'     : {
			'requestType' : "upload_img",
		},
		'swf'      : "/Public/Vendor/uploadify/uploadify.swf",
		'uploader' : "/CMS/System/configure",
		'onUploadSuccess' : function(file, data, response) {
			var data1 = $.parseJSON(data);
			//console.log(data1);
			$('#thumb').val(data1.path);
		}

	});
});