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
					<section class="content" style="padding: 10px;">
						<div class="modal-header">
							<h2 class="modal-title" id="authorize_role_title">修改会议</h2>
						</div>
						<form class="form-horizontal" role="form" method="post" action="" onsubmit="return checkMeetingAlter()">
							<div class="modal-body" style=" padding: 15px 20px 15px 0px;">
								<div class="form-group">
									<label for="meeting_name" class="col-sm-1 control-label">会议名称：</label>
									<div class="col-sm-11">
										<input type="text" value="<?php echo ($info["name"]); ?>" class="form-control meeting_name" name="name" id="meeting_name">
									</div>
								</div>
								<div class="form-group">
									<label for="meeting_status" class="col-sm-1 control-label">状态：</label>
									<div class="col-sm-11">
										<select name="status" id="meeting_status" class="form-control">
											<?php switch($info['status']): case "0": ?><option value="0" selected>禁用</option>
													<option value="1">新建</option>
													<option value="2">进行中</option>
													<option value="3">结束</option><?php break;?>
												<?php case "1": ?><option value="0">禁用</option>
													<option value="1" selected>新建</option>
													<option value="2">进行中</option>
													<option value="3">结束</option><?php break;?>
												<?php case "2": ?><option value="0">禁用</option>
													<option value="1">新建</option>
													<option value="2" selected>进行中</option>
													<option value="3">结束</option><?php break;?>
												<?php case "3": ?><option value="0">禁用</option>
													<option value="1">新建</option>
													<option value="2">进行中</option>
													<option value="3" selected>结束</option><?php break; endswitch;?>
										</select>
									</div>

								</div>
								<div class="form-group">
									<label for="meeting_type" class="col-sm-1 control-label">类型：</label>
									<div class="col-sm-11">
										<select name="type" id="meeting_type" class="form-control">
											<?php switch($info['type']): case "1": ?><option value="1" selected>成交会</option>
													<option value="2">特训营</option>
													<option value="3">优雅女子</option>
													<option value="4">招商会</option><?php break;?>
												<?php case "2": ?><option value="1">成交会</option>
													<option value="2" selected>特训营</option>
													<option value="3">优雅女子</option>
													<option value="4">招商会</option><?php break;?>
												<?php case "3": ?><option value="1">成交会</option>
													<option value="2">特训营</option>
													<option value="3" selected>优雅女子</option>
													<option value="4">招商会</option><?php break;?>
												<?php case "4": ?><option value="1">成交会</option>
													<option value="2">特训营</option>
													<option value="3">结束</option>
													<option value="4" selected>招商会</option><?php break; endswitch;?>
										</select>
									</div>

								</div>
								<div class="form-group">
									<label for="meeting_host" class="col-sm-1 control-label">主办方：</label>
									<div class="col-sm-11">
										<input type="text" value="<?php echo ($info["host"]); ?>" class="form-control meeting_host" name="host" id="meeting_host">
									</div>

								</div>
								<div class="form-group">
									<label for="meeting_plan" class="col-sm-1 control-label">策划方：</label>
									<div class="col-sm-11">
										<input type="text" value="<?php echo ($info["plan"]); ?>" class="form-control meeting_plan" name="plan" id="meeting_plan">
									</div>

								</div>
								<!--<div class="form-group">
									<label for="meeting_place" class="col-sm-1 control-label">举办地点：</label>
									<div class="col-sm-11">
										<input type="text" value="<?php echo ($info["place"]); ?>" class="form-control meeting_place" name="place" id="meeting_place">
									</div>

								</div>-->
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
											<div class="address_item address_default">
												原先地址：<span><?php echo ($info["place"]); ?></span>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="meeting_start_time" class="col-sm-1 control-label">开始时间：</label>
									<div class="col-sm-11">
										<!--<input type="text" class="form-control meeting_start_time" name="meeting_place" id="meeting_start_time">-->
										<div class="input-group date form_datetime" id="meeting_start_time_wrap">
											<input class="form-control" id="meeting_start_time" name="start_time" value="<?php echo ($info["start_time"]); ?>">
											<!--<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>-->
											<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
										</div>
									</div>

								</div>
								<div class="form-group">
									<label for="meeting_end_time" class="col-sm-1 control-label">结束时间：</label>
									<div class="col-sm-11">
										<div class="input-group date form_datetime" id="meeting_end_time_wrap">
											<input class="form-control" id="meeting_end_time" name="end_time" value="<?php echo ($info["end_time"]); ?>" >
											<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
										</div>
									</div>

								</div>
								<div class="form-group">
									<label for="director_id" class="col-sm-1 control-label">负责人ID：</label>
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
										<input type="text" class="form-control brief" name="brief" id="brief" value="<?php echo ($info["brief"]); ?>">
									</div>

								</div>
								<div class="form-group">
									<label for="logo" class="col-sm-1 control-label">LOGO图片地址：</label>
									<div class="col-sm-11">
										<input type="text" class="form-control logo" name="logo" id="logo" value="<?php echo ($info["logo"]); ?>">
									</div>

								</div>
								<div class="form-group">
									<label for="comment" class="col-sm-1 control-label">备注：</label>
									<div class="col-sm-11">
										<textarea class="form-control comment" name="comment" id="comment"><?php echo ($info["comment"]); ?></textarea>
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
					startView         :4,
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
					justInput   :true,
					hasEmptyItem:false,
					defaultValue:'<?php echo ($info["director_id"]); ?>',
					defaultHtml:'<?php echo ($info["director_name"]); ?>'

				}),
				contacts1IdSelect:$('#contacts_1_id').QuasarSelect({
					name        :'contacts_1_id',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_list);?>',
					idInput     :'selected_contacts_1_id',
					idHidden    :'selected_contacts_1_id_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false,
					defaultValue:'<?php echo ($info["contacts_1_id"]); ?>',
					defaultHtml:'<?php echo ($info["contacts_1_name"]); ?>'
				}),
				contacts2IdSelect:$('#contacts_2_id').QuasarSelect({
					name        :'contacts_2_id',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_list);?>',
					idInput     :'selected_contacts_2_id',
					idHidden    :'selected_contacts_2_id_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false,
					defaultValue:'<?php echo ($info["contacts_2_id"]); ?>',
					defaultHtml:'<?php echo ($info["contacts_2_name"]); ?>'
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