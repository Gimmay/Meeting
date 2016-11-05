<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-28
	 * Time: 14:12
	 */
	namespace Core\Logic;

	class CarLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getRemainSeat($car_id){
			/** @var \Core\Model\CarModel $car_model */
			$car_model = D('Core/Car');
			/** @var \Core\Model\AssignCarModel $assign_car_model */
			$assign_car_model = D('Core/AssignCar');
			$car_record = $car_model->findCar(1, ['id' => $car_id, 'status' => 'not deleted']);
			if($car_record && $car_record['mid']){
				return $car_record['capacity']-$assign_car_model->findRecord(0, ['carID' => $car_id]);
			}
			else return 0;
		}

		public function initializeStatus(){
			/** @var \Core\Model\CarModel $model */
			$model = D('Core/Car');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\AssignCarModel $assign_car_model */
			$assign_car_model = D('Core/AssignCar');
			$list = $model->findCar(2, ['status' => 'available']);
			foreach($list as $val){
				if(!empty($val)){
					$meeting_record = $meeting_model->findMeeting(1, ['id' => $val['mid'], 'not deleted']);
					$carry_record = $assign_car_model->findRecord(2, ['carID' => $val['id']]);
					if($meeting_record){
						$meeting_start_time = strtotime($meeting_record['start_time']);
						$meeting_end_time = strtotime($meeting_record['end_time']);
						$now_time = time();
						if($meeting_start_time<$now_time && $meeting_end_time>$now_time){
							C('TOKEN_ON', false);
							$model->alterCar(['id' => $val['id'], ['status' => 2]]);
						}
					}
				}
			}
		}
	}