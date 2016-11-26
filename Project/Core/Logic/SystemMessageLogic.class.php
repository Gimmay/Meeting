<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-22
	 * Time: 15:05
	 */
	namespace Core\Logic;

	class SystemMessageLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		private $_messageTemp = [
			'alterPasswordWhenEmptyPassword' => '您的密码为空存在风险，请立刻修改密码！ <a href=\'::URL::\'>点击修改</a>'
		];

		public function initMessage(){
			$message = [];
			if(I('session.MANAGER_EMPLOYEE_EMPTY_PASSWORD', 0, 'int') === 1) $message[] = $this->_getMessage('alterPasswordWhenEmptyPassword');

			return $message;
		}

		private function _getMessage($index){
			switch($index){
				case 'alterPasswordWhenEmptyPassword':
					$message = $this->_messageTemp['alterPasswordWhenEmptyPassword'];
					$message = str_replace('::URL::', U('My/alterPassword'), $message);
				break;
				default:
					$message = '';
				break;
			}

			return $message;
		}
	}