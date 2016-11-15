<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-19
	 * Time: 10:45
	 */
	namespace Manager\Model;

	class ClientModel extends ManagerModel{
		protected $tableName       = 'client';
		protected $tablePrefix     = 'user_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getColumn($just_desc = false){
			$result = $this->query('(SELECT
	COLUMN_NAME `NAME`,
	(CASE WHEN COLUMN_NAME = \'gender\' THEN \'性别\' ELSE COLUMN_COMMENT END) `DESC`,
	COLUMN_TYPE `TYPE`
FROM information_schema.`COLUMNS`
WHERE TABLE_SCHEMA = \'gimmay_meeting\' AND TABLE_NAME = \'user_client\'
AND COLUMN_NAME NOT IN (\'id\', \'password\', \'status\', \'creatime\', \'creator\', \'pinyin_code\'))
UNION SELECT \'registration_date\', \'报名时间\', \'date\'
UNION SELECT \'traffic_method\', \'交通方式\', \'varchar(20)\'
UNION SELECT \'inviter_id\', \'邀约人\', \'int(11)\'
UNION SELECT \'price\', \'收款\', \'decimal(12, 2)\'
');
			if($just_desc){
				$list[0] = [];
				foreach($result as $val) array_push($list[0], $val['desc']);

				return $list;
			}
			else return $result;
		}

		public function getSelfColumn($just_desc = false){
			$result = $this->query('(SELECT
	COLUMN_NAME `NAME`,
	(CASE WHEN COLUMN_NAME = \'gender\' THEN \'性别\' ELSE COLUMN_COMMENT END) `DESC`,
	COLUMN_TYPE `TYPE`
FROM information_schema.`COLUMNS`
WHERE TABLE_SCHEMA = \'gimmay_meeting\' AND TABLE_NAME = \'user_client\'
AND COLUMN_NAME NOT IN (\'id\', \'password\', \'status\', \'creatime\', \'creator\', \'pinyin_code\'))');
			if($just_desc){
				$list[0] = [];
				foreach($result as $val) array_push($list[0], $val['desc']);

				return $list;
			}
			else return $result;
		}

		public function getClientSelectList($mid){
			$sql = "SELECT CONCAT(`name`) `html`, CONCAT( pinyin_code),
user_client.id `value`
FROM `workflow_join`
LEFT JOIN `user_client` 
ON workflow_join.cid = user_client.id
WHERE mid = $mid AND workflow_join.status = 1 AND user_client.status = 1";

			return $this->query($sql);
		}

	}