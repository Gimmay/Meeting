<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 11:41
	 */
	namespace Manager\Logic;

	class SignPlaceLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function create($data){
			/** @var \Core\Model\SignPlaceModel $model */
			$model            = D('Core/SignPlace');
			$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['mid']      = I('get.mid', 0, 'int');
			$data['place']    = I('post.address_province', '').'-'.I('post.address_city', '').'-'.I('post.address_area', '').'-'.I('post.address_detail', '');
			$data['creatime'] = time();

			return $model->createRecord($data);
		}
	}