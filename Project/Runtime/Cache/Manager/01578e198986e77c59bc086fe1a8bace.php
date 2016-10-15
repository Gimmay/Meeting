<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>会议模块 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.css">
	<!--<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/custom.css">-->
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/skins/all.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE); ?>">
	<link rel="stylesheet" href="<?php echo (SELF_STYLE); ?>">
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/jquery.cityselect.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/icheck.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/custom.min.js"></script>
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
					<section class="content">
						<header class="c_header">
							<div class="function_list clearfix">
								<div class="function_btn bg-warning" data-toggle="modal" data-target="#create_meeting" data-backdrop="static">
									<a href="<?php echo U('Meeting/create');?>"> <i></i>
										<p>创建会议</p>
									</a>
								</div>
								<div class="function_btn bg-danger batch_delete_btn_confirm"  data-toggle="modal" data-target="#batch_delete_meeting" data-backdrop="static">
									<i></i>
									<p>批量删除</p>
								</div>
							</div>
						</header>
						<div class="repertory clearfix">
							<form action="" method="get">
								<div class="input-group repertory_text">
									<input type="search" name="keyword" class="form-control" placeholder="会议名称/拼音简码" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default mian_search">搜索会议</button>
									</span>
								</div>
								<a type="reset" class="btn btn-default mian_search" href="/Meeting/manage.aspx">查看所有</a>
							</form>
						</div>
						<div class="table_wrap">
							<table class="table table-bordered" style="text-align: center">
								<thead>
									<tr>
										<td width="5%" class="all_check">
											<input type="checkbox" class="icheck" placeholder="" value="">
										</td>
										<td width="10%">名称</td>
										<td width="5%">状态</td>
										<td width="5%">类型</td>
										<td width="10%">主办方</td>
										<td width="10%">策划方</td>
										<td width="10%">举办地点</td>
										<td width="10%">开始结束时间</td>
										<td width="5%">负责人</td>
										<td width="10%">创建时间</td>
										<td width="30%">操作</td>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($content)): $i = 0; $__LIST__ = $content;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
											<td class="check_item"><input type="checkbox" class="icheck" value="<?php echo ($vo["id"]); ?>" placeholder=""></td>
											<td><?php echo ($vo["name"]); ?></td>
											<td>
											<span class="color-info">
												<?php switch($vo["status"]): case "0": ?>禁用<?php break;?>


													<?php case "1": ?>新建<?php break;?>


													<?php case "2": ?>进行中<?php break;?>


													<?php case "3": ?>结束<?php break; endswitch;?>
											</span>
											</td>
											<td>
											<span class="color-info ">
											<?php switch($vo["type"]): case "1": ?>成交会<?php break;?>
												<?php case "2": ?>特训营<?php break;?>
												<?php case "3": ?>优雅女子<?php break;?>
												<?php case "4": ?>招商会<?php break;?>
												<?php case "5": ?>启动大会<?php break; endswitch;?>
											</span>
											</td>
											<td><?php echo ($vo["host"]); ?></td>
											<td><?php echo ($vo["plan"]); ?></td>
											<td><?php echo ($vo["place"]); ?></td>
											<td><?php echo ($vo["start_time"]); ?><br>至<br><?php echo ($vo["end_time"]); ?></td>
											<td><?php echo ($vo["director_name"]); ?></td>
											<td><?php echo date('Y-m-d H:i:s', $vo['creatime']);?></td>
											<td>
												<div class="btn-group" data-id="<?php echo ($vo["id"]); ?>">
													<a href="<?php echo U('SignPlace/manage',['mid'=>$vo['id']]);?>" type="button" class="btn btn-default btn-xs modify_btn">签到点</a>
													<a type="button" class="btn btn-default btn-xs mes_btn" data-toggle="modal" data-target="#choose_message">选择消息模板</a>
													<a href="<?php echo U('Client/manage',['mid'=>$vo['id']]);?>" type="button" class="btn btn-default btn-xs modify_btn">参会人员</a>
													<a href="<?php echo U('Client/manage',['mid'=>$vo['id']]);?>" type="button" class="btn btn-default btn-xs modify_btn">胸卡设计</a>
													<!--<a href="<?php echo U('Coupon/manage',['id'=>$vo['id']]);?>" type="button" class="btn btn-default btn-xs">代金券</a>-->
													<a href="<?php echo U('Meeting/alter',['id'=>$vo['id']]);?>" type="button" class="btn btn-default btn-xs modify_btn">修改</a>
													<button type="submit" class="btn btn-default btn-xs delete_btn" data-toggle="modal" data-target="#delete_meeting">删除</button>
												</div>
											</td>
										</tr><?php endforeach; endif; else: echo "" ;endif; ?>
								</tbody>
							</table>
						</div>
						<div class="page_wrap">
							<ul class="pagination ">
								<?php echo ($page); ?>
							</ul>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
	<!-- 删除会议 -->
	<div class="modal fade" id="delete_meeting" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title" id="delete_role_title">删除会议</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否删除会议？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认删除</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量删除会议 -->
	<div class="modal fade" id="batch_delete_meeting" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量删除会议</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否删除选中会议？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认删除</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 选择消息模板 -->
	<div class="modal fade" id="choose_message" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">选择消息模板</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="" onsubmit="return checkIsEmpty()">
					<input type="hidden" name="requestType" value="message">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						<div class="form-group">
							<label for="sign_mes" class="col-sm-2 control-label">签到：</label>
							<div class="col-sm-8">
								<div id="sign_mes"></div>
							</div>
							<div class="col-sm-2">
								<button type="button" class="btn btn-sm btn-primary mes_preview_btn">预览</button>
							</div>
						</div>
						<div class="form-group">
							<label for="unti_sign_mes" class="col-sm-2 control-label">取消签到：</label>
							<div class="col-sm-8">
								<div id="unti_sign_mes"></div>
							</div>
							<div class="col-sm-2">
								<button type="button" class="btn btn-sm btn-primary mes_preview_btn">预览</button>
							</div>
						</div>
						<div class="form-group">
							<label for="receivables_mes" class="col-sm-2 control-label">收款：</label>
							<div class="col-sm-8">
								<div id="receivables_mes"></div>
							</div>
							<div class="col-sm-2">
								<button type="button" class="btn btn-sm btn-primary mes_preview_btn">预览</button>
							</div>
						</div>
						<div class="mes_preview">
							我在马路
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		var ManageObject = {
			object:{
				signMessageSelect:$('#sign_mes').QuasarSelect({
					name        :'sign_mes',
					classStyle  :'form-control',
					idInput     :'selected_sign_mes',
					idHidden    :'selected_sign_mes_form',
					data        :'<?php echo json_encode($message);?>',
					placeholder :'',
					hasEmptyItem:false
				}),
				antiSignMessageSelect:$('#unti_sign_mes').QuasarSelect({
					name        :'anti_sign_mes',
					classStyle  :'form-control',
					idInput     :'selected_unti_sign_mes',
					idHidden    :'selected_unti_sign_mes_form',
					data        :'<?php echo json_encode($message);?>',
					placeholder :'',
					hasEmptyItem:false
				}),
				receivablesMessageSelect:$('#receivables_mes').QuasarSelect({
					name        :'receivables_mes',
					classStyle  :'form-control',
					idInput     :'selected_receivables_mes',
					idHidden    :'selected_receivables_mes_form',
					data        :'<?php echo json_encode($message);?>',
					placeholder :'',
					hasEmptyItem:false
				}),
				toast         :$().QuasarToast(),
				icheck:$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				})
			}
		}
	</script>
</body>
</html>