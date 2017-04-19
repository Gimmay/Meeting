<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 15:05
	 */
	namespace General\Logic;

	class GeneralLogic{
		public function __construct(){
			$this->_initialize();
		}

		public function _initialize(){
		}

		/**
		 * 排序数组
		 *
		 * @param array  $list         数组列表
		 * @param string $order_column 排序字段
		 * @param string $order_method 排序方式
		 *
		 * @return array
		 */
		public static function orderList($list, $order_column, $order_method){
			$arr_sort     = [];
			$order_method = $order_method == 'desc' ? SORT_DESC : SORT_ASC;
			foreach($list as $key => $val){
				$arr_sort[$key] = iconv("UTF-8", "GBK", $val[$order_column]);
			}
			array_multisort($arr_sort, $order_method, $list);

			return $list;
		}
	}