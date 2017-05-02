<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 17:46
	 */

	namespace Mobile\Logic;

	use General\Logic\GeneralLogic;

	abstract class MobileLogic extends GeneralLogic{
		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		abstract public function handlerRequest($type, $opt = []);

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		abstract public function setData($type, $data);
	}