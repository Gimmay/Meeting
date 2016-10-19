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
		<div class="logo">
			<img src="<?php echo (COMMON_IMAGE_PATH); ?>/logo.png" alt="">
		</div>
		<a href="javascript:void(0)">吉美会议</a>
	</div>
	<div class="sidenav">
		<ul class="sidenav_list" id="side_menu">
			<?php if($permission_list['EMPLOYEE.VIEW'] == 1): ?><li class="side_item <?php if('Employee'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Employee/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-user"></i> <span class="nav-label">员工管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['EMPLOYEE.CREATE'] == 1): ?><li>
								<a href="<?php echo U('Employee/create');?>">新建员工</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['ROLE.VIEW'] == 1): ?><li class="side_item <?php if('Role'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Role/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon glyphicon-star"></i> <span class="nav-label">角色管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MEETING.VIEW'] == 1): ?><li class="side_item <?php if('Meeting'==$c_name or 'SignPlace'==$c_name or 'Client'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Meeting/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-th"></i> <span class="nav-label">会议管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['MEETING.CREATE'] == 1): ?><li>
								<a href="<?php echo U('Meeting/create');?>">创建会议</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['COUPON.VIEW'] == 1): ?><li class="side_item <?php if('Coupon'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Coupon/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-tags"></i> <span class="nav-label">券管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MESSAGE.VIEW'] == 1): ?><li class="side_item <?php if('Message'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Message/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-comment"></i> <span class="nav-label">消息管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['BADGE.VIEW'] == 1): ?><li class="side_item <?php if('Badge'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Badge/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-bookmark"></i> <span class="nav-label">胸卡设计</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['ROOM.VIEW'] == 1): ?><li class="side_item <?php if('Room'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Room/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-home"></i> <span class="nav-label">房间管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['CAR.VIEW'] == 1): ?><li class="side_item <?php if('Car'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Car/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-plane"></i> <span class="nav-label">车辆管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['DINING_TABLE.VIEW'] == 1): ?><li class="side_item <?php if('DiningTable'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('DiningTable/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-glass"></i> <span class="nav-label">餐桌管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if(($permission_list['RECYCLE.VIEW-CLIENT'] == 1) OR ($permission_list['RECYCLE.VIEW-EMPLOYEE'] == 1) OR ($permission_list['RECYCLE.VIEW-ROLE'] == 1) OR ($permission_list['RECYCLE.VIEW-MEETING'] == 1)): ?><li class="side_item cls <?php if('Recycle'==$c_name) echo 'active'; ?>">
					<div class="side-item-link no_link">
						<i class="icon_nav glyphicon glyphicon-trash"></i> <span class="nav-label">回收站管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span>
					</div>
					<ul class="nav-second-level">
						<?php if($permission_list['RECYCLE.VIEW-CLIENT'] == 1): ?><li>
								<a href="<?php echo U('Recycle/client');?>">客户列表</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-EMPLOYEE'] == 1): ?><li>
								<a href="<?php echo U('Recycle/employee');?>">员工列表</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-ROLE'] == 1): ?><li>
								<a href="<?php echo U('Recycle/role');?>">角色列表</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-MEETING'] == 1): ?><li>
								<a href="<?php echo U('Recycle/meeting');?>">会议列表</a>
							</li><?php endif; ?>
						<li>
							<a href="<?php echo U('Recycle/coupon');?>">代金券</a>
						</li>
						<li>
							<a href="<?php echo U('Recycle/coupon_item');?>">代金券码</a>
						</li>
						<li>
							<a href="<?php echo U('Recycle/message');?>">消息管理</a>
						</li>
					</ul>
				</li><?php endif; ?>
		</ul>
	</div>
</div>
			<div class="mt_wrapper">
				<!--会议系统头部  公用-->
<div class="mt_topbar clearfix">
	<ul class="nav_info clearfix">
		<li class="name">
			<i class="glyphicon glyphicon-user"></i> <span><?php echo ($curname); ?></span>
		</li>
		<li class="logout">
			<a href="<?php echo U('Employee/logout');?>"> <i class="glyphicon glyphicon-log-out"></i> <span>注销</span> </a>
		</li>
	</ul>
</div>
				<div class="main_body">
					<section class="content">
						<div class="return">
							<a class="btn btn-default" onclick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回上一页</a>
						</div>
						<div class="table_wrap">
							<form class="form-horizontal" role="form" method="post" id="form" onsubmit="return checkIsEmpty()">
								<input type="hidden" name="requestType" value="create">
								<div class="modal-header">
									<h2 class="modal-title" id="delete_role_title">新建员工</h2>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<label for="code" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>工号：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control code" name="code" id="code">
										</div>
									</div>
									<div class="form-group">
										<label for="name" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>姓名：</label>
										<div class="col-sm-11">
											<div id="name"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="password" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>密码：</label>
										<div class="col-sm-11">
											<input type="password" class="form-control password" name="password" id="password">
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
										<label for="position" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>职位：</label>
										<div class="col-sm-11">
											<div id="position"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="department" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>部门：</label>
										<div class="col-sm-11">
											<div id="department"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="department" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>公司：</label>
										<div class="col-sm-11">
											<div id="company"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="title" class="col-sm-1 control-label">职称：</label>
										<div class="col-sm-11">
											<div id="title"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="mobile" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>手机：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control mobile" name="mobile" id="mobile">
										</div>
									</div>
									<div class="form-group">
										<label for="birthday" class="col-sm-1 control-label">生日：</label>
										<div class="col-sm-11">
											<div class="input-group date form_datetime" id="birth_date">
												<input class="form-control" id="birthday" name="birthday">
												<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="comment" class="col-sm-1 control-label">备注：</label>
										<div class="col-sm-11">
											<textarea class="form-control comment" name="comment" id="comment"></textarea>
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
	<!-- 预览员工信息 -->
	<div class="modal fade" id="oa_user_info_viewer" tabindex="3" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h3 class="modal-title"></h3>
				</div>
				<div class="modal-body info_css">
					<p>工号：<span id="oa_user_info_viewer_code"></span></p>
					<p>姓名：<span id="oa_user_info_viewer_name"></span></p>
					<p>手机：<span id="oa_user_info_viewer_mobile"></span></p>
					<p>生日：<span id="oa_user_info_viewer_birthday"></span></p>
					<p>禁用：<span id="oa_user_info_viewer_status"></span></p>
					<p>性别：<span id="oa_user_info_viewer_gender"></span></p>
					<p>职位：<span id="oa_user_info_viewer_position"></span></p>
					<p>部门：<span id="oa_user_info_viewer_dept_name"></span></p>
					<input id="oa_user_info_viewer_dept_code" type="hidden">
					<input id="oa_user_info_viewer_status_code" type="hidden">
					<input id="oa_user_info_viewer_gender_code" type="hidden">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button type="button" class="btn btn-primary" id="oa_user_info_viewer_submit">确定</button>
				</div>
			</div>
		</div>
	</div>
	<script>
		var CreateObject = {
			object:{
				positionSelect:$('#position').QuasarSelect({
					name        :'position',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($position);?>',
					idInput     :'selected_position',
					idHidden    :'selected_position_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				titleSelect   :$('#title').QuasarSelect({
					name        :'title',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($position);?>',
					idInput     :'selected_title',
					idHidden    :'selected_title_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				userSelect    :$('#name').QuasarSelect({
					name        :'name',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($oa_user);?>',
					idInput     :'selected_name',
					idHidden    :'selected_name_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				deptSelect    :$('#department').QuasarSelect({
					name        :'did',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($dept);?>',
					idInput     :'selected_department',
					idHidden    :'selected_department_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				companySelect    :$('#company').QuasarSelect({
					name        :'company',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($company);?>',
					idInput     :'selected_company',
					idHidden    :'selected_company_form',
					placeholder :'',
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
					startView         :2,
					minView           :2,
					maxView           :4,
					minuteStep        :5
				}),
				toast         :$().QuasarToast(),
				loading       :$().QuasarLoading()
			}
		};
	</script>
</body>
</html>