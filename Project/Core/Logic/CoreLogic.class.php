<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:17
	 */
	namespace Core\Logic;

	class CoreLogic{
		public function __construct(){
			$this->_initialize();
		}

		public function _initialize(){
			$this->_buildConstVariables();
		}

		public function _buildConstVariables(){
			define('UPLOAD_PATH', APP_PATH.'Uploads');
		}

	}