<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 15:42
	 */
	namespace Core\Logic;

	use Quasar\WXCorpUniversalApi;

	class WxCorpLogic extends CoreLogic{
		private $_config = [
			'corpID'     => 'wxbd62380696bb0201',
			'corpSecret' => 'nS7-z8pbUzUwxwnImCoNCslbKSj6kRgDqYm8vn2oRD2kCE9aKpNNUAJVkPc9wlQb',
			'appID'      => [
				'client'   => 27,
				'employee' => 33
			]
		];

		public function _initialize(){
			parent::_initialize();
		}

		public function getAllUserList(){
			$wxcorp_object = new WXCorpUniversalApi($this->_config['corpID'], $this->_config['corpSecret']);
			$result        = $wxcorp_object->getUserList(1);

			return $result;
		}

		public function sendMessage($type, $data, $receiver, $app_type){
			if($app_type == 'client') $app_id = $this->_config['appID']['client'];
			elseif($app_type == 'employee') $app_id = $this->_config['appID']['employee'];
			else return ['status' => false, 'message' => '错误的应用类型'];
			$wxcorp_object = new WXCorpUniversalApi($this->_config['corpID'], $this->_config['corpSecret']);
			$result        = $wxcorp_object->sendMessage($type, $data, $app_id, $receiver);

			return $result;
		}

		public function getUserID(){
			$wxcorp_object = new WXCorpUniversalApi($this->_config['corpID'], $this->_config['corpSecret']);

			return $wxcorp_object->getID();
		}

	}