<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>会议模块 - 会议系统</title>
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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/xheditor-1.2.2/xheditor-1.2.2.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/xheditor-1.2.2/xheditor_lang/zh-cn.js"></script>
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
					<section class="content" style="padding: 10px;">
						<div class="return">
							<a class="btn btn-default" href="<?php echo U('manage');?>"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回上一页</a>
						</div>
						<div class="modal-header">
							<h2 class="modal-title" id="authorize_role_title">新建会议</h2>
						</div>
						<form class="form-horizontal" role="form" method="post" action="" id="form">
							<div class="modal-body" style=" padding: 15px 20px 15px 0;">
								<div class="form-group">
									<label for="meeting_name" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>会议名称：</label>
									<div class="col-sm-11">
										<input type="text" class="form-control meeting_name" name="name" id="meeting_name">
									</div>
								</div>
								<div class="form-group">
									<label for="meeting_status" class="col-sm-1 control-label">状态：</label>
									<div class="col-sm-11">
										<select name="status" id="meeting_status" class="form-control">
											<option value="1">新建</option>
											<option value="2">进行中</option>
											<option value="3">结束</option>
											<option value="4">作废</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="meeting_type" class="col-sm-1 control-label">类型：</label>
									<div class="col-sm-11">
										<select name="type" id="meeting_type" class="form-control">
											<option value="1">成交会</option>
											<option value="2">特训营</option>
											<option value="3">优雅女子</option>
											<option value="4">招商会</option>
											<option value="5">启动大会</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="meeting_host" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>主办方：</label>
									<div class="col-sm-11">
										<input type="text" class="form-control meeting_host" name="host" id="meeting_host">
									</div>
								</div>
								<div class="form-group">
									<label for="meeting_plan" class="col-sm-1 control-label">策划方：</label>
									<div class="col-sm-11">
										<input type="text" class="form-control meeting_plan" name="plan" id="meeting_plan">
									</div>
								</div>
								<div class="form-group">
									<label for="place" class="col-sm-1 control-label">地址：</label>
									<div class="col-sm-11">
										<div id="place" class="address_wrap clearfix">
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
									<label for="meeting_start_time" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>开始时间：</label>
									<div class="col-sm-11">
										<!--<input type="text" class="form-control meeting_start_time" name="meeting_place" id="meeting_start_time">-->
										<div class="input-group date form_datetime" id="meeting_start_time_wrap">
											<input class="form-control" id="meeting_start_time" name="start_time">
											<!--<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>-->
											<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="meeting_end_time" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>结束时间：</label>
									<div class="col-sm-11">
										<div class="input-group date form_datetime" id="meeting_end_time_wrap">
											<input class="form-control" id="meeting_end_time" name="end_time">
											<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="director_id" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>负责人：</label>
									<div class="col-sm-11">
										<div id="director_id"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="contacts_1_id" class="col-sm-1 control-label">联系人：</label>
									<div class="col-sm-11">
										<div id="contacts_1_id"></div>
									</div>

								</div>
								<div class="form-group">
									<label for="contacts_2_id" class="col-sm-1 control-label">联系人2：</label>
									<div class="col-sm-11">
										<div id="contacts_2_id"></div>
									</div>
								</div>
								<div class="form-group">
									<label for="brief" class="col-sm-1 control-label">简介：</label>
									<div class="col-sm-11">
										<textarea class="form-control brief xheditor" runat="server"  name="brief" id="brief" style="min-height: 200px"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label for="updateBackground" class="col-sm-1 control-label">LOGO图片地址：</label>
									<div class="col-sm-5">
											<div class="bg_up_file">
												<input type="hidden" name="logo" value="">
												<input type="file" class="filePrew" name="logo_upload" id="updateBackground">
												<span class="text"><span class="glyphicon glyphicon-upload" style="margin-right: 5px;"></span><span>上传图片</span></span>
												<button type="button" id="submit_logo" onclick="upLoadLogo()"></button>
											</div>
									</div>
									<div class="col-sm-6">
										<span class="upload_prompt">(Logo未上传)</span>
										<button type="button" class="btn btn-sm btn-success mes_preview_btn hide" data-toggle="modal" data-target="#see_logo_modal">预览</button>
									</div>
								</div>
								<div class="form-group">
									<label for="comment" class="col-sm-1 control-label">备注：</label>
									<div class="col-sm-11">
										<textarea class="form-control comment" name="comment" id="comment"></textarea>
									</div>
								</div>
							</div>
							<input type="hidden" name="requestType" value="create">
							<div class="modal-footer text_center">
								<button type="submit" class="btn btn-primary"  onclick="return checkIsEmpty()">保存</button>
							</div>
						</form>
					</section>
				</div>
			</div>
		</div>
	</div>
	<!--预览logo-->
	<div class="modal fade" id="see_logo_modal" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<img id="logo_src" src="" alt="">
		</div>
	</div>
	<script>
		var CreateObject = {
			object:{
				icheck           :$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				}),
				meetingTime      :$('#meeting_start_time_wrap , #meeting_end_time_wrap').datetimepicker({
					language          :'zh-CN',
					todayBtn          :true,
					autoclose         :true,
					todayHighlight    :true,
					keyboardNavigation:true,
					forceParse        :true,
					format            :'yyyy-mm-dd hh:ii:ss',
					weekStart         :0,
					startView         :2,
					minView           :0,
					maxView           :4,
					minuteStep        :5,
					startDate         :new Date()
				}),
				directorIdSelect :$('#director_id').QuasarSelect({
					name        :'director_id',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_list);?>',
					idInput     :'selected_director_id',
					idHidden    :'selected_director_id_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				contacts1IdSelect:$('#contacts_1_id').QuasarSelect({
					name        :'contacts_1_id',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_list);?>',
					idInput     :'selected_contacts_1_id',
					idHidden    :'selected_contacts_1_id_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				contacts2IdSelect:$('#contacts_2_id').QuasarSelect({
					name        :'contacts_2_id',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_list);?>',
					idInput     :'selected_contacts_2_id',
					idHidden    :'selected_contacts_2_id_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				toast            :$().QuasarToast(),
				loading          :$().QuasarLoading(),
				citySelect       :$("#place").citySelect({
					nodata:"none",
					url   :'<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/city.min.js'
				})
			}
		};
	</script>
</body>
</html>