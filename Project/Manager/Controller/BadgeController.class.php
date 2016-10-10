<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 11:59
	 */
	namespace Manager\Controller;

	class BadgeController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$this->display();
		}
	}