<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 18:02
	 */
	namespace Manager\Logic;

	class CarLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch(strtolower($type)){
				case 'create':
					/** @var \Core\Model\CarModel $model */
					$model            = D('Core/Car');
					$data             = I('post.');
					$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['creatime'] = time();
					$result           = $model->createCar($data);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'alter':
					
				break;
				case 'delete':

				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data, $option = []){
			switch($type){
				case '':
					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}