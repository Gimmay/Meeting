<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-16
	 * Time: 10:05
	 */
	namespace Core\Logic;

	use Quasar\StringPlus;

	class ReceivablesLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function makeOrderNumber(){
			$str_obj    = new StringPlus();
			$date       = date('YmdHis');
			$random_str = $str_obj->makeRandomString(8, 'NW+');

			return "$date$random_str";
		}
	}