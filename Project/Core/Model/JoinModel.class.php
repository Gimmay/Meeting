<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 16:31
	 */
	namespace Core\Model;
	
	class JoinModel extends CoreModel{
		protected $tableName   = 'join';
		protected $tablePrefix = 'workflow_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			$data['creatime']  = time();
			$data['creator']   = I('session.MANAGER_USER_ID', 0, 'int');
			C('TOKEN_ON', false); // todo delete this
			if($this->create($data)){
				$result = $this->add($data);
				if($result) return ['status' => true, 'message' => '记录成功', 'id' => $result];
				else return ['status' => false, 'message' => $this->getError()];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}