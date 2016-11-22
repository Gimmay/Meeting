<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 16:45
	 */
	namespace Manager\Controller;

	use Manager\Logic\EmployeeLogic;
	use Manager\Logic\ExcelLogic;
	use Manager\Logic\ReceivablesLogic;

	class ReceivablesController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function create(){
		}

		public function manage(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$meeting_result     = $receivables_logic->findMeetingClient();
			$coupon_item_result = $receivables_logic->findCouponItem();
			$receivables_result = $receivables_logic->findReceivables();
			/** @var \Manager\Model\ClientModel $client_model */
			$client_model = D('Client');
			$mid          = I('get.mid', 0, 'int');
			$client_list  = $client_model->getClientSelectList($mid);
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model  = D('Core/PayMethod');
			$pay_method_result = $pay_method_model->findRecord(2, ['status' => 'not deleted']);
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			/** @var \Core\Model\EmployeeModel $employee_model_one */
			$employee_model_one = D('Core/Employee');
			$employee_result    = $employee_model_one->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
			/** @var \Manager\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model  = D('ReceivablesType');
			$receivables_type_result = $receivables_type_model->getReceivablesTypeSelectList();
			/** @var \Core\Model\PosMachineModel $pos_machine_model */
			$pos_machine_model  = D('Core/PosMachine');
			$pos_machine_result = $pos_machine_model->findRecord(2, ['status' => 'not deleted']);
			$time               = time();
			$this->assign('type', $receivables_type_result);
			$this->assign('client_type', $client_list);
			$this->assign('pay', $pay_method_result);
			$this->assign('pos', $pos_machine_result);
			$this->assign('time', $time);
			$this->assign('employee_info', $employee_result);
			$this->assign('meeting_list', $meeting_list);
			$this->assign('info', $receivables_result);
			$this->assign('meeting_info', $meeting_result);
			$this->assign('employee_list', $employee_list);
			$this->assign('coupon_item', $coupon_item_result);
			$this->display();
		}

		public function payMethod(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model  = D('Core/PayMethod');
			$pay_method_result = $pay_method_model->findRecord(2, [
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', '')
			]);
			$this->assign('pay', $pay_method_result);
			$this->display();
		}

		public function receivablesType(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model  = D('Core/ReceivablesType');
			$receivables_type_result = $receivables_type_model->findRecord(2, [
				'status'  => 'not deleted',
				'keyword' => I('get.keyword')
			]);
			$this->assign('type_info', $receivables_type_result);
			$this->display();
		}

		public function posMachine(){
			$pos_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $pos_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\PosMachineModel $pos_machine_model */
			$pos_machine_model  = D('Core/PosMachine');
			$pos_machine_result = $pos_machine_model->findRecord(2, [
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', '')
			]);
			$this->assign('pos', $pos_machine_result);
			$this->display();
		}

		public function exportReceivablesData(){
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Core/Receivables');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model      = D('Core/Meeting');
			$excel_logic        = new ExcelLogic();
			$logic              = new ReceivablesLogic();
			$receivables_result = $receivables_model->findRecord(2, [
				'mid'    => $this->meetingID,
				'status' => 'not deleted'
			]);
			$receivables_result = $logic->setData('excel_data', $receivables_result);
			$meeting            = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
			$excel_logic->exportCustomData($receivables_result, [
				'fileName'    => "[$meeting[name]]收款信息",
				'title'       => "$meeting[name]",
				'subject'     => '收款列表',
				'description' => '吉美会议系统导出收款数据',
				'company'     => '吉美集团',
				'hasHead'     => true
			]);
		}

		public function exportReceivablesDataTemplate(){
			if($this->permissionList['CLIENT.DOWNLOAD-IMPORT-EXCEL-TEMPLATE']){ // todo
				/** @var \Manager\Model\ReceivablesModel $receivables_model */
				$receivables_model = D('Receivables');
				$header            = $receivables_model->getColumn(true);
				$excel_logic       = new ExcelLogic();
				$excel_logic->exportCustomData($header, [
					'fileName'    => '导入收款数据模板',
					'title'       => '导入收款数据模板',
					'subject'     => '导入收款数据模板',
					'description' => '吉美会议系统导入收款数据模板',
					'company'     => '吉美集团',
					'hasHead'     => true
				]);
			}
			else $this->error('您没有下载导入模板的权限');
		}
	}