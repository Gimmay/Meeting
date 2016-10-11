<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-16
	 * Time: 17:35
	 */
	namespace Core\Model;

	use Exception;

	class AssignPermissionModel extends CoreModel{
		protected $tableName       = 'assign_permission';
		protected $tablePrefix     = 'system_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '授权成功', 'id' => $result];
					else return ['status' => false, 'message' => '授权失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function deleteRecord($condition){
			if($this->create($condition)){
				try{
					$result = $this->where($condition)->delete();
					if($result) return ['status' => true, 'message' => '收回权限成功'];
					else return ['status' => false, 'message' => '未收回权限'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}