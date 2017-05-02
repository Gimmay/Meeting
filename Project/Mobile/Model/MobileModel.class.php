<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 17:46
	 */

	namespace Mobile\Model;

	use General\Model\GeneralModel;

	class MobileModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $autoCheckFields = false;
	}