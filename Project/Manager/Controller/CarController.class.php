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
			$init = function(){
				/** @var \Core\Model\CarModel $model */
				$model = D('Core/Car');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				/** @var \Core\Model\AssignCarModel $assign_car_model */
				$assign_car_model = D('Core/AssignCar');
				$core_logic = new CarLogic();
				$list = $model->findCar(2, ['status'=>'available']);
				print_r($list);exit;
				foreach($list as $val){
					if(!empty($val)){
						$meeting_record = $meeting_model->findMeeting(1, ['id'=>$val['mid'], 'not deleted']);
						$carry_record = $assign_car_model->findRecord(2, ['carID'=>$val['id']]);
						if($meeting_record){
							$meeting_start_time = strtotime($meeting_record['start_time']);
							$meeting_end_time   = strtotime($meeting_record['end_time']);
							$now_time           = time();
							if($meeting_start_time<$now_time && $meeting_end_time>$now_time){
								C('TOKEN_ON', false);
								$model->alterCar(['id'=>$val['id'], ['status'=>2]]);
							}

						}
					}
				}
			};
			$init();

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
			/** @var \Core\Model\AssignCarModel $assign_car_model */
			$assign_car_model = D('Core/AssignCar');
			/** @var \Manager\Model\CarModel $model */
			$model         = D('Car');
			$type          = I('get.type', '');
			$list          = $assign_car_model->findRecord(2, ['status' => 'not deleted']);
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