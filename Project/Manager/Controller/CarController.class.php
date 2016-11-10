<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 18:01
	 */
	namespace Manager\Controller;

	use Manager\Logic\CarLogic;

	class CarController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function manage(){
			$logic = new CarLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $logic->handlerRequest($type);
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
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			
			/** @var \Core\Model\CarModel $car_model */
			$car_model = D('Core/Car');
			/** @var \Manager\Model\CarModel $model */
			$model         = D('Car');
			$type          = I('get.type', '');
			$list          = $car_model->findCar(2, ['status' => 'not deleted']);
			$list          = $logic->setData('manage:set_and_filter', $list, ['type' => $type]);
			$meeting_list  = $meeting_model->getMeetingForSelect();
			$employee_list = $employee_model->getEmployeeNameSelectList();
			$type_list     = $model->getTypeSelectList();
			$this->assign('list', $list);
			$this->assign('meeting', $meeting_list);
			$this->assign('employee', $employee_list);
			$this->assign('type', $type_list);
			$this->display();
		}
	}