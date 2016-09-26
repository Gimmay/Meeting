<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>员工管理 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/skins/all.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE); ?>">
	<link rel="stylesheet" href="<?php echo (SELF_STYLE); ?>">
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/icheck.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/custom.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.js"></script>
	<script src="<?php echo (COMMON_SCRIPT); ?>"></script>
	<script src="<?php echo (SELF_SCRIPT); ?>"></script>
</head>
<body>
	<div id="mt_container">
		<div class="mt_content">
			<!--会议系统左侧  导航栏 公用-->
<div class="mt_navbar">
	<div class="header">
		<a href="http://www.baidu.com">吉美会议</a>
	</div>
	<div class="sidenav">
		<ul class="sidenav_list" id="side_menu">
			<li class="side_item <?php if('Employee'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Employee/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-home"></i>
					<span class="nav-label">员工模块</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</a>
				<ul class="nav-second-level">
					<li>
						<a href="<?php echo U('Employee/create');?>">新建员工</a>
					</li>
				</ul>
			</li>
			<li class="side_item <?php if('Role'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Role/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-home"></i>
					<span class="nav-label">角色模块</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</a>
			</li>
			<li class="side_item <?php if('Meeting'==$c_name or 'SignPlace'==$c_name or 'Client'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Meeting/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-home"></i>
					<span class="nav-label">会议模块</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</a>
				<ul class="nav-second-level">
					<li>
						<a href="<?php echo U('Meeting/create');?>">创建会议</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>
			<div class="mt_wrapper">
				<!--会议系统头部  公用-->
<div class="mt_topbar">
	<ul class="nav_info clearfix">
		<li class="name">
			<i class="glyphicon glyphicon-user"></i> <span><?php echo ($curname); ?></span>
		</li>
		<li class="logout">
			<a href="<?php echo U('Employee/logout');?>"> <i class="glyphicon glyphicon-log-out"></i> <span>注销</span> </a>
		</li>
	</ul>
</div>
				<div class="mian_body">
					<section class="content">
						<div class="table_wrap">
							<form class="form-horizontal" role="form" method="post" id="form" action="" onsubmit="return employeeAlter.checkIsEmpty()">
								<input type="hidden" name="requestType" value="create">
								<div class="modal-header">
									<h2 class="modal-title" id="delete_role_title">修改用户</h2>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<label for="code" class="col-sm-1 control-label">工号：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control code" name="code" id="code" value="<?php echo ($employee["code"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="name" class="col-sm-1 control-label">姓名：</label>
										<div class="col-sm-11">
											<input type="text" name="name" id="name" class="form-control name" value="<?php echo ($employee["name"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="password" class="col-sm-1 control-label">密码：</label>
										<div class="col-sm-11">
											<input type="password" class="form-control password" name="password" id="password" disabled value="<?php echo ($employee["password"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="status" class="col-sm-1 control-label">禁用：</label>
										<div class="col-sm-11">
											<select name="status" id="status" class="form-control">
												<?php if($employee['status'] == 1): ?><option value="0">否</option>
													<option value="1" selected>是</option><?php endif; ?>
												<?php if($employee['status'] == 0): ?><option value="0" selected>否</option>
													<option value="1">是</option><?php endif; ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="gender" class="col-sm-1 control-label">性别：</label>
										<div class="col-sm-11">
											<select name="gender" id="gender" class="form-control">
												<?php if($employee["gender"] == 0): ?>1
													<option value="0" selected>未指定</option>
													<option value="1">男</option>
													<option value="2">女</option><?php endif; ?>
												<?php if($employee["gender"] == 1): ?>2
													<option value="0">未指定</option>
													<option value="1" selected>男</option>
													<option value="2">女</option><?php endif; ?>
												<?php if($employee["gender"] == 2): ?>3
													<option value="0">未指定</option>
													<option value="1">男</option>
													<option value="2" selected>女</option><?php endif; ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="position" class="col-sm-1 control-label">职位：</label>
										<div class="col-sm-11">
											<div id="position"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="department" class="col-sm-1 control-label">部门：</label>
										<div class="col-sm-11">
											<div id="department"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="title" class="col-sm-1 control-label">职称：</label>
										<div class="col-sm-11">
											<div id="title"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="mobile" class="col-sm-1 control-label">手机：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control mobile" name="mobile" id="mobile" value="<?php echo ($employee["mobile"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="birthday" class="col-sm-1 control-label">生日：</label>
										<div class="col-sm-11">
											<div class="input-group date form_datetime" id="birth_date">
												<input class="form-control" id="birthday" name="birthday" value="<?php echo ($employee["birthday"]); ?>">
												<!--<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>-->
												<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="comment" class="col-sm-1 control-label">备注：</label>
										<div class="col-sm-11">
											<textarea class="form-control comment" name="comment" id="comment"><?php echo ($employee["comment"]); ?></textarea>
										</div>
									</div>
								</div>
								<div class="modal-footer text_center">
									<button type="submit" class="btn btn-primary">提交</button>
								</div>
							</form>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
	<script>
		var ManageObject = {
			object:{
				positionSelect:$('#position').QuasarSelect({
					name        :'position',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($position);?>',
					idInput     :'selected_position',
					idHidden    :'selected_position_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false,
					defaultValue:'<?php echo ($employee["position"]); ?>',
					defaultHtml:'<?php echo ($employee["position"]); ?>'
				}),
				titleSelect   :$('#title').QuasarSelect({
					name        :'title',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($position);?>',
					idInput     :'selected_title',
					idHidden    :'selected_title_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false,
					defaultValue:'<?php echo ($employee["title"]); ?>',
					defaultHtml:'<?php echo ($employee["title"]); ?>'
				}),
				deptSelect    :$('#department').QuasarSelect({
					name        :'did',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($dept);?>',
					idInput     :'selected_department',
					idHidden    :'selected_department_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false,
					defaultValue:'<?php echo ($employee["did"]); ?>',
					defaultHtml:'<?php echo ($employee["did"]); ?>'
				}),
				birthDate     :$('#birth_date').datetimepicker({
					language          :'zh-CN',
					todayBtn          :true,
					autoclose         :true,
					todayHighlight    :true,
					keyboardNavigation:true,
					forceParse        :true,
					format            :'yyyy-mm-dd',
					weekStart         :0,
					startView         :4,
					minView           :2,
					maxView           :4,
					minuteStep        :5
				}),
				toast         :$().QuasarToast(),
				loading: $().QuasarLoading()
			}
		};
	</script>
</body>
</html>