<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Model;

	class ClientModel extends MobileModel{
		protected $tableName   = 'client';
		protected $tablePrefix = 'user_';
		protected $autoCheckFields = true;
		public function _initialize(){
			parent::_initialize();
		}

		public function getClientSelectList($mid){
			$sql = "SELECT CONCAT(`name`) `html`, CONCAT( pinyin_code),
user_client.id `value`
FROM `workflow_join`
LEFT JOIN `user_client`
ON workflow_join.cid = user_client.id
WHERE MID = $mid AND workflow_join.status = 1";
			return $this->query($sql);
		}
	}