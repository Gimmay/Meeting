<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 16:31
	 */
	namespace Core\Model;

	use Exception;

	class JoinModel extends CoreModel{
		protected $tableName   = 'join';
		protected $tablePrefix = 'workflow_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			C('TOKEN_ON', false); // todo delete this
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '记录成功', 'id' => $result];
					else return ['status' => false, 'message' => '记录失败'];
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