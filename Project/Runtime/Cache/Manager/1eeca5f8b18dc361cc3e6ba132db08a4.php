<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>修改签到点 - 会议系统</title>
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
		<a href="http://www.baidu.com">吉美会议</a>
	</div>
	<div class="sidenav">
		<ul class="sidenav_list" id="side_menu">
			<?php if($permission_list['viewEmployee'] == 1): ?><li class="side_item <?php if('Employee'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Employee/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-user"></i> <span class="nav-label">员工管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<li>
							<a href="<?php echo U('Employee/create');?>">新建员工</a>
						</li>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['viewRole'] == 1): ?><li class="side_item <?php if('Role'==$c_name) echo 'active'; ?>">
					<a href="<?php echo U('Role/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon glyphicon-star"></i> <span class="nav-label">角色管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<li class="side_item <?php if('Meeting'==$c_name or 'SignPlace'==$c_name or 'Client'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Meeting/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-th"></i> <span class="nav-label">会议管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				<ul class="nav-second-level">
					<li>
						<a href="<?php echo U('Meeting/create');?>">创建会议</a>
					</li>
				</ul>
			</li>
			<li class="side_item <?php if('Coupon'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Coupon/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-tags"></i> <span class="nav-label">代金券管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
			</li>
			<li class="side_item <?php if('Message'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Message/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-comment"></i> <span class="nav-label">消息管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
			</li>
			<li class="side_item <?php if('Badge'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Badge/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-comment"></i> <span class="nav-label">胸卡设计</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
			</li>
			<li class="side_item cls <?php if('Recycle'==$c_name) echo 'active'; ?>">
				<div class="side-item-link no_link">
					<i class="icon_nav glyphicon glyphicon-trash"></i> <span class="nav-label">回收站管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</div>
				<ul class="nav-second-level">
					<li>
						<a href="<?php echo U('Recycle/client');?>">客户列表</a>
					</li>
					<li>
						<a href="<?php echo U('Recycle/employee');?>">员工列表</a>
					</li>
					<li>
						<a href="<?php echo U('Recycle/role');?>">角色列表</a>
					</li>
					<li>
						<a href="<?php echo U('Recycle/meeting');?>">会议列表</a>
					</li>
				</ul>
			</li>
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
				<div class="mian_body">
					<section class="content" style="padding: 10px;">
						<div class="return">
							<a class="btn btn-default" href="javascript:history.go(-1)"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回上一页</a>
						</div>
						<div class="modal-header">
							<h2 class="modal-title" id="authorize_role_title">修改签到点</h2>
						</div>
						<form class="form-horizontal" role="form" method="post" action="" onsubmit="return checkIsEmpty()">
							<div class="modal-body" style=" padding: 15px 20px 15px 0;">
								<div class="form-group">
									<label for="mid" class="col-sm-1 control-label color-red"><b style="vertical-align: middle">*</b>会议：</label>
									<div class="col-sm-11">
										<input type="text" class="form-control" name="mid" id="mid" value="<?php echo ($record_name["name"]); ?>" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="name" class="col-sm-1 control-label color-red"><b style="vertical-align: middle">*</b>签到点名称：</label>
									<div class="col-sm-11">
										<input type="text" class="form-control name" name="name" id="name" value="<?php echo ($list["name"]); ?>">
									</div>
								</div>
								<div class="form-group">
									<label for="place" class="col-sm-1 control-label color-red"><b style="vertical-align: middle">*</b>地址：</label>
									<div class="col-sm-11">
										<div id="place" class="address_wrap clearfix">
											<div class="address_item">
												<select class="prov form-control" name="address_province"></select>
											</div>
											<div class="address_item">
												<select class="city form-control" name="address_city" disabled="disabled"></select>
											</div>
											<div class="address_item">
												<select class="dist form-control" name="address_area" disabled="disabled"></select>
											</div>
											<div class="address_item address_details">
												<input type="text" placeholder="具体街道或者村镇" class="form-control" name="address.detail">
											</div>
											<div class="address_item address_default">
												原先地址：<span><?php echo ($list["place"]); ?></span>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="director_id" class="col-sm-1 control-label color-red"><b style="vertical-align: middle">*</b>负责人：</label>
									<div class="col-sm-11">
										<div id="director_id"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="sign_director_id" class="col-sm-1 control-label color-red"><b style="vertical-align: middle">*</b>签到负责人：</label>
									<div class="col-sm-11">
										<div id="sign_director_id"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="comment" class="col-sm-1 control-label">备注：</label>
									<div class="col-sm-11">
										<textarea class="form-control comment" name="comment" id="comment"><?php echo ($list["comment"]); ?></textarea>
									</div>
								</div>
							</div>
							<div class="modal-footer text_center">
								<button type="submit" class="btn btn-primary">保存</button>
							</div>
						</form>
					</section>
				</div>
			</div>
		</div>
	</div>
	<script>
		var AlterObject = {
			object:{
				directorIdSelect:$('#director_id').QuasarSelect({
					name        :'director_id',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_list);?>',
					idInput     :'selected_director_id',
					idHidden    :'selected_director_id_form',
					placeholder :'',
					hasEmptyItem:false,
					defaultValue:'<?php echo ($list["director_id"]); ?>',
					defaultHtml :'<?php echo ($info["develop_consultant_code"]); ?>'+" - "+'<?php echo ($info["develop_consultant_name"]); ?>'
				}),
				signDirectorIdSelect:$('#sign_director_id').QuasarSelect({
					name        :'sign_director_id',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_list);?>',
					idInput     :'selected_sign_director_id',
					idHidden    :'selected_sign_director_id_form',
					placeholder :'',
					hasEmptyItem:false,
					defaultValue:'<?php echo ($list["sign_director_id"]); ?>',
					defaultHtml :'<?php echo ($info["service_consultant_code"]); ?>'+" - "+'<?php echo ($info["service_consultant_name"]); ?>'
				}),
				toast            :$().QuasarToast(),
				loading          :$().QuasarLoading()
			}
		};
		// 三联地址
		$(function(){
			$("#place").citySelect({
				nodata:"none",
				url   :'<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/city.min.js'
			});
		});
	</script>
</body>
</html>