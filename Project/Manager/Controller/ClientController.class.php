<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Manager\Controller;

	use Manager\Logic\ClientLogic;
	use Manager\Logic\ExcelLogic;
	use stdClass;
	use Think\Page;

	class ClientController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
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
				$model   = D('Core/Join');
				$options = [];
				/* 处理URL参数 */
				if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 'not signed';
				if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 'not reviewed';
				if(isset($_GET['client_type'])) $options['isNew'] = I('get.client_type', 0, 'int') == 1 ? 1 : 0;
				if(isset($_GET['status'])) $options['status'] = I('get.status', 0, 'int') == 1 ? 1 : 0;
				/* 获取记录总数 */
				$total_list = $model->findRecord(0, array_merge([
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted',
					'mid'     => $this->meetingID
				], $options));
				/* 分页设置 */
				$page_object = new Page($total_list, I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				/* 当前页记录 */
				$client_list = $model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'main.sign_time').' '.I('get._sort', 'desc').', sub.pinyin_code',
					'status'  => 'not deleted',
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
				/* 员工列表(for select component) */
				$employee_list = $employee_model->getEmployeeSelectList();
				/* 获取签到点列表(for select component) */
				$sign_place_list = $sign_place_model->getRecordSelectList($this->meetingID);
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
					'enabled_total' => count($temp_total_list)
				]);
				$this->assign('employee_list', $employee_list);
				$this->assign('list', $client_list);
				$this->assign('page_show', $page_show);
				$this->display();
			}
			else $this->error('您没有查看参会人员的权限');
		}

		public function create(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$result = $logic->handlerRequest('create');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage', ['mid' => $this->meetingID]));
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
				$this->display();
			}
			else $this->error('您没有创建参会人员的权限');
		}

		public function exportClientDataTemplate(){
			if($this->permissionList['CLIENT.DOWNLOAD-IMPORT-EXCEL-TEMPLATE']){
				/** @var \Manager\Model\ClientModel $client_model */
				$client_model = D('Client');
				$header       = $client_model->getColumn(true);
				$excel_logic  = new ExcelLogic();
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
				$client_list = $main_model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
					'status'  => 'not deleted'
				], $options));
				$client_list = $logic->alterColumnForExportExcel($client_list, [
					'password',
					'mobile_qrcode',
					'mid',
					'id',
					'pinyin_code',
					'status',
					'creator',
					'sign_qrcode',
					'cid',
					'creatime',
					'join_status',
					'column2',
					'column3',
					'column4',
					'column5',
					'column6',
					'column7',
					'column8'
				]);
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
					if($result['status']) $this->success($result['message'], isset($_POST['redirectUrl']) ? I('post.redirectUrl', '') : U('manage', ['mid' => $this->meetingID]));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['CLIENT.ALTER']){
				/** @var \Core\Model\ClientModel $model */
				$model = D('Core/Client');
				/** @var \Core\Model\JoinModel $join_model */
				$join_model  = D('Core/Join');
				$logic       = new ClientLogic();
				$info        = $model->findClient(1, ['id' => I('get.id', 0, 'int')]);
				$join_record = $join_model->findRecord(1, ['mid' => $this->meetingID, 'cid' => $info['id']]);
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model     = D('Employee');
				$info               = $logic->setExtendColumnForAlter($info);
				$employee_name_list = $employee_model->getEmployeeNameSelectList();
				$employee_list      = $employee_model->getEmployeeSelectList();
				$this->assign('employee_name_list', $employee_name_list);
				$this->assign('employee_list', $employee_list);
				$this->assign('info', $info);
				$this->assign('join_record', $join_record);
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
						'mid'     => I('get.mid', 0, 'int')
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
						if($join_client['review_status']) $info = 2;
						else $info = 1;
						$this->assign('client', $join_client);
					}
					$this->assign('info_type', $info);
				}
				$this->display();
			}
			else $this->error('您没有签到的权限');
		}
	}