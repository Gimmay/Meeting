<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 16:16
	 */
	namespace CMS\Logic;

	use General\Logic\GeneralLogic;

	abstract class CMSLogic extends GeneralLogic{
		public function _initialize(){
		}

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