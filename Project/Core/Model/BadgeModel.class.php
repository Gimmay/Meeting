<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-11
	 * Time: 17:27
	 */
	namespace Core\Model;

	use Exception;

	class BadgeModel extends CoreModel{
		protected $tableName       = 'badge';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function createBadge($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '保存成功', 'id' => $result];
					else return ['status' => false, 'message' => '保存失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}