<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 10:13
	 */
	namespace Manager\Controller;

	use Manager\Logic\SignPlaceLogic;

	class SignPlaceController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function create(){
			if(IS_POST){
				$logic  = new SignPlaceLogic();
				$result = $logic->create(I('post.'));
				if($result['status']) $this->success('添加签到点成功');
				else $this->error($result['message']);
				exit;
			}
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$meeting_list   = $meeting_model->getMeetingForSelect();
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('meeting_list', $meeting_list);
			$this->assign('employee_list', $employee_list);
			$this->display();
		}

		public function manage(){
			$this->display();
		}
	}