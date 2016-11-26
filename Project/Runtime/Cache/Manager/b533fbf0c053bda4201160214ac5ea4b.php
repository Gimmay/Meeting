<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>修改客户 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/skins/all.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE); ?>">
	<link rel="stylesheet" href="<?php echo (SELF_STYLE); ?>">
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jedate/jedate.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/jquery.cityselect.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/icheck.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/custom.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.js"></script>
	<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=18h24xIQGKWEuIMgHzjsNxMD4HTc9Q7m"></script>
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
		<a href="javascript:void(0)">瑞辉会议</a>
	</div>
	<div class="sidenav">
		<ul class="sidenav_list" id="side_menu">
			<?php if($permission_list['MEETING.VIEW']): ?><li class="side_item <?php if($cv_name == Meeting_index): ?>active<?php endif; ?>">
					<a href="<?php echo U('Meeting/index', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-th-large"></i> <span class="nav-label">会议中心</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MEETING.VIEW'] == 1): ?><li class="side_item <?php if($cv_name == Meeting_alter): ?>active<?php endif; ?>">
					<a href="<?php echo U('Meeting/alter', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-list-alt"></i> <span class="nav-label">会议信息</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['CLIENT.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Client): ?>active<?php endif; ?>">
					<a href="<?php echo U('Client/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-user"></i> <span class="nav-label">参会人员</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['SIGN_PLACE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == SignPlace): ?>active<?php endif; ?>">
					<a href="<?php echo U('SignPlace/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-star"></i> <span class="nav-label">签到点管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['RECEIVABLES.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Receivables): ?>active<?php endif; ?>">
					<a href="<?php echo U('Receivables/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-usd"></i> <span class="nav-label">收款管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<li>
							<a href="<?php echo U('Receivables/create', ['mid'=>I('get.mid', 0, 'int')]);?>">添加收款</a>
						</li>
						<?php if($permission_list['PAY_METHOD.VIEW'] == 1): ?><li>
								<a href="<?php echo U('Receivables/payMethod', ['mid'=>I('get.mid', 0, 'int')]);?>">支付方式</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECEIVABLES_TYPE.VIEW'] == 1): ?><li>
								<a href="<?php echo U('Receivables/receivablesType', ['mid'=>I('get.mid', 0, 'int')]);?>">收款类型</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECEIVABLES_TYPE.VIEW'] == 1): ?><li>
								<a href="<?php echo U('Receivables/posMachine', ['mid'=>I('get.mid', 0, 'int')]);?>">POS机</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['COUPON.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Coupon): ?>active<?php endif; ?>">
					<a href="<?php echo U('Coupon/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-tags"></i> <span class="nav-label">项目管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MESSAGE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Message): ?>active<?php endif; ?>">
					<a href="<?php echo U('Message/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-comment"></i> <span class="nav-label">消息管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['MESSAGE.CREATE'] == 1): ?><li>
								<a href="<?php echo U('Message/Create', ['mid'=>I('get.mid', 0, 'int')]);?>">新建消息模板</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['BADGE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Badge): ?>active<?php endif; ?>">
					<a href="<?php echo U('Badge/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-bookmark"></i> <span class="nav-label">胸卡设计</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['ROOM.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Hotel or $c_name == Room): ?>active<?php endif; ?>">
					<a href="<?php echo U('Hotel/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-home"></i> <span class="nav-label">酒店管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['CAR.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Car): ?>active<?php endif; ?>">
					<a href="<?php echo U('Car/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-plane"></i> <span class="nav-label">车辆管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['DINING_TABLE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == DiningTable): ?>active<?php endif; ?>">
					<a href="<?php echo U('DiningTable/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-glass"></i> <span class="nav-label">餐桌管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['REPORT.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Report): ?>active<?php endif; ?>">
					<a href="<?php echo U('Report/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-list-alt"></i> <span class="nav-label">报表管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span></a>
				</li><?php endif; ?>
			<?php if($permission_list['RECYCLE.VIEW'] == 1): ?><li class="side_item cls <?php if($c_name == Recycle): ?>active<?php endif; ?>">
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
						<?php if($permission_list['RECYCLE.VIEW-COUPON'] == 1): ?><li>
								<a href="<?php echo U('Recycle/coupon');?>">代金券</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-COUPON_ITEM'] == 1): ?><li>
								<a href="<?php echo U('Recycle/coupon_item');?>">代金券码</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-MESSAGE'] == 1): ?><li>
								<a href="<?php echo U('Recycle/message');?>">消息管理</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
		</ul>
	</div>
	<div class="message_box">
		<a href="javascript:void(0)"> <span class="icon_nav glyphicon glyphicon-envelope"></span>
			<span style="margin-left: 15px">站内信</span>
			<?php if(count($system_message) > 0): ?><span style="min-width: 20px; min-height: 20px; line-height: 20px; border-radius: 50%; background: red; display: inline-block; font-size: 10px; text-align: center"><?php echo count($system_message);?></span><?php endif; ?>
		</a>
	</div>
	<div class="message_modal hide">
		<div class="mes_title">站内信 <span class="mes_close">×</span></div>
		<div class="mes_list">
			<?php if(is_array($system_message)): $i = 0; $__LIST__ = $system_message;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$message): $mod = ($i % 2 );++$i;?><div class="mes_item">
					<b><?php echo ($i); ?>、</b><?php echo ($message); ?>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>
</div>
			<div class="mt_wrapper">
				<!--会议系统头部  公用-->
<div class="mt_topbar clearfix">
	<div class="topbar_left">
		<a class="meeting_name "><i class="glyphicon glyphicon-fire"></i><?php echo ($meeting_name); ?></a>
		<a href="<?php echo U('Meeting/manage', ['type'=>'ing']);?>" class="back_meetingList"><i class="glyphicon glyphicon-list"></i>返回会议列表</span></a>
	</div>
	<ul class="nav_info clearfix">
		<li class="name">
			<i class="glyphicon glyphicon-user"></i> <span><?php echo I('session.MANAGER_EMPLOYEE_NAME','');?></span>
			<div class="hidden_dropDown hide">
				<div class="dropDown_item">
					<a href="<?php echo U('My/information');?>">个人信息</a>
				</div>
				<div class="dropDown_item">
					<a href="<?php echo U('My/password');?>">修改密码</a>
				</div>
				<div class="dropDown_item">
					<a href="<?php echo U('My/logout');?>">注销</a>
				</div>
			</div>
		</li>
	</ul>
</div>
				<?php if($permission_list['CLIENT.ALTER'] == 1): ?><div class="main_body">
						<section class="content">
							<div class="table_wrap">
								<div class="nav_tab clearfix">
									<div class="nav_tab_li active">
										<a href="javascript:void(0)">参会人员信息修改</a>
									</div>
								</div>
							</div>
							<form class="form-horizontal" role="form" method="post" id="form" action="" onsubmit="return checkIsEmpty()">
								<input type="hidden" name="requestType" value="create">
								<div class="modal-body">
									<div class="form-group">
										<label for="name" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>姓名：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control name" name="name" id="name" value="<?php echo ($info["name"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="gender" class="col-sm-1 control-label">性别：</label>
										<div class="col-sm-11">
											<select name="gender" id="gender" class="form-control">
												<?php if($info["gender"] == 0): ?>1
													<option value="0" selected>未指定</option>
													<option value="1">男</option>
													<option value="2">女</option><?php endif; ?>
												<?php if($info["gender"] == 1): ?>2
													<option value="0">未指定</option>
													<option value="1" selected>男</option>
													<option value="2">女</option><?php endif; ?>
												<?php if($info["gender"] == 2): ?>3
													<option value="0">未指定</option>
													<option value="1">男</option>
													<option value="2" selected>女</option><?php endif; ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="birthday" class="col-sm-1 control-label">出生日期：</label>
										<div class="col-sm-11">
											<input class="form-control" id="birthday" name="birthday" value="<?php echo ($info["birthday"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="address" class="col-sm-1 control-label">地址：</label>
										<div class="col-sm-11">
											<div class="input-group address_map">
												<input type="text" class="form-control" name="address" id="address" value="<?php echo ($info["address"]); ?>">
												<span class="input-group-addon glyphicon glyphicon-send get_map"></span>
											</div>
											<div id="allmap" class="hide" style="width: 100%; height: 400px;"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="id_card_number" class="col-sm-1 control-label">身份证号：</label>
										<div class="col-sm-11">
											<input type="text" value="<?php echo ($info["id_card_number"]); ?>" class="form-control id_card_number" name="id_card_number" id="id_card_number">
										</div>
									</div>
									<div class="form-group">
										<label for="title" class="col-sm-1 control-label">职称：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control title" name="title" id="title" value="<?php echo ($info["title"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="position" class="col-sm-1 control-label">职务：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control position" name="position" id="position" value="<?php echo ($info["position"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="mobile" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>手机号：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control mobile" name="mobile" id="mobile" value="<?php echo ($info["mobile"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="email" class="col-sm-1 control-label">电子邮箱：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control email" name="email" id="email" value="<?php echo ($info["email"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="unit" class="col-sm-1 control-label "><b style="vertical-align: middle;color: red;">*</b>单位名称：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control unit" name="unit" id="unit" value="<?php echo ($info["unit"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="is_new" class="col-sm-1 control-label">是否新客：</label>
										<div class="col-sm-11">
											<?php switch($info["is_new"]): case "1": ?><!--suppress XmlDuplicatedId -->
													<select id="is_new" name="is_new" class="form-control">
														<option value="1" selected>新客</option>
														<option value="0">老客</option>
													</select><?php break;?>
												<?php case "0": ?><!--suppress XmlDuplicatedId -->
													<select id="is_new" name="is_new" class="form-control">
														<option value="1">新客</option>
														<option value="0" selected>老客</option>
													</select><?php break; endswitch;?>
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
										<label for="registration_date" class="col-sm-1 control-label">报名时间：</label>
										<div class="col-sm-11">
											<input class="form-control" id="registration_date" name="registration_date" value="<?php echo ($join_record["registration_date"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="comment" class="col-sm-1 control-label">备注：</label>
										<div class="col-sm-11">
											<textarea class="form-control comment" name="comment" id="comment"><?php echo ($info["comment"]); ?></textarea>
										</div>
									</div>
									<div class="form-group">
										<label for="column1" class="col-sm-1 control-label">保留字段1：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column1" name="column1" id="column1" value="<?php echo ($info["column1"]); ?>">
										</div>
									</div>
									<!--<div class="form-group">
										<label for="column2" class="col-sm-1 control-label">保留字段2：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column2" name="column2" id="column2" value="<?php echo ($info["column2"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="column3" class="col-sm-1 control-label">保留字段3：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column3" name="column3" id="column3" value="<?php echo ($info["column3"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="column4" class="col-sm-1 control-label">保留字段4：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column4" name="column4" id="column4" value="<?php echo ($info["column4"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="column5" class="col-sm-1 control-label">保留字段5：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column5" name="column5" id="column5" value="<?php echo ($info["column5"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="column6" class="col-sm-1 control-label">保留字段6：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column6" name="column6" id="column6" value="<?php echo ($info["column6"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="column7" class="col-sm-1 control-label">保留字段7：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column7" name="column7" id="column7" value="<?php echo ($info["column7"]); ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="column8" class="col-sm-1 control-label">保留字段8：</label>
										<div class="col-sm-11">
											<input type="text" class="form-control column8" name="column8" id="column8" value="<?php echo ($info["column8"]); ?>">
										</div>
									</div>-->
								</div>
								<div class="modal-footer text_center">
									<button type="submit" class="btn btn-primary">提交</button>
								</div>
								<input type="hidden" name="redirectUrl">
							</form>
						</section>
					</div><?php endif; ?>
			</div>
		</div>
	</div>
	<script>
		var AlterObject = {
			object:{
				developConsultantSelect:$('#develop_consultant').QuasarSelect({
					name        :'develop_consultant',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_name_list);?>',
					idInput     :'selected_develop_consultant',
					idHidden    :'selected_develop_consultant_form',
					placeholder :'',
					hasEmptyItem:false,
					defaultValue:'<?php echo ($info["develop_consultant"]); ?>',
					defaultHtml :'<?php echo ($info["develop_consultant"]); ?>'
				}),
				serviceConsultantSelect:$('#service_consultant').QuasarSelect({
					name        :'service_consultant',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($employee_name_list);?>',
					idInput     :'selected_service_consultant',
					idHidden    :'selected_service_consultant_form',
					placeholder :'',
					hasEmptyItem:false,
					defaultValue:'<?php echo ($info["service_consultant"]); ?>',
					defaultHtml :'<?php echo ($info["service_consultant"]); ?>'
				}),
				birthDate              :jeDate({
					dateCell:"#birthday",
					format  :"YYYY-MM-DD",
					isClear :false, // isClear:false, // 时分秒--true
					/*	minDate :"2015-10-19 00:00:00",
					 maxDate :"2016-11-8 00:00:00"*/
				}),
				registrationDate       :jeDate({
					dateCell:"#registration_date",
					format  :"YYYY-MM-DD hh:mm:ss",
					isTime  :true, // isClear:false, // 时分秒--true
					/*	minDate :"2015-10-19 00:00:00",
					 maxDate :"2016-11-8 00:00:00"*/
				}),
				toast                  :$().QuasarToast(),
				loading                :$().QuasarLoading(),
				// 地址
				citySelect             :$("#address").citySelect({
					nodata:"none",
					url   :'<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/city.min.js'
				})
			}
		};
		$(function(){
			// 百度地图API功能
			var map = new BMap.Map("allmap");
			map.centerAndZoom(new BMap.Point(116.404, 39.915), 11);
			var local = new BMap.LocalSearch(map, {
				renderOptions:{map:map}
			});
			$('.get_map').on('click', function(){
				$(this).parents('.form-group').find('#allmap').removeClass('hide');
				var value = $(this).parents('.form-group').find('#place').val();
				local.search(value);
			});
		});
	</script>
</body>
</html>