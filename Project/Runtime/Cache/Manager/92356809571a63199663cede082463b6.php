<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>新建客户 - 会议系统</title>
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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/jquery.cityselect.js"></script>
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
							<form class="form-horizontal" role="form" method="post" id="form" action="" onsubmit="return ManageObject.func.checkIsEmpty()">
								<input type="hidden" name="requestType" value="create">
								<div class="modal-header">
									<h2 class="modal-title" id="delete_role_title">新建客户</h2>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<label for="name" class="col-sm-1 control-label">姓名：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control name" name="name" id="name">
										</div>
									</div>
									<div class="form-group">
										<label for="gender" class="col-sm-1 control-label">性别：</label>
										<div class="col-sm-11">
											<select name="gender" id="gender" class="form-control">
												<option value="0">未指定</option>
												<option value="1">男</option>
												<option value="2">女</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="birthdate" class="col-sm-1 control-label">出生日期：</label>
										<div class="col-sm-11">
											<div class="input-group date form_datetime" id="birth_date">
												<input class="form-control" id="birthdate" name="birthdate">
												<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="address" class="col-sm-1 control-label">地址：</label>
										<div class="col-sm-11">
											<div id="address" class="address_wrap clearfix">
												<div class="address_item">
													<select class="prov form-control" name="province"></select>
												</div>
												<div class="address_item">
													<select class="city form-control" name="city" disabled="disabled"></select>
												</div>
												<div class="address_item">
													<select class="dist form-control" name="area" disabled="disabled"></select>
												</div>
												<div class="address_item address_details">
													<input type="text" placeholder="具体街道或者村镇" class="form-control" name="address.detail">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="id_card_number" class="col-sm-1 control-label">身份证号：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control id_card_number" name="id_card_number" id="id_card_number">
										</div>
									</div>
									<div class="form-group">
										<label for="title" class="col-sm-1 control-label">职称：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control title" name="title" id="title">
										</div>
									</div>
									<div class="form-group">
										<label for="position" class="col-sm-1 control-label">职务：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control position" name="position" id="position">
										</div>
									</div>
									<div class="form-group">
										<label for="mobile" class="col-sm-1 control-label">手机号：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control mobile" name="mobile" id="mobile">
										</div>
									</div>
									<div class="form-group">
										<label for="telephone" class="col-sm-1 control-label">电话号码：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control telephone" name="telephone" id="telephone">
										</div>
									</div>
									<div class="form-group">
										<label for="email" class="col-sm-1 control-label">电子邮箱：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control email" name="email" id="email">
										</div>
									</div>
									<div class="form-group">
										<label for="club" class="col-sm-1 control-label">会所名称：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control club" name="club" id="club">
										</div>
									</div>
									<div class="form-group">
										<label for="develop_consultant" class="col-sm-1 control-label">开拓顾问：</label>
										<div class="col-sm-11">
											<div id="develop_consultant"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="service_consultant" class="col-sm-1 control-label">服务顾问：</label>
										<div class="col-sm-11">
											<div id="service_consultant"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="accompany" class="col-sm-1 control-label">陪同人姓名：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control accompany" name="accompany" id="accompany">
										</div>
									</div>
									<div class="form-group">
										<label for="comment" class="col-sm-1 control-label">备注：</label>
										<div class="col-sm-11">
											<textarea class="form-control comment" name="comment" id="comment"></textarea>
										</div>
									</div>
									<div class="form-group">
										<label for="colume1" class="col-sm-1 control-label">保留字段1：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume1" name="colume1" id="colume1">
										</div>
									</div>
									<div class="form-group">
										<label for="colume2" class="col-sm-1 control-label">保留字段2：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume2" name="colume2" id="colume2">
										</div>
									</div>
									<div class="form-group">
										<label for="colume3" class="col-sm-1 control-label">保留字段3：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume3" name="colume3" id="colume3">
										</div>
									</div>
									<div class="form-group">
										<label for="colume4" class="col-sm-1 control-label">保留字段4：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume4" name="colume4" id="colume4">
										</div>
									</div>
									<div class="form-group">
										<label for="colume5" class="col-sm-1 control-label">保留字段5：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume5" name="colume5" id="colume5">
										</div>
									</div>
									<div class="form-group">
										<label for="colume6" class="col-sm-1 control-label">保留字段6：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume6" name="colume6" id="colume6">
										</div>
									</div>
									<div class="form-group">
										<label for="colume7" class="col-sm-1 control-label">保留字段7：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume7" name="colume7" id="colume7">
										</div>
									</div>
									<div class="form-group">
										<label for="colume8" class="col-sm-1 control-label">保留字段8：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control colume8" name="colume8" id="colume8">
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
			func  :{
				checkIsEmpty:function(){
					var $name   = $('#name');
					var $mobile = $('#mobile');
					var $club = $('#club');
					if($name.val() == ''){
						ManageObject.object.toast.toast("姓名不能为空");
						$name.focus();
						return false;
					}
					if($mobile.val() == ''){
						ManageObject.object.toast.toast("手机号不能为空");
						$mobile.focus();
						return false;
					}
					if($club.val() == ''){
					 	ManageObject.object.toast.toast("会所名称不能为空");
						$club.focus();
					 return false;
					 }
					return true;
				},
			},
			object:{
				developConsultantSelect:$('#develop_consultant').QuasarSelect({
					name        :'develop_consultant',
					classStyle  :'form-control',
					idInput     :'selected_develop_consultant',
					idHidden    :'selected_develop_consultant_form',
					data        :'<?php echo json_encode($employee_list);?>',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				serviceConsultantSelect   :$('#service_consultant').QuasarSelect({
					name        :'service_consultant',
					classStyle  :'form-control',
					idInput     :'selected_service_consultant',
					idHidden    :'selected_service_consultant_form',
					placeholder :'',
					data        :'<?php echo json_encode($employee_list);?>',
					justInput   :true,
					hasEmptyItem:false
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
		$(function(){
			$("#address").citySelect({
				nodata:"none",
				url   :'<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/city.min.js'
			});
		});
	</script>
</body>
</html>