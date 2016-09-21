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
				$data['creator'] = I('session.MANAGER_USER_ID', 0, 'int');
				$data['creatime'] = time();
				$data['director_id'] = 2799;
				$data['contacts_1_id'] = 2799;
				$data['contacts_2_id'] = 2799;
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