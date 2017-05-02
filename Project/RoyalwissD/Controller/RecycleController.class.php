<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-6
	 * Time: 10:13
	 */

	namespace RoyalwissD\Controller;

	class RecycleController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
		}

		public function client(){
			$this->display();
		}

		public function project(){
			$this->display();
		}

		public function projectType(){
			$this->display();
		}

		public function payMethod(){
			$this->display();
		}

		public function posMachine(){
			$this->display();
		}

		public function receivables(){
			$this->display();
		}

		public function hotel(){
			$this->display();
		}

		public function room(){
			$this->display();
		}

		public function message(){
			$this->display();
		}

		public function group(){
			$this->display();
		}
	}