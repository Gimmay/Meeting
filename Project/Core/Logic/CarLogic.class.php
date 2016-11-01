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
			$car_record       = $car_model->findCar(1, ['id' => $car_id, 'status' => 'not deleted']);
			if($car_record && $car_record['mid']){
				return $car_record['capacity'] - $assign_car_model->findRecord(0, ['carID' => $car_id]);
			}else return 0;
		}
	}