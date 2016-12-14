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
		const RECEIVABLES_TYPE        = [
			['value' => 1, 'name' => '门票'],
			['value' => 2, 'name' => '代金券'],
			['value' => 3, 'name' => '产品'],
			['value' => 4, 'name' => '其他'],
			['value' => 5, 'name' => '定金'],
			['value' => 6, 'name' => '课程费'],
			['value' => 7, 'name' => '产品费'],
			['value' => 8, 'name' => '场餐费'],
		];
		const RECEIVABLES_SOURCE_TYPE = [
			['value' => 1, 'name' => '会前收款'],
			['value' => 2, 'name' => '会中收款'],
			['value' => 3, 'name' => '会后收款'],
		];

		public function _initialize(){
			parent::_initialize();
		}

		public function makeOrderNumber(){
//			$str_obj    = new StringPlus();
//			$date       = date('YmdHis');
//			$random_str = $str_obj->makeRandomString(8, 'NW+');
//
//			return "$date$random_str";
			$str_obj    = new StringPlus();
//			$date       = date('YmdHis');
			$random_str = $str_obj->makeRandomString(6, 'N');

			return "SJ$random_str";
		}

		public function getReceivablesType($value = null){
			if($value !== null){
				$value  = (int)$value;
				$result = '';
				foreach(self::RECEIVABLES_TYPE as $val){
					if($val['value'] == $value){
						$result = $val['name'];
						break;
					}
				}
			}
			else $result = self::RECEIVABLES_TYPE;

			return $result;
		}

		public function getReceivablesSourceType($value = null){
			if($value !== null){
				$value  = (int)$value;
				$result = '';
				foreach(self::RECEIVABLES_SOURCE_TYPE as $val){
					if($val['value'] == $value){
						$result = $val['name'];
						break;
					}
				}
			}
			else $result = self::RECEIVABLES_SOURCE_TYPE;

			return $result;
		}
	}