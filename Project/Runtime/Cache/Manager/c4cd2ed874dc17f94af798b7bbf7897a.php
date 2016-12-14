<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>客户恢复 - 会议系统</title>
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
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/Quasar.js" id="quasar_script" data-url-sys-param="<?php echo TP_SYS_PARAM;?>" data-page-suffix="<?php echo C('PAGE_SUFFIX');?>"></script>
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
			<?php if($permission_list['MEETING.VIEW-BRIEF']): ?><li class="side_item <?php if($cv_name == Meeting_index): ?>active<?php endif; ?>">
					<a href="<?php echo U('Meeting/index', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-th-large"></i> <span class="nav-label">会议中心</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MEETING.VIEW'] == 1): ?><li class="side_item <?php if($cv_name == Meeting_alter): ?>active<?php endif; ?>">
					<a href="<?php echo U('Meeting/alter', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-list-alt"></i> <span class="nav-label">会议详情</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MESSAGE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Message): ?>active<?php endif; ?>">
					<a href="<?php echo U('Message/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-comment"></i> <span class="nav-label">消息管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['MESSAGE.CREATE'] == 1): ?><li>
								<a href="<?php echo U('Message/create', ['mid'=>I('get.mid', 0, 'int')]);?>">新建消息模板</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['BADGE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Badge): ?>active<?php endif; ?>">
					<a href="<?php echo U('Badge/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-bookmark"></i> <span class="nav-label">胸卡设计</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['COUPON.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Coupon): ?>active<?php endif; ?>">
					<a href="<?php echo U('Coupon/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-tags"></i> <span class="nav-label">项目管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['HOTEL.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Hotel or $c_name == Room): ?>active<?php endif; ?>">
					<a href="<?php echo U('Hotel/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-home"></i> <span class="nav-label">酒店管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['CAR.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Car): ?>active<?php endif; ?>">
					<a href="<?php echo U('Car/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-plane"></i> <span class="nav-label">车辆管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['MEETING.CONFIGURE'] == 1): ?><li class="side_item <?php if($cv_name == Meeting_configure): ?>active<?php endif; ?>">
					<a href="<?php echo U('Meeting/configure', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-cog"></i> <span class="nav-label">会议配置</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['CLIENT.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Client): ?>active<?php endif; ?>">
					<a href="<?php echo U('Client/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-user"></i> <span class="nav-label">参会人员</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['CLIENT.SIGN']): ?><li>
								<a href="<?php echo U('Client/sign', ['mid'=>I('get.mid', 0, 'int')]);?>">签到</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<?php if($permission_list['SIGN_PLACE.VIEW'] == 1): ?><li class="side_item <?php if($c_name == SignPlace): ?>active<?php endif; ?>">
					<a href="<?php echo U('SignPlace/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-star"></i> <span class="nav-label">签到点管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['GROUP.VIEW']): ?><li class="side_item <?php if($c_name == Grouping): ?>active<?php endif; ?>">
					<a href="<?php echo U('Grouping/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-compressed"></i> <span class="nav-label">分组管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
				</li><?php endif; ?>
			<?php if($permission_list['RECEIVABLES.VIEW'] == 1): ?><li class="side_item <?php if($c_name == Receivables): ?>active<?php endif; ?>">
					<a href="<?php echo U('Receivables/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">
						<i class="icon_nav glyphicon glyphicon-usd"></i> <span class="nav-label">收款管理</span>
						<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>
					<ul class="nav-second-level">
						<?php if($permission_list['RECEIVABLES.CREATE'] == 1): ?><li>
								<a href="<?php echo U('Receivables/create', ['mid'=>I('get.mid', 0, 'int')]);?>">添加收款</a>
							</li><?php endif; ?>
						<?php if($permission_list['PAY_METHOD.VIEW'] == 1): ?><li>
								<a href="<?php echo U('Receivables/payMethod', ['mid'=>I('get.mid', 0, 'int')]);?>">支付方式</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECEIVABLES_TYPE.VIEW'] == 1): ?><li>
								<a href="<?php echo U('Receivables/receivablesType', ['mid'=>I('get.mid', 0, 'int')]);?>">收款类型</a>
							</li><?php endif; ?>
						<?php if($permission_list['POS_MACHINE.VIEW'] == 1): ?><li>
								<a href="<?php echo U('Receivables/posMachine', ['mid'=>I('get.mid', 0, 'int')]);?>">POS机</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
			<!--<?php if($permission_list['DINING_TABLE.VIEW'] == 1): ?>-->
				<!--<li class="side_item <?php if($c_name == DiningTable): ?>active<?php endif; ?>">-->
				<!--<a href="<?php echo U('DiningTable/manage', ['mid'=>I('get.mid', 0, 'int')]);?>" class="side-item-link">-->
				<!--<i class="icon_nav glyphicon glyphicon-glass"></i> <span class="nav-label">餐桌管理</span>-->
				<!--<span class="arrow glyphicon glyphicon-chevron-left"></span> </a>-->
				<!--</li>-->
			<!--<?php endif; ?>-->
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
								<a href="<?php echo U('Recycle/client',['mid'=>I('get.mid',0,'int')]);?>">客户列表</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-EMPLOYEE'] == 1): ?><li>
								<a href="<?php echo U('Recycle/employee',['mid'=>I('get.mid',0,'int')]);?>">员工列表</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-ROLE'] == 1): ?><li>
								<a href="<?php echo U('Recycle/role',['mid'=>I('get.mid',0,'int')]);?>">角色列表</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-MEETING'] == 1): ?><li>
								<a href="<?php echo U('Recycle/meeting',['mid'=>I('get.mid',0,'int')]);?>">会议列表</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-COUPON'] == 1): ?><li>
								<a href="<?php echo U('Recycle/coupon',['mid'=>I('get.mid',0,'int')]);?>">代金券</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-COUPON_ITEM'] == 1): ?><li>
								<a href="<?php echo U('Recycle/couponItem',['mid'=>I('get.mid',0,'int')]);?>">代金券码</a>
							</li><?php endif; ?>
						<?php if($permission_list['RECYCLE.VIEW-MESSAGE'] == 1): ?><li>
								<a href="<?php echo U('Recycle/message',['mid'=>I('get.mid',0,'int')]);?>">消息管理</a>
							</li><?php endif; ?>
					</ul>
				</li><?php endif; ?>
		</ul>
	</div>
	<div class="message_box">
		<a href="javascript:void(0)"> <span class="icon_nav glyphicon glyphicon-envelope"></span>
			<span style="margin-left: 15px">站内信</span>
			<?php if(count($system_message['notRead']) > 0): ?><span id="message_count" style="min-width: 20px; min-height: 20px; line-height: 20px; border-radius: 50%; background: red; display: inline-block; font-size: 10px; text-align: center"><?php echo count($system_message['notRead']);?></span><?php endif; ?>
		</a>
	</div>
	<div class="message_modal hide">
		<div class="mes_title">站内信 <span class="mes_close">×</span></div>
		<div class="mes_list">
			<?php if(is_array($system_message["notRead"])): $i = 0; $__LIST__ = $system_message["notRead"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><!--suppress XmlDuplicatedId -->
				<div class="mes_item" id="system_message_<?php echo ($single["id"]); ?>" data-id="<?php echo ($single["id"]); ?>" onclick="readSystemMessage('<?php echo ($single["id"]); ?>')">
					<?php echo ($i); ?>、<span class="content bold"><?php echo ($single["message"]); ?></span> <span class="sign">未读</span>
					<button onclick="deleteSystemMessage('<?php echo ($single["id"]); ?>')" class="btn btn-default btn-xs del_btn" style="display: none">删除</button>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
			<?php if(is_array($system_message["read"])): $i = 0; $__LIST__ = $system_message["read"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$single): $mod = ($i % 2 );++$i;?><!--suppress XmlDuplicatedId -->
				<div class="mes_item" id="system_message_<?php echo ($single["id"]); ?>" data-id="<?php echo ($single["id"]); ?>">
					<?php echo ($i); ?>、<span class="content"><?php echo ($single["message"]); ?></span> <span class="sign">已读</span>
					<button onclick="deleteSystemMessage('<?php echo ($single["id"]); ?>')" class="btn btn-default btn-xs">删除</button>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>
	<script>
		function readSystemMessage(id){
			var url = "<?php echo U('RequestHandler/getHandler', ['requestType'=>'read_system_message', 'id'=>'__ID__']);?>";
			url     = url.replace('__ID__', id);
			Common.ajax({
				url     :url,
				callback:function(r){
					if(r.status){
						var $message           = $('#system_message_'+id);
						var $message_count_obj = $('#message_count');
						var count              = $message_count_obj.text();
						count--;
						if(count<=0) $message_count_obj.remove();
						else $message_count_obj.text(count);
						$message.find('span.sign').text('已读');
						$message.find('span.bold').removeClass('bold');
						$message.find('button.del_btn').show();
						$message.off('click').attr('onclick', null);
					}
				}
			})
		}
		function deleteSystemMessage(id){
			var url = "<?php echo U('RequestHandler/getHandler', ['requestType'=>'delete_system_message', 'id'=>'__ID__']);?>";
			url     = url.replace('__ID__', id);
			Common.ajax({
				url     :url,
				callback:function(r){
					if(r.status){
						var $message_count_obj = $('#message_count');
						var count              = $message_count_obj.text();
						count--;
						if(count<=0) $message_count_obj.remove();
						else $message_count_obj.text(count);
						$('#system_message_'+id).remove();
					}
				}
			})
		}
	</script>
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
				<?php if($permission_list['RECYCLE.VIEW-CLIENT']): ?><div class="main_body">
						<section class="content">
							<div class="return">
								<a class="btn btn-default" onclick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left color-primary"></span>返回上一页</a>
							</div>
							<header class="c_header">
								<div class="function_list clearfix">
									<?php if($permission_list['RECYCLE.RESTORE-CLIENT']): ?><div class="function_btn bg-danger batch_recover_btn_confirm" data-toggle="modal" data-target="#batch_recover_client" data-backdrop="static">
											<i></i>
											<p>恢复</p>
										</div><?php endif; ?>
								</div>
							</header>
							<div class="repertory clearfix">
								<form action="" method="get">
									<div class="input-group repertory_text">
										<input name="p" type="hidden" value="1">
										<input type="search" name="keyword" class="form-control" placeholder="客户名称/拼音简码" value="<?php echo I('get.keyword', '');?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default main_search">搜索客户</button>
									</span>
									</div>
									<a type="reset" class="btn btn-default main_search" href="<?php echo U('manage', ['mid'=>I('get.mid', 0, 'int'), 'sid'=>$_GET['sid']]);?>">查看所有&nbsp;(<?php echo ($statistics["total"]); ?>人)</a>
								</form>
							</div>
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
											<td width="10%">单位名称</td>
											<td width="15%">创建时间</td>
											<td width="15%">操作</td>
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
												<td><?php echo ($single["unit"]); ?></td>
												<td><?php echo (date('Y-m-d',$single["creatime"])); ?></td>
												<td>
													<div class="btn-group" data-id="<?php echo ($single["id"]); ?>">
														<?php if($permission_list['RECYCLE.RESTORE-CLIENT']): ?><button type="submit" class="btn btn-default btn-xs recover_btn" data-toggle="modal" data-target="#recover_client" data-backdrop="static">恢复</button><?php endif; ?>
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
					</div><?php endif; ?>
			</div>
		</div>
	</div>
	<!-- 恢复客户 -->
	<div class="modal fade" id="recover_client" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">恢复客户</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="recover"> <input type="hidden" name="id" value="">
					<div class="modal-body">
						是否恢复客户？
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 批量恢复 -->
	<div class="modal fade" id="batch_recover_client" tabindex="3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h2 class="modal-title">批量恢复客户</h2>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<input type="hidden" name="requestType" value="multi_recover">
					<input type="hidden" name="id" value="">
					<div class="modal-body">
						是否恢复已选客户？
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
		var ClientObject = {
			object:{
				toast :$().QuasarToast(),
				icheck:$('.icheck').iCheck({
					checkboxClass:'icheckbox_square-green',
					radioClass   :'iradio_square-green'
				})
			}
		}
	</script>
</body>
</html>