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
			/** @var \Core\Model\CarModel $car_model */
			$car_model = D('Core/Car');
			/** @var \Core\Model\AssignCarModel $assign_car_model */
			$assign_car_model = D('Core/AssignCar');
			$list      = $assign_car_model->findRecord(2, ['status' => 'not deleted']);
			$list = $logic->setData('manage:set_extend_column', $list);

		}

		public function create(){
			$logic = new CarLogic();
			if(IS_POST){
				$result = $logic->handlerRequest('create');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage'));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$this->display();
		}

		public function alter(){
			$logic = new CarLogic();
			if(IS_POST){
				$result = $logic->handlerRequest('alter');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage'));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$this->display();
		}
	}