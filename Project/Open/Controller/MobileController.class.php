<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-29
	 * Time: 14:16
	 */
	namespace Open\Controller;

	class MobileController extends OpenController{
		public function _initialize(){
			parent::_initialize();
		}

		public function invite(){
			$this->display();
		}
	}