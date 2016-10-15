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
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.css">
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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.js"></script>
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
						<i class="icon_nav glyphicon glyphicon-tags"></i> <span class="nav-label">代金券管理</span>
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
					<section class="content badge_content">
						<header>
							<div class="nav_tab clearfix">
								<div class="nav_tab_li active">
									<a href="javascript:void(0)">自定义胸卡模板</a>
								</div>
								<div class="nav_tab_li">
									<a href="javascript:void(0)">系统胸卡模板列表</a>
								</div>
							</div>
						</header>
						<!--自定义胸卡模板-->
						<div class="badge_box custom_temp tab_c clearfix">
							<div class="cart_view">
								<div class="badge_muban" style="width: 300px; height: 400px;">
									<img src="<?php echo (COMMON_IMAGE_PATH); ?>/bg.jpg" id="badge_bg" alt="" style="width: 100%; height: 100%; position: absolute;">
									<div class="cart_view_item ui-widget-content hide" id="client_name" style=" position: absolute; width: 300px; font-size:22px; text-align: center; left: 50%; margin-left: -150px; top: 5%;">客户姓名</div>
									<div class="cart_view_item ui-widget-content hide" id="QRcode" style=" position: absolute; width: 150px; font-size:14px; text-align: center; left: 50%; margin-left: -75px; top: 25%;;">
										<img src="<?php echo (COMMON_IMAGE_PATH); ?>/CheckIn_Code.jpg" alt="" width="100%">
									</div>
									<div class="cart_view_item ui-widget-content hide" id="meeting_name" style=" position: absolute; width: 300px; font-size:22px; text-align: center; left: 50%; margin-left: -150px; top: 65%;;">会议名称</div>
									<div class="cart_view_item ui-widget-content hide" id="meeting_time" style=" position: absolute; width: 300px; font-size:14px; text-align: center; left: 50%; margin-left: -150px; top: 88%;;">会议时间</div>
									<div class="cart_view_item ui-widget-content hide" id="sign_place" style=" position: absolute; width: 300px; font-size:14px; text-align: center; left: 50%; margin-left: -150px; top: 92%;;">签到点</div>
									<div class="cart_view_item ui-widget-content hide" id="club" style=" position: absolute; width: 300px; font-size:22px; text-align: center; left: 50%; margin-left: -150px; top: 15%;;">会所</div>
									<div class="cart_view_item ui-widget-content hide" id="brief" style=" position: absolute; width: 300px; font-size:14px; text-align: center; left: 50%; margin-left: -150px; top: 75%;;">会议简介</div>
								</div>
							</div>
							<div class="cart_set">
								<h2>请设计会议所用胸卡模板</h2>
								<div class="template template_custom">
									<div class="form_li">
										<p class="title">胸卡名称</p>
										<div class="name">
											<input type="text" name="badge_name" class="form-control">
										</div>
									</div>
									<div class="form_li">
										<p class="title">背景（宽度为400px，高度为700px的.png、.jpg图片，小于2M）</p>
										<form action="" id="uploadBgForm">
											<input type="hidden" name="requestType" value="upload_image">
											<div class="bg_up_file">
												<input type="file" class="filePrew" name="uploadImage" id="updateBackground">
												<span class="text"><span class="glyphicon glyphicon-upload" style="margin-right: 5px;"></span><span>上传图片</span></span>
												<button type="button" id="submit_bg" onclick="badgeManage.upLoadBg()"></button>
											</div>
										</form>
										<div class="size">
											<span>宽高（PX）：</span><span><input type="text" class="temp_width" value="400">&nbsp;&nbsp;×&nbsp;&nbsp;<input type="text" class="temp_height" value="700"></span>
											<button type="button" class="btn btn-sm btn-info size_confirm">确认</button>
										</div>
									</div>
									<div class="form_li">
										<p class="title">内容（点击勾选添加到左侧界面中，点击删除，将从左侧界面中移除）</p>
										<ul class="vote_list clearfix">
											<li class="keyword no_choose client_name">
												<span>客户姓名</span><i class="glyphicon glyphicon-trash"></i>
											</li>
											<li class="keyword no_choose QRcode">
												<span>参会二维码</span><i class="glyphicon glyphicon-trash"></i>
											</li>
											<li class="keyword no_choose meeting_name">
												<span>会议名称</span><i class="glyphicon glyphicon-trash"></i>
											</li>
											<li class="keyword no_choose time">
												<span>会议时间</span><i class="glyphicon glyphicon-trash"></i>
											</li>
											<li class="keyword no_choose sign_place">
												<span>签到点</span><i class="glyphicon glyphicon-trash"></i>
											</li>
											<li class="keyword no_choose club">
												<span>会所</span><i class="glyphicon glyphicon-trash"></i>
											</li>
											<li class="keyword no_choose brief">
												<span>简介</span><i class="glyphicon glyphicon-trash"></i>
											</li>
											<!--<li class="add">
												<span class="glyphicon glyphicon-plus"></span>
											</li>-->
										</ul>
									</div>
									<div class="form_li text-center">
										<button type="button" id="keep_badge_temp" class="btn btn-primary">保存设置</button>
									</div>
								</div>
							</div>
						</div>
						<!--系统模板	-->
						<div class="badge_box system_tem tab_c hide">
							<form role="form" class="form-horizontal" method="post" action="">
								<input type="hidden" name="requestType" value="assign_badge_for_meeting">
								<div class="form-group">
									<label for="meeting_name_s" class="col-sm-1 control-label"><b style="vertical-align: middle;color: red;">*</b>选择会议：</label>
									<div class="col-sm-5">
										<div id="meeting_name_s"></div>
									</div>
								</div>
								<div class="system_tem_list">
									<ul class="clearfix system_tem_ul">
										<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><li data-id="<?php echo ($single["id"]); ?>">
												<div class="layer"></div>
												<i class="select_icon glyphicon glyphicon-ok"></i>
												<div class="system_ontent">
													<?php echo ($single["data"]); ?>
												</div>
												<p class="title">胸卡名称</p>
											</li><?php endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div>
								<input type="hidden" name="id">
								<div class="form_li text-center">
									<button type="submit" id="choose_system_badge" class="btn btn-primary">确认</button>
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
				meetingNamesSelect:$('#meeting_name_s').QuasarSelect({
					name        :'mid',
					classStyle  :'form-control',
					idInput     :'selected_develop_consultant',
					idHidden    :'selected_develop_consultant_form',
					data        :'<?php echo json_encode($meeting_list);?>',
					placeholder :'',
					hasEmptyItem:false
				}),
				loading                :$().QuasarLoading(),
				toast         :$().QuasarToast(),
				icheck:$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-blue',
					radioClass   :'iradio_square-blue'
				})
			}
		};
		$(function(){
			$(".cart_view_item ").resizable({
				resize:function(event, ui){
					var h = $(this).height();
					$(this).css('font-size', h*0.6)
				}
			}).draggable();
		});
	</script>
</body>
</html>