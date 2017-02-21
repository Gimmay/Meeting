<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Manager\Controller;

	use Manager\Logic\BadgeLogic;
	use Manager\Logic\ClientLogic;
	use Manager\Logic\ExcelLogic;
	use Quasar\StringPlus;
	use stdClass;
	use Think\Page;

	class ClientController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function manageUnit(){
			$logic = new ClientLogic();
			/* 处理POST提交请求 */
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['__return__']) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['CLIENT.VIEW']){
				/** @var \Core\Model\JoinModel $model */
				$model = D('Core/Join');
				/* 获取记录总数 */
				$total_list = $model->findRecordAll(2, ([
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted',
					'mid'     => $this->meetingID,
					'type'    => '会所'
				]));
				/* 分页设置 */
				$page_object = new Page(count($total_list), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				/* 当前页记录 */
				$unit_list       = $model->findRecordAll(2, [
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => 'sub.unit, sub.pinyin_code',
					'status'  => 'not deleted',
					'mid'     => $this->meetingID,
					'type'    => '会所'
				]);
				$enabled_count   = $model->findRecordAll(0, [
					'status' => 1,
					'mid'    => $this->meetingID,
					'type'   => '会所'
				]);
				$disabled_count  = $model->findRecordAll(0, [
					'status' => 0,
					'mid'    => $this->meetingID,
					'type'   => '会所'
				]);
				$all_count       = $model->findRecordAll(0, [
					'status' => 'not deleted',
					'mid'    => $this->meetingID,
					'type'   => '会所'
				]);
				$temp_total_list = $model->findRecordAll(2, [
					'status' => 1,
					'mid'    => $this->meetingID,
					'type'   => '会所'
				]);
				$this->assign('statistics', [
					'enabled'       => $enabled_count,
					'disabled'      => $disabled_count,
					'total'         => $all_count,
					'enabled_total' => count($temp_total_list),
				]);
				$this->assign('list', $unit_list);
				$this->assign('page_show', $page_show);
				$this->display();
			}
			else $this->error('您没有查看参会人员的权限');
		}

		public function manage(){
			$logic = new ClientLogic();
			/* 处理POST提交请求 */
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['__return__']) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['CLIENT.VIEW']){
				/** @var \Manager\Model\SignPlaceModel $sign_place_model */
				$sign_place_model = D('Manager/SignPlace');
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				/** @var \Core\Model\JoinModel $model */
				$model = D('Core/Join');
				/** @var \Core\Model\GroupModel $group_model */
				$group_model  = D('Core/Group');
				$client_logic = new ClientLogic();
				$options      = [];
				/* 处理URL参数 */
				if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 'not signed';
				if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 'not reviewed';
				if(isset($_GET['client_type'])) $options['isNew'] = I('get.client_type', 0, 'int') == 1 ? 1 : 0;
				if(isset($_GET['is_employee'])) $options['type'] = I('get.is_employee', 0, 'int') == 1 ? '内部员工' : 'not employee';
				if(isset($_GET['status'])) $options['status'] = I('get.status', 0, 'int') == 1 ? 1 : 0;
				if(isset($_GET['cid'])) $options['cid'] = I('get.cid', 0, 'int');
				/* 获取记录总数 */
				$total_list = $model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted',
					'mid'     => $this->meetingID
				], $options));
				/* 分页设置 */
				$page_object = new Page(count($total_list), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				/* 当前页记录 */
				$client_list = $model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'main.sign_time').' '.I('get._sort', 'desc').', main.creatime desc, sub.unit_pinyin_code, sub.pinyin_code',
					'status'  => 1,
					'mid'     => $this->meetingID
				], $options));
				/* 统计数据 */
				$signed_count   = $model->findRecord(0, [
					'sign_status' => 1,
					'status'      => 1,
					'mid'         => $this->meetingID
				]);
				$reviewed_count = $model->findRecord(0, [
					'review_status' => 1,
					'status'        => 1,
					'mid'           => $this->meetingID
				]);
				$enabled_count  = $model->findRecord(0, [
					'status' => 1,
					'mid'    => $this->meetingID
				]);
				$disabled_count = $model->findRecord(0, [
					'status' => 0,
					'mid'    => $this->meetingID
				]);
				$new_count      = $model->findRecord(0, [
					'isNew'  => 1,
					'status' => 1,
					'mid'    => $this->meetingID
				]);
				$client_count   = $model->findRecord(0, [
					'type'   => 'not employee',
					'status' => 1,
					'mid'    => $this->meetingID
				]);
				$employee_count = $model->findRecord(0, [
					'type'   => '内部员工',
					'status' => 1,
					'mid'    => $this->meetingID
				]);
				$all_count      = $model->findRecord(0, [
					'status' => 'not deleted',
					'mid'    => $this->meetingID
				]);
				/* 获取记录总数 */
				$temp_total_list = $model->findRecord(2, [
					//'keyword' => I('get.keyword', ''),
					'status' => 1,
					'mid'    => $this->meetingID
				]);
				$client_list     = $client_logic->setColumn('manage:client_list', $client_list, ['mid' => $this->meetingID]);
				/* 员工列表(for select component) */
				$employee_list = $employee_model->getEmployeeNameSelectList();
				/* 获取签到点列表(for select component) */
				$sign_place_list = $sign_place_model->getRecordSelectList($this->meetingID);
				$group_list      = $group_model->findRecord(2, ['mid' => $this->meetingID, 'status' => 1]);
				/* 向视图输出数据 */
				$this->assign('sign_place_list', $sign_place_list);
				$this->assign('statistics', [
					'signed'        => $signed_count,
					'not_signed'    => count($temp_total_list)-$signed_count,
					'reviewed'      => $reviewed_count,
					'not_reviewed'  => count($temp_total_list)-$reviewed_count,
					'new'           => $new_count,
					'not_new'       => count($temp_total_list)-$new_count,
					'enabled'       => $enabled_count,
					'disabled'      => $disabled_count,
					'total'         => $all_count,
					'enabled_total' => count($temp_total_list),
					'client'        => $client_count,
					'employee'      => $employee_count
				]);
				$this->assign('employee_list', $employee_list);
				$this->assign('list', $client_list);
				$this->assign('page_show', $page_show);
				$getColumnStatus = function ($mid){
					/** @var \Core\Model\ColumnControlModel $column_control_model */
					$column_control_model = D('Core/ColumnControl');
					$column_list          = $column_control_model->findRecord(2, [
						'mid'   => $mid,
						'table' => ['user_client', 'workflow_join']
					]);
					$data                 = [];
					foreach($column_list as $val) $data[$val['code']] = $val;

					return $data;
				};
				$this->assign('group_list', $group_list);
				$this->assign('column_status', $getColumnStatus($this->meetingID));
				$this->display();
			}
			else $this->error('您没有查看参会人员的权限');
		}

		public function create(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$type   = I('post.requestType', '');
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					if(isset($result['__return__'])) $redirect_url = $result['__return__'];
					else $redirect_url = '';
					unset($result['__ajax__']);
					unset($result['__return__']);
					if($result['status']) $this->success($result['message'], $redirect_url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['CLIENT.CREATE']){
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model     = D('Employee');
				$employee_name_list = $employee_model->getEmployeeNameSelectList();
				$employee_list      = $employee_model->getEmployeeSelectList();
				$this->assign('employee_name_list', $employee_name_list);
				$this->assign('employee_list', $employee_list);
				$getColumnStatus = function ($mid){
					/** @var \Core\Model\ColumnControlModel $column_control_model */
					$column_control_model = D('Core/ColumnControl');
					$column_list          = $column_control_model->findRecord(2, [
						'mid'   => $mid,
						'table' => ['user_client', 'workflow_join']
					]);
					$data                 = [];
					foreach($column_list as $val) $data[$val['code']] = $val;

					return $data;
				};
				$this->assign('column_status', $getColumnStatus($this->meetingID));
				$this->display();
			}
			else $this->error('您没有创建参会人员的权限');
		}

		public function exportClientDataTemplate(){
			if($this->permissionList['CLIENT.DOWNLOAD-IMPORT-EXCEL-TEMPLATE']){
				/** @var \Manager\Model\ClientModel $client_model */
				//$client_model = D('Client');
				//$header       = $client_model->getColumn(true);
				/** @var \Core\Model\ColumnControlModel $column_control_model */
				$column_control_model = D('Core/ColumnControl');
				$column_list          = $column_control_model->findRecord(2, [
					'mid'   => I('get.mid', 0, 'int'),
					'view'  => 1,
					'table' => ['user_client', 'workflow_join']
				]);
				$header[0]            = [];
				foreach($column_list as $col){
					$header[0][] = $col['name'];
				}
				$excel_logic = new ExcelLogic();
				$excel_logic->exportCustomData($header, [
					'fileName'    => '导入客户数据模板',
					'title'       => '导入客户数据模板',
					'subject'     => '导入客户数据模板',
					'description' => '吉美会议系统导入客户数据模板',
					'company'     => '吉美集团',
					'hasHead'     => true
				]);
			}
			else $this->error('您没有下载导入模板的权限');
		}

		public function exportClientData(){
			if($this->permissionList['CLIENT.EXPORT-EXCEL']){
				/** @var \Core\Model\JoinModel $main_model */
				$main_model = D('Core/Join');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				$excel_logic   = new ExcelLogic();
				$logic         = new ClientLogic();
				$options       = [];
				$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
				/* 处理URL参数 */
				if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 'not signed';
				if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 'not reviewed';
				if(isset($_GET['mid'])) $options['mid'] = $this->meetingID;
				$sql         = "SELECT
	name,
	case gender when 1 then '男' when 2 then '女' else '未指定' end gender,
	mobile,
	unit,
	birthday,
	email,
	title,
	position,
	address,
	id_card_number,
	develop_consultant,
	service_consultant,
	case is_new when 1 then '新客' when 0 then '老客' else '' end is_new,
	team,
	type,
	case workflow_join.status when 1 then '可用' when 0 then '禁用' when 2 then '删除' else '' end status,
	registration_type,
	case review_status when 1 then '已审核' when 0 then '未审核' else '取消审核' end review_status,
	FROM_UNIXTIME(review_time) review_time,
	case sign_status when 1 then '已签到' when 0 then '未签到' else '取消签到' end sign_status,
	FROM_UNIXTIME(sign_time) sign_time,
	case sign_type when 1 then 'PC签到' when 2 then '微信自主签到' when 3 then '微信签到' else '' end sign_type,
	(select name from user_employee where id = sign_director_id) sign_director,
	case gift_status when 1 then '已领取奖品' when 0 then '未领取奖品' end gift_status,
	(select code from workflow_group join workflow_group_member on gid = workflow_group.id
where workflow_group.mid = $options[mid] and cid = user_client.id and workflow_group_member.status = 1 order by workflow_group_member.id desc
	limit 1) group_name,
	column1,
	column2,
	column3,
	column4,
	column5,
	column6,
	column7,
	column8
FROM user_client
JOIN workflow_join ON workflow_join.cid = user_client.id
where mid = $options[mid] and workflow_join.status =1 and user_client.type <> '会所'";
				$client_list = M()->query($sql);
				$client_list = array_merge([
					[
						'姓名',
						'性别',
						'手机号',
						'单位',
						'生日',
						'邮箱',
						'职称',
						'职位',
						'地址',
						'身份证号',
						'开拓顾问',
						'服务顾问',
						'是否新客',
						'团队',
						'类型',
						'状态',
						'审核状态',
						'审核时间',
						'签到状态',
						'签到时间',
						'签到类型',
						'签到者',
						'领取奖品状态',
						'分组',
						'备用字段1',
						'备用字段2',
						'备用字段3',
						'备用字段4',
						'备用字段5',
						'备用字段6',
						'备用字段7',
						'备用字段8'
					]
				], $client_list);
				//				$client_list = $main_model->findRecord(2, array_merge([
				//					'keyword' => I('get.keyword', ''),
				//					'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
				//					'status'  => 'not deleted'
				//				], $options));
				//				$client_list = $logic->setColumn('excel:set_data', $client_list, [
				//					'exceptColumn' => [
				//						'password',
				//						'mobile_qrcode',
				//						'mid',
				//						'id',
				//						'pinyin_code',
				//						'status',
				//						'creator',
				//						'sign_qrcode',
				//						'cid',
				//						'creatime',
				//						'join_status'
				//					]
				//				]);
				$excel_logic->exportCustomData($client_list, [
					'fileName'    => "[$meeting[name]]参会人员客户列表",
					'title'       => "$meeting[name]",
					'subject'     => '参会人员客户列表',
					'description' => '吉美会议系统导出客户数据',
					'company'     => '吉美集团',
					'hasHead'     => true
				]);
			}
			else $this->error('您没有下载导入模板的权限');
		}

		public function alter(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$result = $logic->handlerRequest('alter');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], $result['redirectUrl']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['CLIENT.ALTER']){
				/** @var \Core\Model\JoinModel $join_model */
				$join_model  = D('Core/Join');
				$logic       = new ClientLogic();
				$join_record = $join_model->findRecordAll(1, [
					'mid' => $this->meetingID,
					'cid' => I('get.id', 0, 'int')
				]);
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model     = D('Employee');
				$join_record        = $logic->setColumn('alter:client_info', $join_record, [
					'mid' => $this->meetingID,
					'cid' => I('get.id', 0, 'int')
				]);
				$employee_name_list = $employee_model->getEmployeeNameSelectList();
				$employee_list      = $employee_model->getEmployeeSelectList();
				$this->assign('employee_name_list', $employee_name_list);
				$this->assign('employee_list', $employee_list);
				$this->assign('info', $join_record);
				$getColumnStatus = function ($mid){
					/** @var \Core\Model\ColumnControlModel $column_control_model */
					$column_control_model = D('Core/ColumnControl');
					$column_list          = $column_control_model->findRecord(2, [
						'mid'   => $mid,
						'table' => ['user_client', 'workflow_join']
					]);
					$data                 = [];
					foreach($column_list as $val) $data[$val['code']] = $val;

					return $data;
				};
				$this->assign('column_status', $getColumnStatus($this->meetingID));
				$this->display();
			}
			else $this->error('您没有修改参会人员的权限');
		}

		public function sign(){
			if($this->permissionList['CLIENT.SIGN']){
				if(isset($_GET['keyword'])){
					/** @var \Core\Model\JoinModel $join_model */
					$join_model        = D('Core/Join');
					$core_client_logic = new \Core\Logic\ClientLogic();
					$join_client       = $join_model->findRecord(1, [
						'keyword' => I('get.keyword', ''),
						'status'  => 1,
						'mid'     => $this->meetingID
					]);
					if($join_client['sign_status'] != 1 && $join_client){
						$result = $core_client_logic->sign([
							'mid'  => $this->meetingID,
							'cid'  => $join_client['cid'],
							'eid'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
							'type' => 1
						]);
						if($result['status']){
							$join_client['sign_status']      = $result['data']['sign_status'];
							$join_client['sign_time']        = $result['data']['sign_time'];
							$join_client['sign_director_id'] = $result['data']['sign_director_id'];
							$join_client['sign_type']        = $result['data']['sign_type'];
						}
					}
					$info = 0;
					if($join_client){
						/** @var \Core\Model\ReceivablesModel $receivables_model */
						$receivables_model = D('Core/Receivables');
						/** @var \Core\Model\ReceivablesOptionModel $receivables_option_model */
						$receivables_option_model = D('Core/ReceivablesOption');
						$badge_logic              = new BadgeLogic();
						$badge                    = $badge_logic->getBadge($this->meetingID, $join_client['cid']);
						if($join_client['review_status']) $info = 2;
						else $info = 1;
						$receivables = $receivables_model->findRecord(2, [
							'mid'    => $this->meetingID,
							'cid'    => $join_client['cid'],
							'status' => 1
						]);
						$price       = 0;
						foreach($receivables as $val){
							$option = $receivables_option_model->findRecord(2, ['rid' => $val['id'], 'status' => 1]);
							foreach($option as $val2) $price += $val2['price'];
						}
						$join_client['price'] = $price;
						$this->assign('client', $join_client);
						$this->assign('badge', $badge);
					}
					$this->assign('info_type', $info);
				}
				$this->display();
			}
			else $this->error('您没有签到的权限');
		}

		public function printBadge(){
			/** @var \Core\Model\BadgeModel $model */
			$model = D('Core/Badge');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$logic         = new BadgeLogic();
			$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID, 'status' => 'not deleted']);
			$info          = $model->findBadge(1, ['id' => $meeting['bid'], 'status' => 'not deleted']);
			if(isset($_GET['cid'])){
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				/** @var \Core\Model\GroupMemberModel $group_member_model */
				$group_member_model = D('Core/GroupMember');
				/** @var \Core\Model\GroupModel $group_model */
				$group_model = D('Core/Group');
				$cid_arr     = explode('*', I('get.cid', ''));
				$list        = [];
				foreach($cid_arr as $cid){
					$client        = $join_model->findRecord(1, [
						'mid'    => $this->meetingID,
						'cid'    => $cid,
						'status' => 'not deleted'
					]);
					$leader        = $group_model->findRecord(1, [
						'leader'      => $client['cid'],
						'mid'         => $this->meetingID,
						'leader_type' => 1
					]);
					$deputy_leader = $group_model->findRecord(1, [
						'deputy_leader'      => $client['cid'],
						'mid'                => $this->meetingID,
						'deputy_leader_type' => 1
					]);
					$group         = $group_member_model->findRecord(1, [
						'mid' => $this->meetingID,
						'cid' => $cid
					]);
					if($deputy_leader['leader'] == $client['cid'] && $deputy_leader['leader_type'] == 1){
						$group['code'] = $deputy_leader['code']."副组长";
					}
					if($leader['leader_type'] == 1 && $client['cid'] == $leader['leader']){
						$group['code'] = $leader['code']."组长";
					}
					$list[] = $logic->setData('preview:init_temp', $info, [
						'client'  => $client,
						'meeting' => $meeting,
						'group'   => $group
					]);
				}
				$this->assign('list', $list);
			}
			$this->display();
		}

		public function update1(){
			set_time_limit(0);
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			$str_obj      = new StringPlus();
			$client_list  = $client_model->findClientAll(2);
			C('TOKEN_ON', false);
			foreach($client_list as $client){
				$result = $client_model->alterClient(['id' => $client['id']], ['pinyin_code'=>$str_obj->getPinyin($client['name'], true, ''), 'unit_pinyin_code' => $str_obj->getPinyin($client['unit'], true, '')]);
				if($result['status']) echo "$client[id] - success<br>";
				else echo "$client[id] - failure<br>";
			}
		}
	}