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
			if(stripos($message, 'Duplicate entry')) return [
				'status'  => false,
				'message' => "该记录已经存在"
			];
			if(stripos($message, 'a foreign key constraint fails')) return [
				'status'  => false,
				'message' => "部分字段违反外键约束"
			];
			if(stripos($message, 'doesn\'t have a default value')) return [
				'status'  => false,
				'message' => "未提交非空字段"
			];
			return ['status'=>true];
		}
	}