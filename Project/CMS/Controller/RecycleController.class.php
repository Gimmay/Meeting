<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 17:26
	 */

	namespace CMS\Controller;

	class RecycleController extends CMS{
		public function _initialize(){
			parent::_initialize();
		}

		public function user(){
			$this->display();
		}

		public function role(){
			$this->display();
		}

		public function department(){
			$this->display();
		}

		public function apiConfigure(){
			$this->display();
		}
	}