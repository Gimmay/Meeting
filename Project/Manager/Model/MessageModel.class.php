<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-19
	 * Time: 10:45
	 */
	namespace Manager\Model;

	class MessageModel extends ManagerModel{
		protected $tableName       = 'message';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getMessageSelectList(){
			return $this->field('name html, id value, name keyword')->where("status = 1")->select();
		}

	}