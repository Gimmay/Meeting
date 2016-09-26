<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>客户模块 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.css">
	<!--<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/custom.css">-->
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/icheck-1.x/skins/all.css">
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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.js"></script>
	<script src="<?php echo (COMMON_SCRIPT); ?>"></script>
	<script src="<?php echo (SELF_SCRIPT); ?>"></script>
</head>
<body data-meeting-id="<?php echo I('get.mid');?>">
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
					<section class="content">
						<header class="c_header">
							<div class="function_list clearfix">
								<div class="function_btn bg-warning" data-toggle="modal" data-target="#create_meeting" data-backdrop="static">
									<a href="<?php echo U('Client/create');?>"> <i></i>
										<p>新建客户</p>
									</a>
								</div>
								<div class="function_btn bg-danger batch_delete_btn_confirm"  data-toggle="modal" data-target="#batch_delete_meeting" data-backdrop="static">
									<i></i>
									<p>批量删除</p>
								</div>
								<div class="function_btn bg-success batch_delete_btn_confirm">
									<i></i>
									<p>批量签到</p>
								</div>
								<div class="function_btn bg-28B778 import_excel_btn">
									<input type="file" id="import_excel" name="import_excel">
									<i></i>
									<p>导入Excel</p>
								</div>
								<!--<div class="function_btn bg-info batch_delete_btn_confirm">
									<a href="<?php echo U('exportClientDataTemplate');?>">
										<i></i>
										<p>导出Excel</p>
									</a>
								</div>-->
								<div class="function_btn bg-f63 batch_delete_btn_confirm">
									<a href="<?php echo U('exportClientDataTemplate');?>">
										<i></i>
										<p>下载Excel模板</p>
									</a>
								</div>
							</div>
						</header>
						<div class="repertory clearfix">
							<form action="" method="get">
								<div class="input-group repertory_text">
									<input type="search" name="keyword" class="form-control" placeholder="客户名称/拼音简码" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default mian_search">搜索客户</button>
									</span>
								</div>
								<a type="reset" class="btn btn-default mian_search" href="/Meeting/manage.aspx">查看所有</a>
							</form>
							<form action="" method="get" class="sign_check">
								<div class="input-group pull-left">
									<input type="checkbox" class="icheck" placeholder="" value="">&nbsp;&nbsp;全部
								</div>
								<div class="input-group pull-left">
									<input type="checkbox" class="icheck" placeholder="" value="">&nbsp;&nbsp;已签到
								</div>
								<div class="input-group pull-left">
									<input type="checkbox" class="icheck" placeholder="" value="">&nbsp;&nbsp;未签到
								</div>
							</form>
						</div>
						<div class="table_wrap">
							<table class="table table-bordered" id="tableExcel" style="text-align: center">
								<thead>
									<tr>
										<td width="5%" class="all_check">
											<input type="checkbox" class="icheck" placeholder="" value="">
										</td>
										<td width="10%">姓名</td>
										<td width="10%">性别</td>
										<td width="10%">手机号</td>
										<td width="10%">会所名称</td>
										<td width="10%">开拓顾问</td>
										<td width="10%">服务顾问</td>
										<td width="10%">创建时间</td>
										<td width="10%">备注</td>
										<td width="15%">操作</td>
									</tr>
								</thead>
								<tbody>
										<tr>
											<td class="check_item"><input type="checkbox" class="icheck" value="<?php echo ($vo["id"]); ?>" placeholder=""></td>
											<td><?php echo ($vo["name"]); ?></td>
											<td>
											<span class="color-info">
												<?php switch($vo["status"]): case "1": ?>新建<?php break;?>
													<?php case "2": ?>进行中<?php break; endswitch;?>
											</span>
											</td>
											<td>手机号码</td>
											<td>会所名称</td>
											<td>开拓顾问</td>
											<td>服务顾问</td>
											<td>创建时间</td>
											<td>备注</td>
											<td>
												<div class="btn-group" data-id="<?php echo ($vo["id"]); ?>">
													<a href="" type="button" class="btn btn-default btn-xs modify_btn">签到</a>
													<a href="<?php echo U('Client/alter',['id'=>$vo['id']]);?>" type="button" class="btn btn-default btn-xs modify_btn">修改</a>
													<button type="submit" class="btn btn-default btn-xs delete_btn" data-toggle="modal" data-target="#delete_meeting">删除</button>
												</div>
											</td>
										</tr>
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
					<h2 class="modal-title" id="delete_role_title" value="<{$.content.id}>">删除会议</h2>
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
	<script>
		var ManageObject = {
			object:{
				/*toast         :$().QuasarToast(),*/
				icheck:$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				})
			}
		}
	</script>
</body>
</html>