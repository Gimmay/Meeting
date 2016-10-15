<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>角色模块 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.css">
	<!--<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/custom.css">-->
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/skins/all.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE); ?>">
	<link rel="stylesheet" href="<?php echo (SELF_STYLE); ?>">
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/icheck.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/custom.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.js"></script>
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
								<?php if($permission_list['ROLE.CREATE'] == 1): ?><div class="function_btn bg-warning" data-toggle="modal" data-target="#create_role">
										<i></i>
										<p>新增角色</p>
									</div><?php endif; ?>
								<?php if($permission_list['ROLE.DELETE'] == 1): ?><div class="function_btn bg-danger batch_delete_btn_confirm" data-toggle="modal" data-target="#batch_delete_employee">
										<i></i>
										<p>批量删除</p>
									</div><?php endif; ?>
							</div>
						</header>
						<div class="repertory clearfix">
							<form action="" method="get">
								<div class="input-group repertory_text">
									<input type="search" name="keyword" class="form-control" placeholder="角色/拼音简码" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default mian_search">搜索角色</button>
									</span>
								</div>
								<a type="reset" class="btn btn-default mian_search" href="<?php echo U('manage');?>">查看所有</a>
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
										<td width="5%">用户数</td>
										<td width="5%">等级</td>
										<td width="15%">会议</td>
										<td width="10%">备注</td>
										<td width="15%">创建时间</td>
										<td width="30%">操作</td>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($role_list)): $i = 0; $__LIST__ = $role_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i; if($max_role_level <= $single['level']): ?><tr>
												<td class="check_item">
													<input type="checkbox" class="icheck" placeholder="" value="<?php echo ($single["id"]); ?>">
												</td>
												<td><?php echo ($single["name"]); ?></td>
												<td>
													<?php switch($single["status"]): case "0": ?><span class="color-danger">禁用</span><?php break;?>
														<?php case "1": ?><span class="color-info">可用</span><?php break; endswitch;?>
												</td>
												<td>
													<?php if($single['count'] == 0): echo ($single["count"]); ?>
														<?php else: ?>
														<a href="<?php echo U('Employee/manage', ['rid'=>$single['id']]);?>" class="link color-primary"><?php echo ($single["count"]); ?></a><?php endif; ?>
												</td>
												<td><?php echo ($single["level"]); ?></td>
												<td>
													<?php if($single[effect] == 0): ?>(系统全局)
														<?php else: ?>
														<?php echo ($single["meeting"]); endif; ?>
												</td>
												<td><?php echo ($single["comment"]); ?></td>
												<td><?php echo date('Y-m-d H:i:s', $single['creatime']);?></td>
												<td>
													<div class="btn-group" data-id="<?php echo ($single["id"]); ?>">
														<?php if($max_role_level <= $single['level']): if($permission_list['ROLE.ASSIGN-PERMISSION'] == 1): ?><button type="button" class="btn btn-default btn-xs authorize_btn" data-toggle="modal" data-target="#authorize_role">授权</button><?php endif; ?>
															<?php if($permission_list['ROLE.ALTER'] == 1): ?><button type="button" class="btn btn-default btn-xs modify_btn" data-toggle="modal" data-target="#modify_role">修改</button><?php endif; ?>
															<?php if($permission_list['ROLE.DELETE'] == 1): ?><button type="button" class="btn btn-default btn-xs delete_btn" data-toggle="modal" data-target="#delete_role">删除</button><?php endif; endif; ?>
													</div>
												</td>
											</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
								</tbody>
							</table>
						</div>
						<div class="page_wrap">
							<div class="pagination">
								<?php echo ($page_show); ?>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
	<!-- 新增角色 -->
	<?php if($permission_list['ROLE.CREATE'] == 1): ?><div class="modal fade" id="create_role" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="create_role_title">新增角色</h2>
					</div>
					<form class="form-horizontal" role="form" method="post" action="">
						<input type="hidden" name="requestType" value="create">
						<div class="modal-body">
							<div class="form-group">
								<label for="create_role_name" class="col-sm-2 control-label">名称：</label>
								<div class="col-sm-10">
									<input type="text" class="form-control name" name="name" id="create_role_name">
								</div>
							</div>
							<div class="form-group">
								<label for="authorize_role_level" class="col-sm-2 control-label">等级：</label>
								<div class="col-sm-10">
									<select class="form-control level" name="level" id="authorize_role_level">
										<?php if($max_role_level <= 1): ?><option value="1">1</option><?php endif; ?>
										<?php if($max_role_level <= 2): ?><option value="2">2</option><?php endif; ?>
										<?php if($max_role_level <= 3): ?><option value="3">3</option><?php endif; ?>
										<?php if($max_role_level <= 4): ?><option value="4">4</option><?php endif; ?>
										<?php if($max_role_level <= 5): ?><option value="5">5</option><?php endif; ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="create_role_status" class="col-sm-2 control-label">状态：</label>
								<div class="col-sm-10">
									<select name="status" id="create_role_status" class="form-control">
										<option value="1">可用</option>
										<option value="0">禁用</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="meeting_select" class="col-sm-2 control-label">作用域：</label>
								<div class="col-sm-10">
									<div id="meeting_select"></div>
								</div>
							</div>
							<div class="form-group">
								<label for="create_role_comment" class="col-sm-2 control-label">备注：</label>
								<div class="col-sm-10">
									<textarea class="form-control comment" name="comment" id="create_role_comment"></textarea>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary">新增</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 修改角色 -->
	<?php if($permission_list['ROLE.ALTER'] == 1): ?><div class="modal fade" id="modify_role" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="modify_role_title">修改角色</h2>
					</div>
					<form class="form-horizontal" role="form" action="" method="post">
						<input type="hidden" name="requestType" value="alter"> <input type="hidden" name="id">
						<div class="modal-body">
							<div class="form-group">
								<label for="modify_role_name" class="col-sm-2 control-label">名称：</label>
								<div class="col-sm-10">
									<input type="text" class="form-control name" name="name" id="modify_role_name">
								</div>
							</div>
							<div class="form-group">
								<label for="modify_role_level" class="col-sm-2 control-label">等级：</label>
								<div class="col-sm-10">
									<select class="form-control level" name="level" id="modify_role_level">
										<?php if($max_role_level <= 1): ?><option value="1">1</option><?php endif; ?>
										<?php if($max_role_level <= 2): ?><option value="2">2</option><?php endif; ?>
										<?php if($max_role_level <= 3): ?><option value="3">3</option><?php endif; ?>
										<?php if($max_role_level <= 4): ?><option value="4">4</option><?php endif; ?>
										<?php if($max_role_level <= 5): ?><option value="5">5</option><?php endif; ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="modify_role_status" class="col-sm-2 control-label">状态：</label>
								<div class="col-sm-10">
									<select name="status" id="modify_role_status" class="form-control">
										<option value="1">可用</option>
										<option value="0">禁用</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="modify_meeting_select" class="col-sm-2 control-label">作用域：</label>
								<div class="col-sm-10">
									<div id="modify_meeting_select"></div>
								</div>
							</div>
							<div class="form-group">
								<label for="modify_role_comment" class="col-sm-2 control-label">备注：</label>
								<div class="col-sm-10">
									<textarea class="form-control comment" name="comment" id="modify_role_comment"></textarea>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary">保存</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 删除角色 -->
	<?php if($permission_list['ROLE.DELETE'] == 1): ?><div class="modal fade" id="delete_role" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="delete_role_title">删除角色</h2>
					</div>
					<form class="form-horizontal" role="form" method="post" action="">
						<input type="hidden" name="requestType" value="delete"> <input type="hidden" name="id" value="">
						<div class="modal-body">
							是否删除角色？
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary">确认删除</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 角色授权 -->
	<?php if($permission_list['ROLE.ASSIGN-PERMISSION'] == 1): ?><div class="modal fade" id="authorize_role" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style="width: 80%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="authorize_role_title">角色授权</h2>
					</div>
					<form class="form-horizontal" role="form" method="post">
						<div class="modal-body role_body">
							<div class="btn_rect">
								<header><h3>已分配的权限</h3></header>
								<section class="select_area btn_green" id="authorize_select">
								</section>
							</div>
							<div class="btn_rect but_wrap">
								<header>
									<div class="form-group">
										<div class="input-group" id="authorize_search">
											<input class="form-control" type="text" placeholder="权限名称">
											<div class="input-group-addon">搜索</div>
										</div>
									</div>
								</header>
								<section class="all_area" id="authorize_all">
								</section>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						</div>
						<input type="hidden" name="hide_employee_id">
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 批量删除 -->
	<?php if($permission_list['ROLE.DELETE'] == 1): ?><div class="modal fade" id="batch_delete_employee" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title">批量删除角色</h2>
					</div>
					<form class="form-horizontal" role="form" method="post" action="">
						<input type="hidden" name="requestType" value="delete"> <input type="hidden" name="id" value="">
						<div class="modal-body">
							是否删除已选角色？
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary batch_delete_btn">确认删除</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<script>
		var ManageObject = {
			object:{
				meetingSelect      :$('#meeting_select').QuasarSelect({
					name        :'effect',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($meeting_list);?>',
					idInput     :'selected_meeting',
					idHidden    :'selected_meeting_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				modifyMeetingSelect:$('#modify_meeting_select').QuasarSelect({
					name        :'effect',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($meeting_list);?>',
					idInput     :'selected_meeting',
					idHidden    :'selected_meeting_form',
					placeholder :'',
					hasEmptyItem:false,
					defaultHtml :0,
					defaultValue:0
				}),
				icheck             :$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				}),
				loading            :$().QuasarLoading()
			}
		}
	</script>
</body>
</html>