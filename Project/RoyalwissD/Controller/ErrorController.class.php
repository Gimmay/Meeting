<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-29
	 * Time: 9:13
	 */
	namespace RoyalwissD\Controller;

	class ErrorController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
		}

		public function error(){
			$this->display();
		}
	}