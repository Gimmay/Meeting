<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>员工管理 - 会议系统</title>
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
					<i class="icon_nav glyphicon glyphicon-bookmark"></i> <span class="nav-label">胸卡设计</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
			</li>
			<li class="side_item <?php if('Room'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Room/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-home"></i> <span class="nav-label">房间管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
			</li>
			<li class="side_item <?php if('Car'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Car/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-plane"></i> <span class="nav-label">车辆管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
			</li>
			<li class="side_item <?php if('DiningBoard'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('DiningBoard/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-glass"></i> <span class="nav-label">餐桌管理</span>
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
					<section class="content">
						<header class="c_header">
							<div class="function_list clearfix">
								<?php if($permission_list['createEmployee'] == 1): ?><div class="function_btn bg-warning" data-toggle="modal" data-target="#create_employee">
										<a href="<?php echo U('Employee/create');?>"> <i></i>
											<p>新增员工</p>
										</a>
									</div><?php endif; ?>
								<?php if($permission_list['deleteEmployee'] == 1): ?><div class="function_btn bg-danger batch_delete_btn_confirm" data-toggle="modal" data-target="#batch_delete_employee">
										<i></i>
										<p>批量删除</p>
									</div><?php endif; ?>
							</div>
						</header>
						<div class="repertory clearfix">
							<form action="" method="get">
								<div class="input-group repertory_text">
									<input type="search" name="keyword" class="form-control" placeholder="工号/名称/拼音简码/手机号" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default mian_search">搜索员工</button>
									</span>
								</div>
								<a type="reset" class="btn btn-default mian_search" href="<?php echo U('manage');?>">查看所有</a>
							</form>
						</div>
						<div class="table_wrap">
							<div class=""></div>
							<table class="table table-bordered" style="text-align: center">
								<thead>
									<tr>
										<td width="5%" class="all_check">
											<input type="checkbox" class="icheck" placeholder="" value="">
										</td>
										<td width="5%">工号</td>
										<td width="10%">名称</td>
										<td width="5%">性别</td>
										<td width="5%">状态</td>
										<td width="10%">手机</td>
										<td width="10%">部门</td>
										<td width="15%">角色</td>
										<td width="15%">创建日期</td>
										<td width="20%">操作</td>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><tr>
											<td class="check_item">
												<input type="checkbox" class="icheck" placeholder="" value="<?php echo ($single["id"]); ?>">
											</td>
											<td><?php echo ($single["code"]); ?></td>
											<td data-id="<?php echo ($single["id"]); ?>"><?php echo ($single["name"]); ?></td>
											<td>
												<?php switch($single["gender"]): case "0": ?>未设置<?php break;?>
													<?php case "1": ?>男<?php break;?>
													<?php case "2": ?>女<?php break; endswitch;?>
											</td>
											<td>
												<?php switch($single["status"]): case "0": ?><span class="color-danger">禁用</span><?php break;?>
													<?php case "1": ?><span class="color-info">可用</span><?php break; endswitch;?>
											</td>
											<td><?php echo ($single["mobile"]); ?></td>
											<td><a href="<?php echo U('manage', ['did'=>$single['did']]);?>"><?php echo ($single["department"]); ?></a></td>
											<td><?php echo ($single["role"]); ?></td>
											<td><?php echo date('Y-m-d H:i:s', $single['creatime']);?></td>
											<td>
												<div class="btn-group" data-id="<?php echo ($single["id"]); ?>">
													<?php if($max_role_level <= $single['roleLevel']): if($permission_list['assignRoleForEmployee'] == 1): ?><button type="button" class="btn btn-default btn-xs btn_distribution_role" data-toggle="modal" data-target="#distribution_role">分配角色</button><?php endif; endif; ?>
													<?php if($permission_list['assignPermissionForEmployee'] == 1): ?><button type="button" class="btn btn-default btn-xs authorize_btn" data-toggle="modal" data-target="#authorize_role">授权</button><?php endif; ?>
													<?php if($permission_list['alterEmployee'] == 1): ?><a href="<?php echo U('Employee/alter',['id'=>$single['id']]);?>" type="button" class="btn btn-default btn-xs modify_btn">修改</a><?php endif; ?>
													<?php if($permission_list['alterEmployeePassword'] == 1): ?><button type="button" class="btn btn-default btn-xs btn_alter_password" data-toggle="modal" data-target="#alter_password">修改密码</button><?php endif; ?>
													<?php if($permission_list['resetEmployeePassword'] == 1): ?><button type="button" class="btn btn-default btn-xs btn_reset_password" data-toggle="modal" data-target="#reset_password">重置密码</button><?php endif; ?>
													<?php if($permission_list['deleteEmployee'] == 1): ?><button type="button" class="btn btn-default btn-xs delete_btn" data-toggle="modal" data-target="#delete_employee">删除</button><?php endif; ?>
												</div>
											</td>
										</tr><?php endforeach; endif; else: echo "" ;endif; ?>
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
	<!-- 删除员工 -->
	<?php if($permission_list['deleteEmployee'] == 1): ?><div class="modal fade" id="delete_employee" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="delete_role_title">删除员工</h2>
					</div>
					<form class="form-horizontal" role="form" method="post" action="">
						<input type="hidden" name="requestType" value="delete"> <input type="hidden" name="id" value="">
						<div class="modal-body">
							是否删除员工？
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary">确认删除</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 批量删除 -->
	<?php if($permission_list['deleteEmployee'] == 1): ?><div class="modal fade" id="batch_delete_employee" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title">批量删除员工</h2>
					</div>
					<form class="form-horizontal" role="form" method="post" action="">
						<input type="hidden" name="requestType" value="delete"> <input type="hidden" name="id" value="">
						<div class="modal-body">
							是否删除已选员工？
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary batch_delete_btn">确认删除</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 分配角色 -->
	<?php if($permission_list['assignRoleForEmployee'] == 1): ?><div class="modal fade" id="distribution_role" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style="width: 80%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="distribution_role_title">分配角色</h2>
					</div>
					<form class="form-horizontal" role="form" method="post">
						<div class="modal-body role_body">
							<div class="btn_rect">
								<header><h3>已选择角色</h3></header>
								<section class="select_role_area select_area btn_green">
								</section>
							</div>
							<div class="btn_rect but_wrap">
								<header>
									<div class="form-group">
										<div class="input-group">
											<input class="form-control role_search_input" type="text" placeholder="角色名">
											<div class="input-group-addon role_search_btn">搜索</div>
										</div>
									</div>
								</header>
								<section class="all_role_area all_area">
								</section>
							</div>
						</div>
						<div class="modal-footer">
							<a href="javascript:history.go(0)" class="btn btn-default">关闭</a>
						</div>
						<input type="hidden" name="hide_employee_id">
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 员工授权 -->
	<?php if($permission_list['assignPermissionForEmployee'] == 1): ?><div class="modal fade" id="authorize_role" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style="width: 80%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="authorize_role_title">员工授权</h2>
					</div>
					<form class="form-horizontal" role="form" method="post">
						<div class="modal-body role_body">
							<div class="btn_rect">
								<header><h3>已选择角色</h3></header>
								<section class="select_area btn_green" id="authorize_select">
								</section>
							</div>
							<div class="btn_rect but_wrap">
								<header>
									<div class="form-group">
										<div class="input-group" id="authorize_search">
											<input class="form-control" type="text" placeholder="角色名">
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
	<!-- 修改密码 -->
	<?php if($permission_list['alterEmployeePassword'] == 1): ?><div class="modal fade" id="alter_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title" id="modify_role_title">员工密码修改</h2>
					</div>
					<form class="form-horizontal" role="form" action="" method="post">
						<input type="hidden" name="requestType" value="alter_password"> <input type="hidden" name="id">
						<div class="modal-body">
							<div class="form-group">
								<label for="old_password" class="col-sm-2 control-label">旧密码：</label>
								<div class="col-sm-10">
									<input type="password" class="form-control old_password" name="old_password" id="old_password">
								</div>
							</div>
							<div class="form-group">
								<label for="new_password" class="col-sm-2 control-label">新密码：</label>
								<div class="col-sm-10">
									<input type="password" class="form-control new_password" name="new_password" id="new_password">
								</div>
							</div>
							<div class="form-group">
								<label for="new_password2" class="col-sm-2 control-label">新密码：</label>
								<div class="col-sm-10">
									<input type="password" class="form-control new_password2" name="new_password2" id="new_password2">
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary password_btn">保存</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<!-- 重置密码 -->
	<?php if($permission_list['resetEmployeePassword'] == 1): ?><div class="modal fade" id="reset_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h2 class="modal-title">重置密码</h2>
					</div>
					<form class="form-horizontal" role="form" action="" method="post">
						<input type="hidden" name="requestType" value="reset_password"> <input type="hidden" name="id">
						<div class="modal-body">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="reset_password_employee_name">员工：</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="reset_password_employee_name" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="password_reset" class="col-sm-2 control-label">密码：</label>
								<div class="col-sm-10">
									<input type="text" class="form-control reset_password" name="password" id="password_reset" value="123456">
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
							<button type="submit" class="btn btn-primary password_reset_btn">保存</button>
						</div>
					</form>
				</div>
			</div>
		</div><?php endif; ?>
	<script>
		var ManageObject = {
			object:{
				toast  :$().QuasarToast(),
				icheck :$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				}),
				loading:$().QuasarLoading()
			}
		};
	</script>
</body>
</html>