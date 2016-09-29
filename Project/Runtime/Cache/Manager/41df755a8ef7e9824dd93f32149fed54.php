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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/Quasar.js" id="Quasar" data-mvc-name="<?php echo '/'.CONTROLLER_NAME.'/'.ACTION_NAME;?>" data-page-suffix="<?php echo C('PAGE_SUFFIX');?>" ></script>
	<script src="<?php echo (COMMON_SCRIPT); ?>"></script>
	<script src="<?php echo (SELF_SCRIPT); ?>"></script>
</head>
<body data-meeting-id="<?php echo I('get.mid');?>" data-place-id="<?php echo I('get.sid');?>">
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
					<span class="nav-label">员工管理</span>
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
					<span class="nav-label">角色管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</a>
			</li>
			<li class="side_item <?php if('Meeting'==$c_name or 'SignPlace'==$c_name or 'Client'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Meeting/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-home"></i>
					<span class="nav-label">会议管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</a>
				<ul class="nav-second-level">
					<li>
						<a href="<?php echo U('Meeting/create');?>">创建会议</a>
					</li>
				</ul>
			</li>
			<li class="side_item <?php if('Message'==$c_name) echo 'active'; ?>">
				<a href="<?php echo U('Message/manage');?>" class="side-item-link">
					<i class="icon_nav glyphicon glyphicon-home"></i>
					<span class="nav-label">消息管理</span>
					<span class="arrow glyphicon glyphicon-chevron-left"></span>
				</a>
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
						<div class="return">
							<a class="btn btn-default" href="<?php echo U('Meeting/manage');?>"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回上一页</a>
						</div>
						<header class="c_header">
							<div class="function_list clearfix">
								<div class="function_btn bg-warning" data-toggle="modal" data-target="#create_meeting" data-backdrop="static">
									<a href="<?php echo U('Client/create',['mid'=>I('get.mid')]);?>"> <i></i>
										<p>新建客户</p>
									</a>
								</div>
								<div class="function_btn bg-danger batch_delete_btn_confirm" data-toggle="modal" data-target="#batch_delete_client" data-backdrop="static">
									<i></i>
									<p>批量删除</p>
								</div>
								<div class="function_btn bg-f63	 batch_review_btn_confirm" data-toggle="modal" data-target="#batch_review_client" data-backdrop="static">
									<i></i>
									<p>批量审核</p>
								</div>
								<div class="function_btn bg-f63	 batch_anti_review_btn_confirm" data-toggle="modal" data-target="#batch_anti_review_client" data-backdrop="static">
									<i></i>
									<p>取消审核</p>
								</div>
								<div class="function_btn bg-success batch_sign_point"  data-toggle="modal" data-target="#batch_sign_point" data-backdrop="static">
									<i></i>
									<p>批量签到</p>
								</div>
								<div class="function_btn bg-success batch_anti_sign_point"  data-toggle="modal" data-target="#batch_anti_sign_point" data-backdrop="static">
									<i></i>
									<p>取消签到</p>
								</div>
								<div class="function_btn bg-f63	 batch_send_message_btn_confirm" data-toggle="modal" data-target="#batch_send_message" data-backdrop="static">
									<i></i>
									<p>批量发送消息</p>
								</div>
								<div class="function_btn bg-28B778 import_excel_btn">
									<form action="" method="post" enctype="multipart/form-data" name="fileForm" target="fileUpload">
										<input type="file" name="excel" accept=".xlsx, .xls" id="excel_file">
										<input name="requestType" value="import_excel" type="hidden">
										<i></i>
										<button type="submit" id="import_sub">提交</button>
										<p>导入Excel</p>
									</form>
								</div>
								<!-- 导入EXCEL -->
								<iframe name="fileUpload" id="fileUpload_iframe" width="0" height="0" style=" display: none;"></iframe>
								<div class="function_btn bg-info batch_delete_btn_confirm">
									<a href="<?php echo U('exportClientDataTemplate');?>"> <i></i>
										<p>导出Excel</p>
									</a>
								</div>
								<div class="function_btn bg-f63 batch_delete_btn_confirm">
									<a href="<?php echo U('exportClientDataTemplate');?>"> <i></i>
										<p>下载Excel模板</p>
									</a>
								</div>
								<div class="function_btn bg-warning assigned_sign" data-toggle="modal" data-target="#batch_alter_sign_point" data-backdrop="static">
									<i></i>
									<p>分配签到点</p>
								</div>
							</div>
						</header>
						<div class="repertory clearfix">
							<form action="" method="get">
								<div class="input-group repertory_text">
									<input name="p" type="hidden" value="1">
									<input type="search" name="keyword" class="form-control" placeholder="客户名称/拼音简码" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default mian_search">搜索客户</button>
									</span>
								</div>
								<a type="reset" class="btn btn-default mian_search" href="<?php echo U('manage', ['mid'=>I('get.mid', 0, 'int'), 'sid'=>$_GET['sid']]);?>">查看所有&nbsp;(<?php echo ($statistics["total"]); ?>人)</a>
							</form>
							<form action="" method="get" class="sign_check">
								<!--<div class="input-group pull-left">
									<input type="checkbox" id="check_all_condition" class="icheck">全部
								</div>-->
								<div class="input-group pull-left check_sign">
									<input type="checkbox" class="icheck" >&nbsp;&nbsp;签到 (<?php echo ($statistics["signed"]); ?>)
								</div>
								<div class="input-group pull-left check_review">
									<input type="checkbox" class="icheck">&nbsp;&nbsp;审核 (<?php echo ($statistics["audited"]); ?>)
								</div>
								<div class="input-group pull-left check_receivables">
									<input type="checkbox" class="icheck">&nbsp;&nbsp;收款
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
										<td width="5%">性别</td>
										<td width="10%">手机号</td>
										<td width="10%">会所名称</td>
										<td width="10%">创建时间</td>
										<td width="5%">状态</td>
										<td width="10%">审核/签到/打印</td>
										<td width="25%">操作</td>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><tr>
											<td class="check_item">
												<input type="checkbox" class="icheck" value="<?php echo ($single["id"]); ?>" placeholder="">
											</td>
											<td><?php echo ($single["name"]); ?></td>
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
												<?php switch($single["status"]): case "0": ?>禁用<?php break;?>
													<?php case "1": ?>可用<?php break; endswitch;?>
											</td>
											<td>
												<?php switch($single["audit_status"]): case "0": ?>未审核<?php break;?>
													<?php case "1": ?>已审核<?php break; endswitch;?>
												/
												<?php switch($single["sign_status"]): case "0": ?>未签到<?php break;?>
													<?php case "1": ?>已签到<?php break; endswitch;?>
												/
												<?php switch($single["print_status"]): case "0": ?>未打印<?php break;?>
													<?php case "1": ?>已打印<?php break; endswitch;?>
											</td>
											<td>
												<div class="btn-group" data-id="<?php echo ($single["id"]); ?>">
													<?php if(($single['audit_status'] == 1) and ($single['sign_status'] == 0)): ?><button type="button" class="btn btn-default btn-xs sign_btn">签到</button><?php endif; ?>
													<?php if(($single['audit_status'] == 1) and ($single['sign_status'] == 1)): ?><button type="button" class="btn btn-default btn-xs anti_sign_btn">取消签到</button><?php endif; ?>
													<?php if($single['audit_status'] == 1): ?><button type="button" class="btn btn-default btn-xs send_message_btn">发送消息</button><?php endif; ?>
													<?php switch($single["audit_status"]): case "0": ?><button type="button" class="btn btn-default btn-xs review_btn">审核</button><?php break;?>
														<?php case "1": ?><button type="button" class="btn btn-default btn-xs anti_review_btn">取消审核</button><?php break; endswitch;?>

													<button type="button" class="btn btn-default btn-xs receivables_btn" data-toggle="modal" data-target="#receivables_modal">收款</button>
													<button type="submit" class="btn btn-default btn-xs alter_sign_point_btn" data-toggle="modal" data-target="#alter_sign_point">分配签到点</button>
													<a href="<?php echo U('Client/alter',['id'=>$single['id']]);?>" class="btn btn-default btn-xs modify_btn">修改</a>
													<button type="submit" class="btn btn-default btn-xs delete_btn" data-toggle="modal" data-target="#delete_client">删除</button>
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
	<input type="hidden" id="Excal_hide_btn" data-toggle="modal" data-target="#ExcelHead">
	<!-- 单个修改签到点 -->
	<div class="modal fade" id="alter_sign_point" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h2 class="modal-title">修改签到点</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="receivables"> <input type="hidden" name="id">
					<div class="modal-body">
						<div class="form-group">
							<label for="sign_place" class="col-sm-3 control-label">选择签到点：</label>
							<div class="col-sm-9">
								<div id="sign_place"></div>
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
	<!-- 批量修改签到点 -->
	<div class="modal fade" id="batch_alter_sign_point" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h2 class="modal-title">批量修改签到点</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="receivables"> <input type="hidden" name="id">
					<div class="modal-body">
						<div class="form-group">
							<label for="sign_place" class="col-sm-3 control-label">选择签到点：</label>
							<div class="col-sm-9">
								<div id="batch_sign_place"></div>
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
	<!-- 收款 -->
	<div class="modal fade" id="receivables_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">收款</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="receivables"> <input type="hidden" name="id">
					<div class="modal-body">
						<div class="form-group">
							<label for="price" class="col-sm-2 control-label">收款金额：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control price" name="price" id="price">
							</div>
						</div>
						<div class="form-group">
							<label for="type" class="col-sm-2 control-label">收款类型：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control type" name="type" id="type">
							</div>
						</div>
						<div class="form-group">
							<label for="time" class="col-sm-2 control-label">收款时间：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control time" name="time" id="time">
							</div>
						</div>
						<div class="form-group">
							<label for="payee" class="col-sm-2 control-label">收款人：</label>
							<div class="col-sm-10">
								<div id="payee"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="coupon" class="col-sm-2 control-label">代金券：</label>
							<div class="col-sm-10">
								<div id="coupon"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="place" class="col-sm-2 control-label">收款地点：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control coupon" name="place" id="place">
							</div>
						</div>
						<div class="form-group">
							<label for="modify_role_status" class="col-sm-2 control-label">禁用：</label>
							<div class="col-sm-10">
								<select name="status" id="modify_role_status" class="form-control">
									<option value="1">是</option>
									<option value="0">否</option>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">收款</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 删除客户 -->
	<div class="modal fade" id="delete_client" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">删除客户</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否删除客户？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认删除</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量删除 -->
	<div class="modal fade" id="batch_delete_client" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量删除客户</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否删除已选客户？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary batch_delete_btn">确认删除</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量审核 -->
	<div class="modal fade" id="batch_review_client" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量审核客户</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否审核选择客户？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary batch_delete_btn">确认审核</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量取消审核 -->
	<div class="modal fade" id="batch_anti_review_client" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量取消客户审核</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="delete">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否取消已选客户审核？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary batch_anti_review_btn">确认取消</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量签到 -->
	<div class="modal fade" id="batch_sign_point" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量签到</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="sign">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						选择客户签到？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量取消签到 -->
	<div class="modal fade" id="batch_anti_sign_point" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量取消签到</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="sign">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						取消选择客户签到？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量发送消息 -->
	<div class="modal fade" id="batch_send_message" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量发送消息</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="sign">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						选择客户发送消息？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary batch_send_message_btn">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Excel表头字段对比 -->
	<div class="modal fade" id="ExcelHead" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h2 class="modal-title">Excel表头字段对比</h2>
				</div>
				<!--<form class="form-horizontal" role="form" method="post" action="">-->
					<div class="modal-body" style="max-height: 600px; overflow-y: scroll">
						<table class="table table-bordered" style="text-align: center">
							<thead>
								<tr>
									<th width="10%" class="all_check_excal">
										<input type="checkbox" class="icheck" placeholder="" value="">
									</th>
									<th width="20%">序号</th>
									<th width=30">您的Excal中的表头字段</th>
									<th width="40%">对应在系统中的字段</th>
								</tr>
							</thead>
							<tbody id="ExcelHeadTable">
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary btn_save_excel">保存字段</button>
					</div>
			<!--	</form>-->
			</div>
		</div>
	</div>
	<script>
		var ManageObject = {
			object:{
				payeeSelect :$('#payee').QuasarSelect({
					name        :'payee',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($dept);?>',
					idInput     :'selected_payee',
					idHidden    :'selected_payee_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				couponSelect:$('#coupon').QuasarSelect({
					name        :'coupon',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($dept);?>',
					idInput     :'selected_coupon',
					idHidden    :'selected_coupon_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				signPlaceSelect:$('#sign_place').QuasarSelect({
					name        :'sign_place',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($dept);?>',
					idInput     :'selected_coupon',
					idHidden    :'selected_coupon_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				batchSignPlaceSelect:$('#batch_sign_place').QuasarSelect({
					name        :'batch_sign_place',
					classStyle  :'form-control',
					data        :'<?php echo json_encode($dept);?>',
					idInput     :'selected_coupon',
					idHidden    :'selected_coupon_form',
					placeholder :'',
					justInput   :true,
					hasEmptyItem:false
				}),
				birthDate   :$('#birth_date').datetimepicker({
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