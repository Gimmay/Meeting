<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:55
	 */
	namespace Core\Model;

	class MeetingModel extends CoreModel{
		protected $tableName   = 'meeting';
		protected $tablePrefix = 'workflow_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createMeeting($data){
			if($this->create($data)){
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建成功'] : [
					'status'  => false,
					'message' => $this->getError()
				];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function editMeeting(){
			if($this->create()){
				$result = $this->save();

				return $result ? ['status' => true, 'message' => '修改成功'] : [
					'status'  => false,
					'message' => $this->getError()
				];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}