/**
 * Created by qyqy on 2016-11-26.
 */
var ScriptObject = {
	employeeListTemp:'<tr><td class="check_item_e"><input type="checkbox" class="icheck" value="$id" placeholder=""></td><td>$num</td><td class="name">$name</td><td>$gender</td><td>$department</td><td>$position</td><td>$mobile</td><td>$company</td></tr>',
	setClentlist    :function(){
		var self = this;
		
	},
	setRoleList     :function(){
	},
}
$(function(){
	$('.preview').on('click', function(){
		var href = $(this).parent().find('#url').val();
		window.open(href);
	});
	$('#role').focus(function(){
		$('#add_role').modal('show');
	});
	// 全选checkbox 角色
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 选择角色确认
	$('#add_role').find('.modal-footer button').on('click', function(){
		var arrId = [], arrName = [];
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id   = $(this).find('.icheck').val();
			var name = $(this).find('.icheck').attr('data-name');
			arrId.push(id);
			arrName.push(name);
		});
	});
	$('#employee').focus(function(){
		$('#add_employee').modal('show');
	});
});
