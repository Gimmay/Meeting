<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 17:18
	 */
	namespace Core\Model;

	class SignPlaceModel extends CoreModel{
		protected $tableName   = 'sign_place';
		protected $tablePrefix = 'workflow_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			if($this->create($data)){
				$result = $this->add($data);
				if($result) return ['status' => true, 'message' => '添加成功', 'id' => $result];
				else return ['status' => false, 'message' => $this->getError()];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}