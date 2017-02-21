<?php
	namespace Core\Model;

	use Think\Model;

	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:56
	 */
	class CoreModel extends Model{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerException($message){
			if(stripos($message, 'Duplicate entry') !== false) return [
				'status'  => false,
				'message' => "该记录已经存在"
			];
			if(stripos($message, 'a foreign key constraint fails') !== false) return [
				'status'  => false,
				'message' => "部分字段违反外键约束"
			];
			if(stripos($message, 'doesn\'t have a default value') !== false) return [
				'status'  => false,
				'message' => "未提交非空字段"
			];
			if(stripos($message, 'Incorrect decimal value') !== false) return [
				'status'  => false,
				'message' => "错误的浮点数据"
			];
			if(stripos($message, 'Incorrect date value') !== false) return [
				'status'  => false,
				'message' => "错误的日期类型"
			];
			if(stripos($message, 'Out of range value for column') !== false) return [
				'status'  => false,
				'message' => "数据超过指定范围"
			];

			return ['status' => true];
		}
	}