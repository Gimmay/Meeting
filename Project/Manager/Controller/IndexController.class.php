<?php
	namespace Manager\Controller;

	use Think\Controller;

	class IndexController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function index(){
			$this->display();
		}
	}