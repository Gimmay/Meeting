<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 14:40
	 */
	namespace General\Logic;

	class Time{
		/**
		 * 获取当前时间字符串
		 *
		 * @return string
		 */
		public static function getCurrentTime(){
			return date('Y-m-d H:i:s');
		}

		/**
		 * 判断时间字符串是否为空
		 *
		 * @param string $str 字符串
		 *
		 * @return null|string 是则返回空 否则返回原字符串
		 */
		public static function isNull($str){
			return $str == '' ? null : $str;
		}
	}