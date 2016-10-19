<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>创建消息模板 - 会议系统</title>
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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-position.js"></script>
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
				<div class="main_body" style="overflow: hidden;">
					<section class="content">
						<div class="return">
							<a class="btn btn-default" onclick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回消息管理</a>
						</div>
						<div class="table_wrap">
							<div class="nav_tab clearfix">
								<div class="nav_tab_li active">
									<a href="javascript:void(0)">创建消息模板</a>
								</div>
							</div>
							<div class="overage clearfix">
								<div class="ti float-left">您当前的短信余额：<span style="font-size: 18px;font-weight: bold;color: #FB5050;"><?php echo ($number); ?></span>条
								</div>
								<!--<div class="float-right op">
									<a href="#"><i class="icon records alm"></i><span class="alm">发送记录</span></a>
								</div>-->
							</div>
							<div class="tab-content clearfix">
								<div class="mes_form">
									<form role="form" class="form-horizontal" method="post" action="">
										<input type="hidden" name="requestType" value="create"> <input type="hidden" name="id">
										<div class="edit_box">
											<div class="form-group">
												<label for="style" class="col-sm-1 control-label">创建方式：</label>
												<div class="col-sm-11">
													<select name="style" id="style" class="form-control">
														<option value="0">仅创建消息模板</option>
														<option value="1">创建消息模板并选择收件人</option>
													</select>
												</div>
											</div>
											<div class="form-group mes_people hide">
												<label class="col-sm-1 control-label"></label>
												<div class="col-sm-11">
													<span class="mr_16">已选择 <strong class="m_color" id="selected_attendee_count_by_0">0</strong> 人</span>
													<a data-toggle="modal" data-target="#add_recipient"><i class="glyphicon glyphicon-plus"></i>添加收件人</a>
													<!--<a data-toggle="modal" data-target="#see_recipient">查看已选收件人</a>-->
												</div>
											</div>
											<div class="form-group">
												<label for="name" class="col-sm-1 control-label">模板名称：</label>
												<div class="col-sm-11">
													<input type="text" class="form-control name" name="name" id="name">
												</div>
											</div>
											<!--<div class="form-group">
												<label for="mes_type" class="col-sm-1 control-label">模板类型：</label>
												<div class="col-sm-11">
													<select name="addressee" id="mes_type" class="form-control">
														<option value="0">&#45;&#45;请选择模板类型&#45;&#45;</option>
														<option value="1">会前通知</option>
													</select>
												</div>
											</div>-->
											<div class="form-group">
												<label for="textarea_edit" class="col-sm-1 control-label">文本内容：</label>
												<div class="col-sm-11">
													<div class="input_box">
														<div class="field_box">
															<h3>选择系统字段：</h3>
															<div class="filed_w">
																<button type="button" class="btn btn-xs btn-primary">&lt;:参会人名称:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:参会人会所:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:参会人手机号:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:签到码:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议名称:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议主办方:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议策划方:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议开始时间:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议结束时间:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议负责人:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议联系人1:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议联系人2:&gt;</button>
																<button type="button" class="btn btn-xs btn-primary">&lt;:会议简介:&gt;</button>
															</div>
														</div>
														<textarea class="form-control" rows="3" name="context" id="textarea_edit"></textarea>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="name" class="col-sm-1 control-label"></label>
												<div class="col-sm-11">
													<p style=" margin-top: 5px;">已输入<span class="words_num">0</span>个字，约<span class="mes_num">0</span>条短信（不含签名）
													</p>
													<p style=" margin-top: 5px;">每60个字符计费1条，超过60个字将自动被运营商按多条计费，长短信最多230个字符</p>
												</div>
											</div>
											<div class="form-group text-center">
												<input type="hidden" name="selected_p">
												<div style=" margin-top: 10px;">
													<button type="submit" class="btn bg-00a0e8 save_mes">保存</button>
													<button type="submit" class="btn bg-00a0e8 send_mes hide">发送</button>
													<button type="reset" class="btn bg-00a0e8">重置</button>
												</div>
											</div>
										</div>
									</form>
								</div>
								<div class="mes_view">
									<div class="phone_model_bg">
										<div class="phone_con">
											<div class="face">
												<div class="phone_title ellipsis">吉美会议</div>
												<div class="p_fa">
													<div class="mes_list">
														<div class="m_li right">
															<div class="show_sms_content_container hide">
																<div class="text show_sms_content_text"></div>
																<img src="<?php echo (COMMON_IMAGE_PATH); ?>/phone_mes_bubble.png">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<img src="<?php echo (COMMON_IMAGE_PATH); ?>/phone_bg.png" alt="">
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
	<!-- 添加收件人 -->
	<div class="modal fade" id="add_recipient" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width: 60%">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h2 class="modal-title">添加收件人</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="" id="search_attendee_form">
					<input type="hidden" name="requestType" value="add"> <input type="hidden" name="id">
					<div class="modal-body">
						<div class="form-group">
							<label for="meeting_name" class="col-sm-1 control-label">选择会议：</label>
							<div class="col-sm-11">
								<div id="meeting_name"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="sign" class="col-sm-1 control-label">签到状态：</label>
							<div class="col-sm-11">
								<select name="sign" class="form-control" id="sign">
									<option value="0">所有</option>
									<option value="1">未签到</option>
									<option value="2">已签到</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="reviewed" class="col-sm-1 control-label">审核状态：</label>
							<div class="col-sm-11">
								<select name="reviewed" class="form-control" id="reviewed">
									<option value="0">所有</option>
									<option value="1">未审核</option>
									<option value="2">已审核</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="receivables" class="col-sm-1 control-label">收款状态：</label>
							<div class="col-sm-11">
								<select name="receivables" class="form-control" id="receivables">
									<option value="0">所有</option>
									<option value="1">未收款</option>
									<option value="2">已收款</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="print" class="col-sm-1 control-label">打印状态：</label>
							<div class="col-sm-10">
								<select name="print" class="form-control" id="print">
									<option value="0">所有</option>
									<option value="1">未打印</option>
									<option value="2">已打印</option>
								</select>
							</div>
							<div class="col-sm-1">
								<button type="button" class="btn btn-success btn-sm btn_search_add">搜索</button>
							</div>
						</div>
						<hr>

					</div>
					<!--<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>-->
				</form>
				<div class="choose_people">
					<div class="d1">
						<span>当前有<b class="current_attendee">6</b>人</span>
						<span>已选择<b class="color-red selected_attendee">0</b>人</span>
						<a href="javascript:void(0)" class="btn_save btn btn-sm btn-info">添加为收件人</a>
					</div>
					<div class="d2">
						<div class="table_wrap">
							<table class="table table-bordered" style="text-align: center">
								<thead>
									<tr>
										<td width="5%" class="all_check">
											<input type="checkbox" class="icheck" placeholder="" value="">
										</td>
										<td width="10%">姓名</td>
										<td width="10%">性别</td>
										<td width="10%">手机号</td>
										<td width="15%">会所名称</td>
										<td width="15%">创建时间</td>
									</tr>
								</thead>
								<tbody id="attendee_body">
								</tbody>
							</table>
						</div>
						<div class="page_wrap">
							<ul class="pagination">
								<?php echo ($page_show); ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--&lt;!&ndash; 查看已选收件人 &ndash;&gt;
	<div class="modal fade" id="see_recipient" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width: 60%">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h2 class="modal-title">已选择收件人</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<div class="modal-body">
					<input type="hidden" name="requestType" value="receivables"> <input type="hidden" name="id">
						<div class="form-group">
							<label for="print" class="col-sm-1 control-label">关键字：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control">
							</div>
							<div class="col-sm-1">
								<button class="btn btn-success">搜索</button>
							</div>
						</div>
						<hr>
						<div class="choose_people">
							<div class="d2">
								<div class="table_wrap">
									<table class="table table-bordered" style="text-align: center">
										<thead>
											<tr>
												<td width="5%" class="all_check">
													<input type="checkbox" class="icheck" placeholder="" value="">
												</td>
												<td width="5%">姓名</td>
												<td width="5%">性别</td>
												<td width="10%">手机号</td>
												<td width="10%">会所名称</td>
												<td width="15%">创建时间</td>
												<td width="15%">签到时间</td>
												&lt;!&ndash;<td width="10%">签到方式</td>&ndash;&gt;
												<td width="10%">状态</td>
												<td width="25%">审核/签到/打印</td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><tr>
													<td class="check_item">
														<input type="checkbox" class="icheck" value="<?php echo ($single["cid"]); ?>" data-join-value="<?php echo ($single["id"]); ?>" placeholder="">
													</td>
													<td class="name"><?php echo ($single["name"]); ?></td>
													<td>
												<span class="color-info">
													<?php switch($single["gender"]): case "0": ?>未指定<?php break;?>
														<?php case "1": ?>男<?php break;?>
														<?php case "2": ?>女<?php break; endswitch;?>
												</span>
													</td>
													<td><?php echo ($single["mobile"]); ?></td>
													<td><?php echo ($single["club"]); ?></td>
													<td><?php echo date('Y-m-d H:i:s', $single['creatime']);?></td>
													<td>
														<?php if(!empty($single['sign_time'])): echo date('Y-m-d H:i:s', $single['sign_time']); endif; ?>
													</td>
													&lt;!&ndash;<td>
														<?php switch($single["sign_type"]): case "0": ?>手动签到<?php break;?>
															<?php case "1": ?>微信签到<?php break; endswitch;?>
													</td>&ndash;&gt;
													<td>
														<?php switch($single["status"]): case "0": ?>禁用<?php break;?>
															<?php case "1": ?>可用<?php break; endswitch;?>
													</td>
													<td>
														<?php switch($single["review_status"]): case "0": ?>未审核<?php break;?>
															<?php case "1": ?><span class="color-info">已审核</span><?php break; endswitch;?>
														/
														<?php switch($single["sign_status"]): case "0": ?>未签到<?php break;?>
															<?php case "1": ?><span class="color-danger">已签到</span><?php break; endswitch;?>
														/
														<?php switch($single["print_status"]): case "0": ?>未打印<?php break;?>
															<?php case "1": ?><span class="color-warning">已打印</span><?php break; endswitch;?>
													</td>
												</tr><?php endforeach; endif; else: echo "" ;endif; ?>
										</tbody>
									</table>
								</div>
								<div class="page_wrap">
									<ul class="pagination">
										<?php echo ($page_show); ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
					&lt;!&ndash;<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>&ndash;&gt;
				</form>
			</div>
		</div>
	</div>-->
	<script>
		var CreateObject = {
			object:{
				meetingName         :$('#meeting_name').QuasarSelect({
					name        :'meeting_name',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($meeting);?>',
					idInput     :'selected_meetingName',
					idHidden    :'selected_meetingName_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				toast:$().QuasarToast(),
				loading:$().QuasarLoading(),
				icheck              :$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				})
			}
		};
	</script>
</body>
</html>