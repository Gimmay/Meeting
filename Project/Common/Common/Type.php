<?php
	/**
	 * Created by PhpStorm.
	 * User: Quasar
	 * Date: 2016/4/20
	 * Time: 15:55
	 */
	namespace Quasar;

	class Type{
		/**
		 * 将xml对象或xml字符串输出为数组
		 *
		 * @param array|object $xml   xml字符串或对象或数组
		 * @param int          $isobj 判断是否已经将$sxml转换为xml对象
		 *
		 * @return array
		 */
		public function parseXmlToArray($xml, $isobj = 0){
			if($isobj == 0){
				$xml = simplexml_load_string($xml);
				$xml = json_decode(json_encode($xml));
			}
			$result = [];
			foreach($xml as $key => $val){
				if(is_object($val) || is_array($val)) $result[$key] = $this->parseXmlToArray($val, 1);
				else $result[$key] = $val;
			}

			return $result;
		}

		/**
		 * 将对象转为数组
		 *
		 * @param object|array $obj 需要转换的对象
		 *
		 * @return array 转化为数组后的数据
		 */
		public function parseObjectToArray($obj){
			$result = [];
			foreach($obj as $key => $val){
				if(is_object($val) || is_array($val)) $result[$key] = $this->parseObjectToArray($val);
				else $result[$key] = $val;
			}

			return $result;
		}
	}