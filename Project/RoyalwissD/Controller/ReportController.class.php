<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:05
	 */
	namespace RoyalwissD\Controller;

	class ReportController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$this->display();
		}
	}