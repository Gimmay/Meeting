/**
 * Created by qyqy on 2016-9-21.
 */
function checkIsEmpty(){
	var $code   = $('#code');
	var $name   = $('#selected_name');
	var $password = $('#password');
	var $selected_position = $('#selected_position');
	var $selected_department = $('#selected_department');
	var $mobile = $('#mobile');
	if($code.val() == ''){
		CreateObject.object.toast.toast("工号不能为空");
		$code.focus();
		return false;
	}
	if(CreateObject.object.userSelect.getValue() == ''){
		CreateObject.object.toast.toast("姓名不能为空");
		$name.focus();
		return false;
	}
	if($password.val() == ''){
		CreateObject.object.toast.toast("密码不能为空");
		$password.focus();
		return false;
	}

	if($selected_position.text() == ''){
		CreateObject.object.toast.toast("职位不能为空");
		$selected_position.focus();
		return false;
	}
	if($selected_department.text() == ''){
		CreateObject.object.toast.toast("部门不能为空");
		$selected_department.focus();
		return false;
	}
	if($mobile.val() == ''){
		CreateObject.object.toast.toast("手机号不能为空");
		$mobile.focus();
		return false;
	}
	return true;
}

$(function(){
	var $code          = $('#code');
	var $status        = $('#status');
	var $gender        = $('#gender');
	var $mobile        = $('#mobile');
	var $birthday      = $('#birthday');
	var $name_tmp      = $('#oa_user_info_viewer_name');
	var $code_tmp      = $('#oa_user_info_viewer_code');
	var $status_tmp    = $('#oa_user_info_viewer_status_code');
	var $gender_tmp    = $('#oa_user_info_viewer_gender_code');
	var $dept_code_tmp = $('#oa_user_info_viewer_dept_code');
	var $dept_name_tmp = $('#oa_user_info_viewer_dept_name');
	var $position_tmp  = $('#oa_user_info_viewer_position');
	//var $title_tmp    = $('#oa_user_info_viewer_title');
	var $mobile_tmp    = $('#oa_user_info_viewer_mobile');
	var $birthday_tmp  = $('#oa_user_info_viewer_birthday');
	var $modal         = $('#oa_user_info_viewer');
	CreateObject.object.userSelect.onQuasarSelect(function(){
		var data = CreateObject.object.userSelect.getExtend();
		if(data){
			var tmp = data.split('&');
			for(var i = 0; i<tmp.length; i++){
				var single = tmp[i].split('=');
				var key    = single[0];
				var val    = single[1];
				if(key == 'gender'){
					var $gender      = $('#oa_user_info_viewer_'+key);
					var $gender_code = $('#oa_user_info_viewer_'+key+'_code');
					switch(parseInt(val)){
						case 0:
							$gender.html('男');
							$gender_code.val(1);
							break;
						case 1:
							$gender.html('女');
							$gender_code.val(2);
							break;
						default:
							$gender.html('未设置');
							$gender_code.val(0);
							break;
					}
				}else if(key == 'status'){
					var $status      = $('#oa_user_info_viewer_'+key);
					var $status_code = $('#oa_user_info_viewer_'+key+'_code');
					switch(parseInt(val)){
						case 0:
						default:
							$status.html('否');
							$status_code.val(0);
							break;
						case 1:
							$status.html('是');
							$status_code.val(1);
							break;
					}
				}else if(key == 'dept_code'){
					var $dept_code = $('#oa_user_info_viewer_'+key);
					$dept_code.val(val);
				}else{
					$('#oa_user_info_viewer_'+key).html(val);
				}
				if(key == 'name') $modal.find('.modal-title').html('是否自动导入'+val+'的信息？');
			}
			$modal.modal('show');
		}
	});
	$('#oa_user_info_viewer_submit').on('click', function(){
		CreateObject.object.userSelect.setValue($name_tmp.html());
		CreateObject.object.userSelect.setHtml($name_tmp.html());
		$code.val($code_tmp.html()).attr('value', $code_tmp.html());
		$mobile.val($mobile_tmp.html()).attr('value', $mobile_tmp.html());
		CreateObject.object.positionSelect.setValue($position_tmp.html());
		CreateObject.object.positionSelect.setHtml($position_tmp.html());
		CreateObject.object.titleSelect.setValue($position_tmp.html());
		CreateObject.object.titleSelect.setHtml($position_tmp.html());
		CreateObject.object.deptSelect.setValue($dept_code_tmp.val());
		CreateObject.object.deptSelect.setHtml($dept_name_tmp.html());
		$birthday.val($birthday_tmp.html()).attr('value', $birthday_tmp.html());
		$gender.find('option[value='+$gender_tmp.val()+']').prop('selected', true);
		$status.find('option[value='+$status_tmp.val()+']').prop('selected', true);
		$modal.modal('hide');
	});
	CreateObject.object.deptSelect.onQuasarSelect(function(){
		var dept = CreateObject.object.deptSelect.getHtml();
		var end = dept.lastIndexOf(')');
		var start = dept.lastIndexOf('(');
		var company = dept.substr(start+1, end-start-1);
		CreateObject.object.companySelect.setValue(company);
		CreateObject.object.companySelect.setHtml(company);
	});
});