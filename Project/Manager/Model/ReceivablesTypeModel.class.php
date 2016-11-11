<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-26
	 * Time: 15:31
	 */
	namespace Manager\Model;

	class ReceivablesTypeModel extends ManagerModel{
		protected $tableName       = 'receivables_type';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){

			parent::_initialize();
		}

		public function getReceivablesTypeSelectList(){
			$sql = "SELECT
	CONCAT(`name`) `html`, CONCAT( pinyin_code) `keyword`,
	`id` `value`
FROM `workflow_receivables_type`
WHERE STATUS = 1";
			return $this->query($sql);
		}
	}