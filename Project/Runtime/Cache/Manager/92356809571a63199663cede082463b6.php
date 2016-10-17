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
							<form class="form-horizontal" role="form" method="post" id="form" action="" onsubmit="return checkIsEmpty()">
								<input type="hidden" name="requestType" value="create">
								<div class="modal-header">
									<h2 class="modal-title" id="delete_role_title">新建客户</h2>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<label for="name" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>姓名：</label>
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
										<label for="mobile" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>手机号：</label>
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
										<label for="club" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>会所名称：</label>
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
										<label for="registration_date" class="col-sm-1 control-label">报名时间：</label>
										<div class="col-sm-11">
											<div class="input-group date form_datetime" id="registration_date_layout">
												<input class="form-control" id="registration_date" name="registration_date">
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
		var CreateObject = {
			object:{
				developConsultantSelect:$('#develop_consultant').QuasarSelect({
					name         :'develop_consultant',
					classStyle   :'form-control',
					idInput      :'selected_develop_consultant',
					idHidden     :'selected_develop_consultant_form',
					data         :'<?php echo json_encode($employee_list);?>',
					placeholder  :'',
					hasEmptyItem :true,
					emptyItemHtml:'---空---'
				}),
				serviceConsultantSelect:$('#service_consultant').QuasarSelect({
					name         :'service_consultant',
					classStyle   :'form-control',
					idInput      :'selected_service_consultant',
					idHidden     :'selected_service_consultant_form',
					placeholder  :'',
					data         :'<?php echo json_encode($employee_list);?>',
					hasEmptyItem :true,
					emptyItemHtml:'---空---'
				}),
				birthDate              :$('#birth_date').datetimepicker({
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
				createDate             :$('#create_date').datetimepicker({
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
				registrationDate       :$('#registration_date_layout').datetimepicker({
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
				toast                  :$().QuasarToast(),
				loading                :$().QuasarLoading(),
				citySelect             :$("#address").citySelect({
					nodata:"none",
					url   :'<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/city.min.js'
				})
			}
		};
	</script>
</body>
</html>