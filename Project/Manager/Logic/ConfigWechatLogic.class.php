<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-30
	 * Time: 18:03
	 */
	namespace Manager\Logic;

	class ConfigWechatLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'create':
				break;
				case 'delete':
				break;
			}
		}
	}