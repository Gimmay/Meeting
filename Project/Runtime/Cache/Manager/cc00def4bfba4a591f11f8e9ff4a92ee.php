<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>会议恢复 - 会议系统</title>
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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/cityselect/jquery.cityselect.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/icheck.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/icheck-1.x/custom.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/Quasar.js" id="Quasar" data-mvc-name="<?php echo TP_SYS_PARAM;?>" data-page-suffix="<?php echo C('PAGE_SUFFIX');?>" ></script>
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
		<!--<li class="logout">
			<a href="<?php echo U('Employee/logout');?>"> <i class="glyphicon glyphicon-log-out"></i> <span>注销</span> </a>
		</li>-->
	</ul>
</div>
				<div class="main_body">
					<section class="content">
						<div class="return">
							<a class="btn btn-default" onclick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回上一页</a>
						</div>
						<header class="c_header">
							<div class="function_list clearfix">
								<div class="function_btn bg-danger batch_recover_btn_confirm" data-toggle="modal" data-target="#batch_recover_meeting" data-backdrop="static">
									<i></i>
									<p>恢复</p>
								</div>
							</div>
						</header>
						<div class="repertory clearfix">
							<form action="" method="get">
								<div class="input-group repertory_text">
									<input name="p" type="hidden" value="1">
									<input type="search" name="keyword" class="form-control" placeholder="会议名称/拼音简码" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default mian_search">搜索会议</button>
									</span>
								</div>
								<a type="reset" class="btn btn-default mian_search" href="<?php echo U('manage', ['mid'=>I('get.mid', 0, 'int'), 'sid'=>$_GET['sid']]);?>">查看所有</a>
							</form>
						</div>
						<div class="table_wrap">
							<table class="table table-bordered" style="text-align: center">
								<thead>
									<tr>
										<td width="5%" class="all_check">
											<input type="checkbox" class="icheck" placeholder="" value="">
										</td>
										<td width="5%">名称</td>
										<td width="10%">主办方</td>
										<td width="10%">策划方</td>
										<td width="10%">举办地点</td>
										<td width="10%">开始结束时间</td>
										<td width="10%">负责人</td>
										<td width="10%">创建日期</td>
										<td width="20%">操作</td>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><tr>
											<td class="check_item">
												<input type="checkbox" class="icheck" value="<?php echo ($single["id"]); ?>" placeholder="">
											</td>
											<td><?php echo ($single["name"]); ?></td>
											<td><?php echo ($single["host"]); ?></td>
											<td><?php echo ($single["plan"]); ?></td>
											<td><?php echo ($single["place"]); ?></td>
											<td><?php echo ($single["start_time"]); ?>--<?php echo ($single["end_time"]); ?></td>
											<td><?php echo ($single["creator"]); ?></td>
											<td><?php echo (date('Y-m-d',$single["creatime"])); ?></td>
											<td>
												<div class="btn-group" data-id="<?php echo ($single["id"]); ?>">
													<button type="submit" class="btn btn-default btn-xs recover_btn" data-toggle="modal" data-target="#recover_meeting" data-backdrop="static">恢复</button>
												</div>
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
					</section>
				</div>
			</div>
		</div>
	</div>
	<!-- 删除会议 -->
	<div class="modal fade" id="recover_meeting" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">恢复会议</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="recover">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否恢复会议？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量恢复会议 -->
	<div class="modal fade" id="batch_recover_meeting" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量恢复会议</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="multi_recover">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否恢复已选会议？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary batch_recover_btn">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		var MeetingObject = {
			object:{
				toast       :$().QuasarToast(),
				icheck      :$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				})
			}
		}
	</script>
</body>
</html>