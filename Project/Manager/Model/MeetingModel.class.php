<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 10:09
	 */
	namespace Manager\Model;

	class MeetingModel extends ManagerModel{
		protected $tableName       = 'meeting';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getMeetingForSelect(){
			return $this->field('name html, id value, name keyword')->where("status = 1 or status = 2")->select();
		}
	}