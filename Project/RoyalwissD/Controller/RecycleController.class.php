<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-6
	 * Time: 10:13
	 */
	namespace RoyalwissD\Controller;

	use RoyalwissD\Logic\RecycleLogic;
	use Think\Page;

	class RecycleController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
		}

		public function client(){

			$this->display();
		}

		public function employee(){

			$this->display();
		}

		public function meeting(){

			$this->display();
		}

		public function role(){

			$this->display();
		}

		public function coupon(){

			$this->display();
		}

		public function couponItem(){

			$this->display();
		}

		public function message(){

			$this->display();
		}

		public function group(){

			$this->display();
		}

		public function groupMember(){

			$this->display();
		}

		public function hotel(){

			$this->display();
		}

		public function room(){

			$this->display();
		}
	}