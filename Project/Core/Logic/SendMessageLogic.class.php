<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-10
	 * Time: 18:00
	 */
	namespace Core\Logic;

	class SendMessageLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function create($context, $receiver, $receiver_type, $status){
			/** @var \Core\Model\SendMessageModel $model */
			$model = D('Core/SendMessage');
			C('TOKEN_ON', false);
			return $model->createRecord([
				'creator'       => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
				'creatime'      => time(),
				'status'        => $status,
				'context'       => $context,
				'receiver'      => $receiver,
				'receiver_type' => $receiver_type
			]);
		}
	}