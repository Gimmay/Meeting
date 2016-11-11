<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-26
	 * Time: 15:31
	 */
	namespace Manager\Model;

	class PayMethodModel extends ManagerModel{
		protected $tableName       = 'pay_method';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){

			parent::_initialize();
		}

		public function getPayMethodSelectList(){
			$sql = "SELECT
	CONCAT(`name`) `html`, CONCAT( pinyin_code) `keyword`,
	`id` `value`
FROM `workflow_pay_method`
WHERE STATUS = 1";
			return $this->query($sql);
		}
	}