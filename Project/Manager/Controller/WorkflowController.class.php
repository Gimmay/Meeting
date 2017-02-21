<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-1-4
	 * Time: 14:10
	 */
	namespace Manager\Controller;

	use Manager\Logic\ClientLogic;
	use Manager\Logic\ReceivablesLogic;
	use Manager\Model\ClientModel;
	use Manager\Model\EmployeeModel;

	class WorkflowController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function createClient(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$type   = I('post.requestType', '');
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					//					if(isset($result['__return__'])) $redirect_url = $result['__return__'];
					//					else $redirect_url = '';
					unset($result['__ajax__']);
					//					unset($result['__return__']);
					if($result['status']) $this->redirect('receivables', [
						'mid' => $this->meetingID,
						'cid' => $result['cid']
					]);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model     = D('Employee');
			$employee_name_list = $employee_model->getEmployeeNameSelectList();
			$employee_list      = $employee_model->getEmployeeSelectList();
			$getColumnStatus    = function ($mid){
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
			$this->assign('employee_name_list', $employee_name_list);
			$this->assign('employee_list', $employee_list);
			$this->assign('column_status', $getColumnStatus($this->meetingID));
			$this->display();
		}

		public function receivables(){
			$next_url          = U('Client/manage', ['mid' => $this->meetingID, 'cid' => I('get.cid', 0, 'int')]);
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
					//					if(isset($result['__return__'])) $url = $result['__return__'];
					//					else $url = '';
					if($result['status']) $this->success($result['message'], $next_url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$getMeetingType = function ($meeting){
				$cur_time   = time();
				$start_time = strtotime($meeting['start_time']);
				$end_time   = strtotime($meeting['end_time']);
				if($cur_time<$start_time) return 0;
				elseif($cur_time>$end_time) return 2;
				else return 1;
			};
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model = D('Core/PayMethod');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model     = D('Core/Meeting');
			$receivables_logic = new \Core\Logic\ReceivablesLogic();
			$order_number      = $receivables_logic->makeOrderNumber();
			$meeting_result    = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
			$pay_method_result = $pay_method_model->findRecord(2, ['status' => '1']);
			/** @var \Core\Model\PosMachineModel $pos_machine_model */
			$pos_machine_model  = D('Core/PosMachine');
			$pos_machine_result = $pos_machine_model->findRecord(2, ['status' => '1', 'mid' => $this->meetingID]);
			$client_logic       = new ClientModel();
			$client             = $client_logic->getClientSelectList($this->meetingID, true);
			$employee_logic     = new EmployeeModel();
			$employee           = $employee_logic->getEmployeeSelectList();
			$client_result      = $client_model->findClient(1, ['id' => I('get.cid', 0, 'int')]);
			$employee_result    = $employee_model->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
			$this->assign('order_number', $order_number);
			$this->assign('meeting', $meeting_result);
			$this->assign('client', $client); //遍历当前会议的所有的参会人员
			$this->assign('employee', $employee); //遍历当前会议的所有的工作人员
			$this->assign('pos', $pos_machine_result); //遍历pos机
			$this->assign('pay', $pay_method_result); //遍例支付方式
			$this->assign('client_single', $client_result);
			$this->assign('employee_single', $employee_result);
			$this->assign('meeting_status', $getMeetingType($meeting_result));
			$this->assign('next_url', $next_url);
			$this->display();
		}

		public function chooseRoom(){
			if(IS_POST){
				exit;
			}
			$this->display();
		}

		public function chooseGroup(){
			if(IS_POST){
				exit;
			}
			$this->display();
		}
	}