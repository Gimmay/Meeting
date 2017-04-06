/**
 * Created by qyqy on 2016-11-28.
 */
var ScriptObject = {
	employeeListTemp:'<tr>\n\t<td>$num</td>\n\t<td class="name">$name</td>\n\t<td>$gender</td>\n\t<td>$department</td>\n\t<td>$position</td>\n\t<td>$mobile</td>\n\t<td>$company</td>\n\t<td>\n\t\t<div class="btn-group" data-id="$id">\n\t\t\t<button type="button" class="btn btn-default btn-xs choose_employee not_choose">选择</button>\n\t\t</div>\n\t</td>\n</tr>',
	roleListTemp    :'<tr>\n\t<td>$num</td>\n\t<td class="name">$name</td>\n\t<td>$level</td>\n\t<td>$comment</td>\n\t<td>\n\t\t<div class="btn-group" data-id="$id">\n\t\t\t<button type="button" class="btn btn-default btn-xs choose_role not_choose">选择</button>\n\t\t</div>\n\t</td>\n</tr>',
	setEmployeeList :function(keyword, type){
		var self = this;
		CreateObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_employee', keyword:keyword},
			callback:function(r){
				CreateObject.object.loading.complete();
				console.log(r);
				var str = '', genderVal = '';
				if(type == true){
					$('#add_employee').find('.has_select').html('<a data-id="-1" class="hide"></a>');
					var selectedArr = $('#employee_arr').val().split(",");
					$.each(r.data, function(index, value){
						if(value.gender == '0'){
							genderVal = '未知';
						}else if(value.gender == '1'){
							genderVal = '男';
						}else if(value.gender == '2'){
							genderVal = '女';
						}
						str += self.employeeListTemp.replace('$num', index+1).replace('$name', value.name)
								   .replace('$gender', genderVal).replace('$department', value.department)
								   .replace('$position', value.position).replace('$mobile', value.mobile)
								   .replace('$company', value.company).replace('$id', value.id);
						$.each(selectedArr, function(index, value1){
							if(value1 == value.id){
								var arrId = [];
								$('#add_employee .has_select a').each(function(){
									var id = $(this).attr('data-id');
									arrId.push(id);
								});
								$('#add_employee .has_select').append('<a data-id='+value.id+'>'+value.name+'</a>');
							}
						});
						$('.has_select a').on('click', function(){
							$(this).remove();
						});
					});
				}else{
					$.each(r.data, function(index, value){
						if(value.gender == '0'){
							genderVal = '未知';
						}else if(value.gender == '1'){
							genderVal = '男';
						}else if(value.gender == '2'){
							genderVal = '女';
						}
						str += self.employeeListTemp.replace('$num', index+1).replace('$name', value.name)
								   .replace('$gender', genderVal).replace('$department', value.department)
								   .replace('$position', value.position).replace('$mobile', value.mobile)
								   .replace('$company', value.company).replace('$id', value.id);
					})
				}
				$('#employee_body').html(str);
				$('.choose_employee').on('click', function(){
					var name = $(this).parents('tr').find('.name').text();
					var id   = $(this).parent('.btn-group').attr('data-id');
					var arr  = [];
					$('#add_employee .has_select a').each(function(){
						var thisId = $(this).attr('data-id');
						arr.push(thisId);
					});
					if(arr.indexOf(id) == -1){
						$('#add_employee .has_select').append('<a data-id='+id+'>'+name+'</a>');
					}else{
						CreateObject.object.toast.toast('该员工已选择！');
					}
					$('.has_select a').on('click', function(){
						$(this).remove();
					});
				});
			}
		});
	},
	setRolelist     :function(keyword, type){
		var self = this;
		CreateObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'get_role', keyword:keyword},
			callback:function(r){
				CreateObject.object.loading.complete();
				console.log(r);
				var str = '';
				if(type == true){
					$('#add_role').find('.has_select').html('<a data-id="-1" class="hide"></a>');
					$.each(r.data, function(index, value){
						str += self.roleListTemp.replace('$num', index+1).replace('$name', value.name)
								   .replace('$level', value.level).replace('$comment', value.comment)
								   .replace('$id', value.id);
						var selectedArr = $('#role_arr').val().split(",");
						$.each(selectedArr, function(index, value1){
							if(value1 == value.id){
								$('#add_role .has_select').append('<a data-id='+value.id+'>'+value.name+'</a>');
							}
						});
						$('.has_select a').on('click', function(){
							$(this).remove();
						});
					});
				}else{
					$.each(r.data, function(index, value){
						str += self.roleListTemp.replace('$num', index+1).replace('$name', value.name)
								   .replace('$level', value.level).replace('$comment', value.comment)
								   .replace('$id', value.id);
					});
				}
				$('#role_body').html(str);
				$('.choose_role').on('click', function(){
					var name = $(this).parents('tr').find('.name').text();
					var id   = $(this).parent('.btn-group').attr('data-id');
					var arr  = [];
					$('#add_role .has_select a').each(function(){
						var thisId = $(this).attr('data-id');
						arr.push(thisId);
					});
					if(arr.indexOf(id) == -1){
						$('#add_role .has_select').append('<a data-id='+id+'>'+name+'</a>');
					}else{
						CreateObject.object.toast.toast('该角色已选择！');
					}
					$('.has_select a').on('click', function(){
						$(this).remove();
					});
				});
			}
		});
	},
}
$(function(){
	$('.preview').on('click', function(){
		var href = $(this).parent().find('#url').val();
		window.open(href);
	});
	$('#role').focus(function(){
		$('#add_role').modal('show');
		ScriptObject.setRolelist('', true);
	});
	$('.role_search').on('click', function(){
		var keyword = $(this).parents('.repertory_text').find('input[name=keyword]').val();
		ScriptObject.setRolelist(keyword, false);
	});
	$('.role_all').on('click', function(){
		ScriptObject.setRolelist('', false);
	});
	$('.save_role').on('click', function(){
		$('#add_role').modal('hide');
		var arr = [], arrId = [];
		$('#add_role .has_select a').each(function(){
			var name = $(this).text();
			var id   = $(this).attr('data-id');
			arr.push(name);
			arrId.push(id);
		});
		arr.shift(); //删掉第一个数组元素
		arrId.shift(); //删掉第一个数组元素
		$('#role').val(arr);
		$('#role_arr').val(arrId);
	});
	$('#employee').focus(function(){
		$('#add_employee').modal('show');
		ScriptObject.setEmployeeList('', true);
	});
	$('.employee_search').on('click', function(){
		var keyword = $(this).parents('.repertory_text').find('input[name=keyword]').val();
		ScriptObject.setEmployeeList(keyword, false);
	});
	$('.employee_all').on('click', function(){
		ScriptObject.setEmployeeList('', false);
	});
	$('.save_employee').on('click', function(){
		$('#add_employee').modal('hide');
		var arr = [], arrId = [];
		$('#add_employee .has_select a').each(function(){
			var name = $(this).text();
			var id   = $(this).attr('data-id');
			arr.push(name);
			arrId.push(id);
		});
		arr.shift(); //删掉第一个数组元素
		arrId.shift(); //删掉第一个数组元素
		$('#employee').val(arr);
		$('#employee_arr').val(arrId);
	});
});
