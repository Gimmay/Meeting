/**
 * Created by 1195 on 2016-12-2.
 */
var ScriptObject = {
	clientLiTemp      :'<tr>\n\t<td class="check_item_i">\n\t\t<input type="checkbox" class="icheck_i" value="$id" placeholder="">\n\t</td>\n\t<td>$num</td>\n\t<td>$unit</td>\n\t<td class="name">$name</td>\n\t<td>$clientType</td>\n\t<td>$gender</td>\n\t<td>$position</td>\n\t<td>$mobile</td>\n\t<td>\n\t\t<button type="button" class="btn btn-default btn-xs add_btn" data-id="$cid">添加</button>\n\t</td>\n</tr>',
	liTemp            :'<li class="coupon_number_item"><span>$number</span></li>',
	num               :0,
	selectedMemberTemp:'<div class="col-sm-2 btn-item mb_10">\n\t<button type="button" class="btn btn-sm btn-default btn-block" data-id="$id">$name</button>\n\t<span class="b_delete glyphicon glyphicon-remove"></span>\n</div>',
	bindEvent         :function(){
		var self = this;
		$('.btn-item').find('.b_delete').on('click', function(){
			var $this       = $(this);
			var $this_modal = $('#member_modal');
			var cid         = $this.parent().find('.btn').attr('data-id');
			var id          = $this_modal.find('input[name=id]').val();
			self.deleteMember($this, cid, id);
		});
	},
	unbindEvent       :function(){
		$('.btn-item').find('.b_delete').on('off');
	},
	initGroupList     :function(id){
		var _self = this;
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_member', id:id},
			callback:function(r){
				console.log(r);
				var str = '';
				ManageObject.object.loading.complete();
				$.each(r, function(index, value){
					str += _self.selectedMemberTemp.replace('$id', value.id).replace('$name', value.name)
				});
				$('#group_list').html(str);
				_self.unbindEvent();
				_self.bindEvent();
			}
		});
	},
	deleteMember      :function(_this, cid, id){
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'remove_member', cid:cid, id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					_this.parent().remove();
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		})
	}
};
$(function(){
	ScriptObject.bindEvent();
	//单个新增和批量新增
	$('#create_way li').on('click', function(){
		var index = $(this).index();
		if(index == 0){
			$('.single_box').removeClass('hide');
			$('.mutil_box').addClass('hide');
		}else{
			$('.single_box').addClass('hide');
			$('.mutil_box').removeClass('hide');
		}
		$('#create_way li').removeClass('active');
		$(this).addClass('active');
		if(index == 0){
			$('#add_group_modal').find('input[name=requestType]').val('add_group');
			ScriptObject.num = 0;
			$('#add_group_modal .submit').on('click');
		}
		if(index == 1){
			$('#add_group_modal').find('input[name=requestType]').val('batch_create');
			ScriptObject.num = 1;
			$('#add_group_modal .submit').off('click');
		}
	});
	// 自动批量获取组号
	$('.auto_get_number').on('click', function(){
		try{
			var $prefix       = $('#prefix');
			var $start_number = $('#start_number');
			var $number       = $('#number');
			var $lenght       = $('#length');
			var suffix        = $('#suffix').val();
			var str           = '';
			var str2          = '';
			for(var i = 0; i<$number.val(); i++){
				var len       = $lenght.val();
				var s_number  = $start_number.val();
				var n         = Number(s_number)+Number(i);
				var realLen   = len-$prefix.val().length;
				var aa        = PrefixInteger(n, realLen-1);
				str += ($prefix.val()+''+aa)+suffix+',';
				var li_number = $prefix.val()+''+aa+suffix;
				str2 += ScriptObject.liTemp.replace('$number', li_number);
			}
			$('.list_coupon_number').html(str2);
			var s, newStr = "";
			s             = str.charAt(str.length-1);
			if(s == ","){
				for(var i = 0; i<str.length-1; i++){
					newStr += str[i];
				}
			}
			$('#add_group_modal').find('input[name=group_name]').val(newStr);
		}catch(error){
			console.log(error);
			ManageObject.object.toast.toast('自动获取组号参数不符合，重新填写');
		}
	});
	// 确认新增组别
	$("#add_group_modal .btn-save").on('click', function(){
		var data = $('#add_group_modal form').serialize();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :data,
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.toast(r.message, '1');
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload();
					})
				}else{
					ManageObject.object.toast.toast(r.message, '2');
				}
			}
		});
	});
	// 搜索组员
	$('.ajax_search').on('click', function(){
		var keyword = $('#add_member').find('input[name=keyword]').val();
		var id      = $('#add_member').find('input[name=gid]').val();
		getClient(id, keyword);
	});
	// 查看所有组员
	$('.ajax_search_all').on('click', function(){
		var id = $('#add_member').find('input[name=gid]').val();
		getClient(id, '');
	});
	// 选择组员确认按钮
	$('#add_member').find('.btn_save').on('click', function(){
		var group_member_count = $('#add_member').find('#group_member_count').val();
		var group_capacity     = $('#add_member').find('#group_capacity').val();
		var arr                = [];
		var gid                = $(this).attr('data-id');
		$('.check_item').find('.icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			arr.push(id);
		});
		var str_string = arr.toString(); //数组转化为字符串
		console.log(str_string);
		ManageObject.object.loading.loading();
		if(arr != ''){
			if(group_member_count<group_capacity){
				Common.ajax({
					data    :{requestType:'save_client', id:str_string, gid:gid},
					callback:function(data){
						ManageObject.object.loading.complete();
						console.log(data);
						if(data.status){
							ManageObject.object.toast.toast('添加成功！');
							$('#add_member').modal('hide');
							location.reload();
						}
					}, error:function(){
						ManageObject.object.toast.toast('添加失败');
					}
				});
			}else{
				ManageObject.object.loading.complete();
				ManageObject.object.toast.toast('该组组员大于可容纳人数！');
			}
		}else{
			ManageObject.object.loading.complete();
			ManageObject.object.toast.toast('添加失败，未选择员工！');
		}
	});
	// 删除点击的组员
	$('.group_con a .g_close').on('click', function(){
		var cid = $(this).attr('data-id');
		$(this).parent().remove();
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'delete_client', cid:cid},
			callback:function(r){
				ManageObject.object.loading.complete();
				console.log(r);
				if(r.status){
					ManageObject.object.toast.toast('组员删除完成！');
				}
			}
		});
	});
	// 组员管理
	$('.manage_btn').on('click', function(){
		var id         = $(this).parent().attr('data-id');
		var $obj_modal = $('#member_modal');
		$obj_modal.find('.btn_create').attr('data-id', id);
		$obj_modal.find('.btn_empty').attr('data-id', id);
		$obj_modal.find('input[name=id]').val(id);
		ScriptObject.initGroupList(id);
	});
	// 组员列表管理
	$('#member_modal .btn_create').on('click', function(){
		var id = $(this).attr('data-id');
		$('#add_member input[name=gid]').val(id);
		$('#add_member').modal('show');
		getClient(id, '');
	});
	// 清空组员
	$('#member_modal .btn_empty').on('click', function(){
		$('#empty_modal').modal('show');
		var id = $(this).attr('data-id');
		$('#empty_modal .btn-save').on('click', function(){
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'remove_all_member', id:id},
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						$('#member_modal #group_list').empty();
						$('#empty_modal').modal('hide');
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			})
		});
		/*var gid = $(this).attr('data-id');
		 $('#empty_modal').find('input[name=gid]').val(gid);
		 var data = $('#empty_modal form').serialize();
		 ManageObject.object.loading.loading();
		 Common.ajax({
		 data    :data,
		 callback:function(r){
		 ManageObject.object.loading.complete();
		 if(r.status){
		 ManageObject.object.toast.toast(r.message, '1');

		 $('#member_modal #group_list').empty();
		 //	$('#empty_modal').modal('hide');
		 }else{
		 }
		 }
		 })*/
	});
	// 修改组 -- 获取组信息
	$('.modify_btn').on('click', function(){
		var $this_modal = $('#alter_group_modal');
		$this_modal.modal('show');
		var id = $(this).parent().attr('data-id');
		$this_modal.find('input[name=id]').val(id);
		Common.ajax({
			data    :{requestType:'get_group', id:id},
			callback:function(r){
				ManageObject.object.loading.complete();
				$this_modal.find('#code_alter').val(r.name);
				$this_modal.find('#capacity_alter').val(r.capacity);
				$this_modal.find('#comment_alter').val(r.comment);
			}
		});
	});
	// 修改组
	$('#alter_group_modal .btn-save').on('click', function(){
		if(checkIsEmptyAlter()){
			var data = $('#alter_group_modal form').serialize();
			Common.ajax({
				data    :data,
				callback:function(r){
					ManageObject.object.loading.complete();
					console.log(r);
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							location.reload();
						})
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			})
		}
	});
	// 批量添加组员
	$('#add_member .batch_add_btn').on('click', function(){
		var cid = $('#add_member input[name=selected_member]').val(); //客户id
		var id  = $('#add_member input[name=gid]').val(); //组ID
		if(cid != ''){
			Common.ajax({
				data    :{requestType:'add_member', cid:cid, id:id},
				callback:function(r){
					ManageObject.object.loading.complete();
					if(r.status){
						ManageObject.object.toast.toast(r.message, '1');
						ManageObject.object.toast.onQuasarHidden(function(){
							$('.check_item_i').find('.icheckbox_square-blue.checked').each(function(){
								$(this).parents('tr').remove();
							});
						});
						$('#add_member').find('input[name=selected_member]').val('');
					}else{
						ManageObject.object.toast.toast(r.message, '2');
					}
				}
			});
		}else{
			ManageObject.object.toast.toast('请选择客户！', 2);
		}
	});
	// 关闭客户列表的模态框局部加载已选组员列表
	$('#add_member').on('hide.bs.modal', function(){
		var id = $('#add_member').find('input[name=gid]').val();
		ScriptObject.initGroupList(id);
	});
	// 关闭组员管理模态框刷新页面
	$('#member_modal').on('hide.bs.modal', function(){
		location.reload();
	});
});
function all_check(){
	// 全选checkbox
	$('.all_check_i').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-blue').hasClass('checked')){
			$('.check_item_i').find('.icheckbox_square-blue').addClass('checked');
		}else{
			$('.check_item_i').find('.icheckbox_square-blue').removeClass('checked');
		}
		var str = [];
		$('.check_item_i').find('.icheckbox_square-blue.checked').each(function(){
			var id = $(this).find('.icheck_i').val();
			str.push(id);
		});
		console.log(str);
		$('#add_member').find('input[name=selected_member]').val(str);
	});
	$('.check_item_i').find('.iCheck-helper').on('click', function(){
		var str = [];
		$('.check_item_i').find('.icheckbox_square-blue.checked').each(function(){
			var id = $(this).find('.icheck_i').val();
			str.push(id);
		});
		console.log(str);
		$('#add_member').find('input[name=selected_member]').val(str);
	});
}
//  num传入的数字，n需要的字符长度
function PrefixInteger(num, n){
	return (Array(n).join(0)+num).slice(-n);
}
// 获取客户列表
function getClient(id, keyword){
	Common.ajax({
		data    :{requestType:'get_client', id:id, keyword:keyword},
		callback:function(r){
			console.log(r);
			var str    = '';
			var gender = '';
			var i      = 0;
			$.each(r, function(index, value){
				i++;
				if(value.gender == 0){
					gender = '未知';
				}else if(value.gender == 1){
					gender = '男';
				}else if(value.gender == 2){
					gender = '女';
				}
				str += ScriptObject.clientLiTemp.replace('$id', value.cid).replace('$cid', value.cid)
								   .replace('$num', index+1)
								   .replace('$name', value.name).replace('$gender', gender)
								   .replace('$position', value.position).replace('$mobile', value.mobile)
								   .replace('$unit', value.unit).replace('$clientType', value.type);
			});
			$('#add_member').find('.current_attendee').text(i);
			$('#add_member').find('#client_body').html(str);
			$('#client_body').scroll(function(){
				console.log('zzz');
			});
			$('.icheck_i').iCheck({
				checkboxClass:'icheckbox_square-blue',
				radioClass   :'iradio_square-blue'
			});
			all_check();
			$('.add_btn').on('click', function(){
				var cid   = $(this).attr('data-id');
				var _self = $(this);
				ManageObject.object.loading.loading();
				Common.ajax({
					data    :{
						requestType:'add_member', cid:cid, id:id
					},
					callback:function(r){
						ManageObject.object.loading.complete();
						if(r.status){
							ManageObject.object.toast.toast(r.message, '1');
							ManageObject.object.toast.onQuasarHidden(function(){
								_self.parents('tr').remove();
							})
						}else{
							ManageObject.object.toast.toast(r.message, '2');
						}
					}
				})
			});
		}
	});
}
function checkIsEmpty(){
	if($('#code').val() == ''){
		ManageObject.object.toast.toast('组号不能为空！');
		$('#code').focus();
		return false;
	}
	return true;
}
function checkIsEmptyAlter(){
	if($('#code_alter').val() == ''){
		ManageObject.object.toast.toast('组号不能为空！');
		$('#code_alter').focus();
		return false;
	}
	return true;
}