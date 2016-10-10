<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>胸卡设计 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.css">
	<!--<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/custom.css">-->
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/skins/all.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/jquery-ui-base.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE); ?>">
	<link rel="stylesheet" href="<?php echo (SELF_STYLE); ?>">
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-ui.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/jquery.cityselect.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/icheck.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/custom.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.js"></script>
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
					<section class="content badge_content">
						<header><h1>胸卡设计</h1></header>
						<div class="badge_box">
							<div class="cart_view">

							</div>
							<div class="cart_set">
								<h2>自定义胸卡模板</h2>
								<div class="choose_type">
									<span>选择胸卡模板：</span>
									<span><input type="radio"  name="iCheck" class="icheck" >&nbsp;&nbsp;使用系统模板</span>
									<span><input type="radio"  name="iCheck" class="icheck" checked >&nbsp;&nbsp;自定义模板</span>
								</div>
								<div class="template template_system hide">

								</div>
								<div class="template template_custom">
									<span>背景</span>
								</div>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
	<script>
		var ManageObject = {
			object:{
				/*toast         :$().QuasarToast(),*/
				icheck:$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				})
			}
		}
	</script>
</body>
</html>