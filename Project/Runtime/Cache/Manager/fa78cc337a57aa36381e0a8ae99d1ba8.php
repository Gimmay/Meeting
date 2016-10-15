<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>代金券模块 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.css">
	<!--<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/custom.css">-->
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
								<div class="function_btn bg-warning" data-toggle="modal" data-target="#create_coupon">
									<i></i>
									<p>新增代金券</p>
								</div>
								<div class="function_btn bg-danger batch_delete_btn_confirm" data-toggle="modal" data-target="#batch_delete_coupon">
									<i></i>
									<p>批量删除</p>
								</div>
							</div>
						</header>
						<div class="repertory clearfix">
							<form action="" method="get">
								<div class="input-group repertory_text">
									<input type="search" name="keyword" class="form-control" placeholder="代金券/拼音简码" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default mian_search">搜索代金券</button>
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
										<td width="10%">代金券名称</td>
										<td width="10%">会议名称(张数)</td>
										<td width="5%">数量</td>
										<td width="10%">价格</td>
										<td width="15%">开始时间</td>
										<td width="15%">结束时间</td>
										<td width="10%">备注</td>
										<td width="20%">操作</td>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($coupon_list)): $i = 0; $__LIST__ = $coupon_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
										<td class="check_item">
											<input type="checkbox" class="icheck" placeholder="" value="<?php echo ($list["id"]); ?>">
										</td>
										<td><?php echo ($list["name"]); ?></td>
										<td><?php echo ($list["meeting"]); ?></td>
										<td><a href="javascript:void(0)" class="link color-primary"><?php echo ($list["count"]); ?></a></td>
										<td><?php echo ($list["price"]); ?></td>
										<td><?php echo (date('Y-m-d',$list["start_time"])); ?></td>
										<td><?php echo (date('Y-m-d',$list["end_time"])); ?></td>
										<td><?php echo ($list["comment"]); ?></td>
										<td>
											<div class="btn-group" data-id="<?php echo ($list["id"]); ?>">
												<?php if($max_role_level <= $single['level']): ?><a href="<?php echo U('Details',['id'=>$list['id']]);?>" type="button" class="btn btn-default btn-xs seeList">查看列表</a>
													<button type="button" class="btn btn-default btn-xs modify_btn" data-toggle="modal" data-target="#modify_coupon">修改</button>
													<button type="button" class="btn btn-default btn-xs delete_btn" data-toggle="modal" data-target="#delete_coupon">删除</button><?php endif; ?>
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
	<!-- 新增代金券 -->
	<div class="modal fade" id="create_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width: 60%">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title" id="create_role_title">新增代金券</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="" onsubmit="return checkCreate()">
					<input type="hidden" name="requestType" value="create">
					<div class="modal-body">
						<div class="form-group">
							<label class="col-sm-2 control-label"><b style="vertical-align: middle;color: red;">*</b>方式：</label>
							<div class="col-sm-10" style="margin:0">
								<ul class="nav nav-pills" id="create_way">
									<li role="presentation" class="active"><a href="#">单个新增</a></li>
									<li role="presentation"><a href="#">批量新增</a></li>
								</ul>
							</div>
						</div>
						<div class="form-group">
							<label for="meeting" class="col-sm-2 control-label"><b style="vertical-align: middle;color: red;">*</b>会议：</label>
							<div class="col-sm-10">
								<div id="meeting"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="coupon_name" class="col-sm-2 control-label"><b style="vertical-align: middle;color: red;">*</b>代金券名称：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control name" name="name" id="coupon_name">
							</div>
						</div>
						<div class="form-group">
							<label for="price" class="col-sm-2 control-label"><b style="vertical-align: middle;color: red;">*</b>价格：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control price" name="price" id="price">
							</div>
						</div>
						<!-- 单个新增 -->
						<div class="single_box c_way">
							<div class="form-group">
								<label class="col-sm-2 control-label"><b style="vertical-align: middle; color: red;">*</b>代金券号：</label>
								<div class="col-sm-10">
									<input type="text" name="coupon_area" class="coupon_area form-control">
								</div>
							</div>
						</div>
						<!-- 批量新增 -->
						<div class="mutil_box hide c_way">
							<div class="form-group">
								<label for="prefix" class="col-sm-2 control-label">前缀：</label>
								<div class="col-sm-10">
									<input type="text" class="form-control prefix" name="prefix" id="prefix">
								</div>
							</div>
							<div class="form-group">
								<label for="start_number" class="col-sm-2 control-label">开始段：</label>
								<div class="col-sm-10">
									<input type="number" class="form-control start_number" name="start_number" id="start_number">
								</div>
							</div>
							<div class="form-group">
								<label for="length" class="col-sm-2 control-label">长度：</label>
								<div class="col-sm-10">
									<input type="number" class="form-control length" name="length" id="length">
								</div>
							</div>
							<div class="form-group">
								<label for="number" class="col-sm-2 control-label">数量：</label>
								<div class="col-sm-10">
									<input type="number" class="form-control number" name="number" id="number">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label"><b style="vertical-align: middle; color: red;">*</b>代金券号：</label>
								<div class="col-sm-10">
									<button type="button" class="btn btn-default auto_get_number">自动获取</button>
									<ul class="list_coupon_number clearfix">
									</ul>
									<input type="hidden" name="coupon_area" value="">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="start_time" class="col-sm-2 control-label"><b style="vertical-align: middle;color: red;">*</b>开始时间：</label>
							<div class="col-sm-10">
								<div class="input-group date form_datetime" id="start_time_wp">
									<input type="text" class="form-control start_time" name="start_time" id="start_time">
									<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="end_time" class="col-sm-2 control-label"><b style="vertical-align: middle;color: red;">*</b>结束时间：</label>
							<div class="col-sm-10">
								<div class="input-group date form_datetime" id="end_time_wp">
									<input type="text" class="form-control end_time" name="end_time" id="end_time">
									<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
								</div>
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
						<button type="submit" class="btn btn-primary">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 修改代金券 -->
	<div class="modal fade" id="modify_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title" id="modify_role_title">修改代金券</h2>
				</div>
				<form class="form-horizontal" role="form" action="" method="post" onsubmit="return checkAlter()">
					<input type="hidden" name="requestType" value="alter"> <input type="hidden" name="id">
					<div class="modal-body">

						<div class="form-group">
							<label for="coupon_name_a" class="col-sm-2 control-label color-red"><b style="vertical-align: middle;color: red;">*</b>券名称：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control name" name="name" id="coupon_name_a" value="">
							</div>
						</div>
						<div class="form-group">
							<label for="price_a" class="col-sm-2 control-label color-red"><b style="vertical-align: middle;color: red;">*</b>价格：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control price" name="price" id="price_a">
							</div>
						</div>
						<div class="form-group">
							<label for="start_time_wp_a" class="col-sm-2 control-label color-red"><b style="vertical-align: middle;color: red;">*</b>开始时间：</label>
							<div class="col-sm-10">
								<div class="input-group date form_datetime" id="start_time_wp_a">
									<input type="text" class="form-control start_time" name="start_time" id="start_time_a">
									<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="end_time_wp_a" class="col-sm-2 control-label color-red"><b style="vertical-align: middle;color: red;">*</b>结束时间：</label>
							<div class="col-sm-10">
								<div class="input-group date form_datetime" id="end_time_wp_a">
									<input type="text" class="form-control end_time" name="end_time" id="end_time_a">
									<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="create_role_comment" class="col-sm-2 control-label">备注：</label>
							<div class="col-sm-10">
								<textarea class="form-control comment" name="comment"></textarea>
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
	</div>
	<!-- 删除代金券 -->
	<div class="modal fade" id="delete_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h2 class="modal-title">删除代金券</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete"> <input type="hidden" name="id" value="">
					<div class="modal-body">
						是否删除代金券？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量删除 代金券-->
	<div class="modal fade" id="batch_delete_coupon" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量删除代金券</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete"> <input type="hidden" name="id" value="">
					<div class="modal-body">
						是否删除已选代金券？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary batch_delete_btn">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		var ManageObject = {
			object:{
				meetingSelect:$('#meeting').QuasarSelect({
					name        :'mid',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($meeting_list);?>',
					idInput     :'selected_meeting',
					idHidden    :'selected_meeting_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				meetingSelect:$('#meeting_a').QuasarSelect({
					name        :'mid',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($meeting_list);?>',
					idInput     :'selected_meeting_a',
					idHidden    :'selected_meeting_a_form',
					placeholder :'',
					hasEmptyItem:false
				}),
				time   :$('#start_time_wp,#end_time_wp,#start_time_wp_a,#end_time_wp_a').datetimepicker({
					language          :'zh-CN',
					todayBtn          :true,
					autoclose         :true,
					todayHighlight    :true,
					keyboardNavigation:true,
					forceParse        :true,
					format            :'yyyy-mm-dd',
					weekStart         :0,
					startView         :4,
					minView           :2,
					maxView           :4,
					minuteStep        :5
				}),
				icheck       :$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				}),
				toast        :$().QuasarToast(),
				loading      :$().QuasarLoading()
			}
		}
	</script>
</body>
</html>