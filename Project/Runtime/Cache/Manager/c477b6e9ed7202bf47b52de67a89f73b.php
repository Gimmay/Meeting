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
			<?php if($permission_list['EMPLOYEE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Employee): ?>active<?php endif; ?>">
					<a href="<?php echo U('Employee/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-user"></i> <span class="nav-label">员工管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['EMPLOYEE.CREATE'] == 1): ?><li>
								<a href="<?php echo U('Employee/create');?>">新建员工</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['ROLE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Role): ?>active<?php endif; ?>">
					<a href="<?php echo U('Role/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon glyphicon-star"></i> <span class="nav-label">角色管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MEETING.VIEW'] == 1): ?><li class="side_item <?php if(($c_name == Meeting) or ($c_name == SignPlace) or ($c_name == Client)): ?>active<?php endif; ?>">
					<a href="<?php echo U('Meeting/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-th"></i> <span class="nav-label">会议管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['MEETING.CREATE'] == 1): ?><li>
								<a href="<?php echo U('Meeting/create');?>">创建会议</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['COUPON.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Receivables): ?>active<?php endif; ?>">
					<a href="<?php echo U('Receivables/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-usd"></i> <span class="nav-label">收款管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['COUPON.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Coupon): ?>active<?php endif; ?>">
					<a href="<?php echo U('Coupon/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-tags"></i> <span class="nav-label">券管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MESSAGE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Message): ?>active<?php endif; ?>">
					<a href="<?php echo U('Message/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-comment"></i> <span class="nav-label">消息管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['BADGE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Badge): ?>active<?php endif; ?>">
					<a href="<?php echo U('Badge/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-bookmark"></i> <span class="nav-label">胸卡设计</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['ROOM.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Hotel or $c_name == Room): ?>active<?php endif; ?>">
					<a href="<?php echo U('Hotel/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-home"></i> <span class="nav-label">酒店管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['CAR.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Car): ?>active<?php endif; ?>">
					<a href="<?php echo U('Car/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-plane"></i> <span class="nav-label">车辆管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['DINING_TABLE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == DiningTable): ?>active<?php endif; ?>">
					<a href="<?php echo U('DiningTable/manage');?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-glass"></i> <span class="nav-label">餐桌管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<li class="side_item cls <?php if($c_name == Report): ?>active<?php endif; ?>">
				<div class="side-item-link no_link">
					<i class="icon_nav glyphicon glyphicon-list-alt"></i> <span class="nav-label">报表查询</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</div>
				<ul class="nav-second-level">
					<li>
						<a href="<?php echo U('Report/joinReceivables');?>">报名收款数据</a>
					</li>
				</ul>
			</li>
			<?php if(($permission_list['RECYCLE.VIEW-CLIENT'] == 1) OR ($permission_list['RECYCLE.VIEW-EMPLOYEE'] == 1) OR ($permission_list['RECYCLE.VIEW-ROLE'] == 1) OR ($permission_list['RECYCLE.VIEW-MEETING'] == 1)): ?><li class="side_item cls <?php if($c_name == Recycle): ?>active<?php endif; ?>">
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
			<div class="hidden_dropDown hide">
				<div class="dropDown_item">
					<a href="<?php echo U('Employee/personalInfor');?>" target="_blank">个人信息</a>
				</div>
				<div class="dropDown_item">
					<a href="<?php echo U('Employee/alterPass');?>">修改密码</a>
				</div>
				<div class="dropDown_item">
					<a href="<?php echo U('Employee/logout');?>">注销</a>
				</div>
			</div>
		</li>
		<!--<li class="logout">
			<a href="<?php echo U('Employee/logout');?>"> <i class="glyphicon glyphicon-log-out"></i> <span>注销</span> </a>
		</li>-->
	</ul>
</div>
				<div class="main_body">
					<section class="content" style="padding: 10px;">
						<div class="return">
							<a class="btn btn-default" href="<?php echo U('manage');?>"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回上一页</a>
						</div>
						<div class="modal-header">
							<h2 class="modal-title" id="authorize_role_title">修改会议</h2>
						</div>
						<form class="form-horizontal" role="form" method="post" action="" id="form">
							<div class="modal-body" style=" padding: 15px 20px 15px 0;">
								<div class="form-group">
									<label for="meeting_name" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>会议名称：</label>
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
											<?php switch($info['type']): case "成交会": ?><option value="成交会" selected>成交会</option>
													<option value="特训营">特训营</option>
													<option value="优雅女子">优雅女子</option>
													<option value="招商会">招商会</option>
													<option value="启动大会">启动大会</option><?php break;?>
												<?php case "特训营": ?><option value="成交会">成交会</option>
													<option value="特训营" selected>特训营</option>
													<option value="优雅女子">优雅女子</option>
													<option value="招商会">招商会</option>
													<option value="启动大会">启动大会</option><?php break;?>
												<?php case "优雅女子": ?><option value="成交会">成交会</option>
													<option value="特训营">特训营</option>
													<option value="优雅女子" selected>优雅女子</option>
													<option value="招商会">招商会</option>
													<option value="启动大会">启动大会</option><?php break;?>
												<?php case "招商会": ?><option value="成交会">成交会</option>
													<option value="特训营">特训营</option>
													<option value="结束">结束</option>
													<option value="招商会" selected>招商会</option>
													<option value="启动大会">启动大会</option><?php break;?>
												<?php case "启动大会": ?><option value="成交会">成交会</option>
													<option value="特训营">特训营</option>
													<option value="结束">结束</option>
													<option value="招商会">招商会</option>
													<option value="启动大会" selected>启动大会</option><?php break; endswitch;?>
										</select>
									</div>

								</div>
								<div class="form-group">
									<label for="meeting_host" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>主办方：</label>
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
									<label for="meeting_start_time" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>开始时间：</label>
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
									<label for="meeting_end_time" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>结束时间：</label>
									<div class="col-sm-11">
										<div class="input-group date form_datetime" id="meeting_end_time_wrap">
											<input class="form-control" id="meeting_end_time" name="end_time" value="<?php echo ($info["end_time"]); ?>" >
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
									<label for="contacts_1_id" class="col-sm-1 control-label ">联系人：</label>
									<div class="col-sm-11">
										<div id="contacts_1_id"></div>
									</div>

								</div>
								<div class="form-group">
									<label for="contacts_2_id" class="col-sm-1 control-label ">联系人2：</label>
									<div class="col-sm-11">
										<div id="contacts_2_id"></div>
									</div>

								</div>
								<div class="form-group">
									<label for="brief" class="col-sm-1 control-label">简介：</label>
									<div class="col-sm-11">
										<textarea class="form-control brief xheditor" runat="server"  name="brief" id="brief" style="min-height: 200px"><?php echo ($info["brief"]); ?></textarea>
									</div>

								</div>
								<!--<div class="form-group">
									<label for="logo" class="col-sm-1 control-label">LOGO图片地址：</label>
									<div class="col-sm-11">
										<input type="text" class="form-control logo" name="logo" id="logo" value="<?php echo ($info["logo"]); ?>">
									</div>
								</div>-->
								<div class="form-group">
									<label for="updateBackground" class="col-sm-1 control-label">LOGO图片地址：</label>
									<div class="col-sm-10">
										<div class="logo_wp">
											<img src="<?php echo ($info["logo"]); ?>">
											<!--<div class="logo_not">
												<span class="glyphicon glyphicon-picture"></span>
												<p>请上传尺寸为1080px*640px的.png、.jpg、.gif图片
													图片大小小于1M</p>
											</div>-->
										</div>
										<div class="bg_up_file">
											<input type="hidden" name="logo" value="<?php echo ($info["logo"]); ?>">
											<input type="file" class="filePrew" name="logo_upload" id="updateBackground">
											<span class="text"><span class="glyphicon glyphicon-upload" style="margin-right: 5px;"></span><span>上传图片</span></span>
											<button type="button" id="submit_logo" onclick="upLoadLogo()"></button>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label">选择酒店：</label>
									<div class="col-sm-11">
										<button type="button" class="btn btn-default choose_hotel_btn" data-toggle="modal" data-target="#choose_hotel">选择酒店</button>
										&nbsp;&nbsp;&nbsp;&nbsp;<span class="c_hotel">已选择：<span></span></span>
									</div>
									<input type="hidden" name="hotel">
								</div>
								<div class="form-group">
									<label for="comment" class="col-sm-1 control-label">备注：</label>
									<div class="col-sm-11">
										<textarea class="form-control comment" name="comment" id="comment"><?php echo ($info["comment"]); ?></textarea>
									</div>

								</div>
							</div>
							<input type="hidden" name="requestType" value="alter">
							<div class="modal-footer text_center">
								<button type="submit" class="btn btn-primary" onclick="return checkIsEmpty()">保存</button>
							</div>
						</form>
					</section>
				</div>
			</div>
		</div>
	</div>
	<!--选择酒店-->
	<div class="modal fade" id="choose_hotel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width: 60%">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title text-center">可选酒店列表</h2>
				</div>
				<form class="form-horizontal" role="form" action="" method="post">
					<input type="hidden" name="requestType" value="choose_car"> <input type="hidden" name="id">
					<div class="modal-body">
						<table class="table table-bordered" style="text-align: center">
							<thead>
								<tr>
									<td width="5%" class="all_check">
										<input type="checkbox" class="icheck" placeholder="" value="">
									</td>
									<td width="5%">序号</td>
									<td width="10%">酒店名称</td>
									<td width="10%">星级</td>
									<td width="10%">类型</td>
									<td width="10%">地址</td>
									<td width="10%">价格</td>
									<td width="10%">联系方式</td>
									<td width="10%">简介</td>
									<td width="20%">备注</td>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><tr>
										<td class="check_item">
											<input type="checkbox" class="icheck" placeholder="" value="<?php echo ($single["id"]); ?>">
										</td>
										<td><?php echo ($i); ?></td>
										<td class="hotel_name"><?php echo ($single["name"]); ?></td>
										<td data-id="<?php echo ($single["id"]); ?>"><?php echo ($single["name"]); ?></td>
										<td><?php echo ($single["type"]); ?></td>
										<td><?php echo ($single["address"]); ?></td>
										<td><?php echo ($single["price"]); ?></td>
										<td><a href="<?php echo U('manage', ['did'=>$single['did']]);?>"><?php echo ($single["contact"]); ?></a>
										</td>
										<td><?php echo ($single["brief"]); ?></td>
										<td><?php echo ($single["comment"]); ?></td>
									</tr><?php endforeach; endif; else: echo "" ;endif; ?>
							</tbody>

						</table>
						<div class="page_wrap">
							<div class="pagination">
								<?php echo ($page_show); ?>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="button" class="btn btn-primary btn_save_hotel">保存</button>
					</div>
				</form>
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
					hasEmptyItem:false,
					defaultValue:'<?php echo ($info["contacts_2_id"]); ?>',
					defaultHtml:'<?php echo ($info["contacts_2_name"]); ?>'
				}),
				toast            :$().QuasarToast(),
				loading          :$().QuasarLoading(),
				addressSelect:$("#place").citySelect({
					nodata:"none",
					url   :'<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/city.min.js'
				})
			}
		};
	</script>
</body>
</html>