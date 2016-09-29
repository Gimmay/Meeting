<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 16:09
	 */
	namespace Manager\Controller;

	class MessageController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$this->display();
		}
	}