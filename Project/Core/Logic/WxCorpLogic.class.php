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
			'appID'      => 24
		];

		public function _initialize(){
			parent::_initialize();
		}

		public function getAllUserList(){
			$wxcorp_object = new WXCorpUniversalApi($this->_config['corpID'], $this->_config['corpSecret']);
			$result        = $wxcorp_object->getUserList(1);

			return $result;
		}

		public function sendMessage($type, $data, $receiver){
			$wxcorp_object = new WXCorpUniversalApi($this->_config['corpID'], $this->_config['corpSecret']);
			$result        = $wxcorp_object->sendMessage($type, $data, $this->_config['appID'], $receiver);

			return $result;
		}

	}