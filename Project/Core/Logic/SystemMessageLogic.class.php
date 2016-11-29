<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-22
	 * Time: 15:05
	 */
	namespace Core\Logic;

	class SystemMessageLogic extends CoreLogic{
		private $_messageTemp = [
			'alterPasswordWhenEmptyPassword' => '您的密码为空存在风险，请立刻修改密码！ <a href=\'::URL::\'>点击修改</a>'
		];

		public function _initialize(){
			parent::_initialize();
		}

		public function initMessage(){
			/** @var \Core\Model\SystemMessageModel $model */
			$model    = D('Core/SystemMessage');
			$list     = $model->findMessage(2, ['receiver' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
			$new_list = ['read' => [], 'notRead' => []];
			foreach($list as $key => $val){
				$val['message'] = $this->_getMessage($val['type']);
				if($val['status'] == 0) $new_list['notRead'][] = $val;
				if($val['status'] == 1) $new_list['read'][] = $val;
			}

			return $new_list;
		}

		private function _getMessage($index){
			switch($index){
				case 'alterPasswordWhenEmptyPassword':
					$message = $this->_messageTemp['alterPasswordWhenEmptyPassword'];
					$message = str_replace('::URL::', U('My/password'), $message);
				break;
				default:
					$message = '';
				break;
			}

			return $message;
		}

		public function sendMessage($receiver_id, $type, $sender = 1){
			/** @var \Core\Model\SystemMessageModel $model */
			$model = D('Core/SystemMessage');

			return $model->createMessage([
				'receiver' => $receiver_id,
				'type'     => $type,
				'sender'   => $sender,
				'creatime' => time()
			]);
		}
	}