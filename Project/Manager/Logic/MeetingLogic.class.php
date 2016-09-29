<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	class MeetingLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getSelectListForRole(){
			/** @var \Manager\Model\MeetingModel $model */
			$model  = D('Meeting');
			$result = $model->getMeetingForSelect();
			array_unshift($result, ['value' => 0, 'html' => '(系统全局)']);

			return $result;
		}

	}