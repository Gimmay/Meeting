<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 16:45
	 */
	namespace Manager\Controller;

	use Manager\Logic\EmployeeLogic;
	use Manager\Logic\ReceivablesLogic;

	class ReceivablesController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
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
			$client_list  = $client_model->getClientSelectList();
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			/** @var \Core\Model\EmployeeModel $employee_model_one */
			$employee_model_one = D('Core/Employee');
			$employee_result    = $employee_model_one->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
			$time               = time();
			$this->assign('time', $time);
			$this->assign('employee_info', $employee_result);
			$this->assign('meeting_list', $meeting_list);
			$this->assign('info', $receivables_result);
			$this->assign('meeting_info', $meeting_result);
			$this->assign('employee_list', $employee_list);
			$this->assign('coupon_item', $coupon_item_result);
			$this->display();
		}
	}