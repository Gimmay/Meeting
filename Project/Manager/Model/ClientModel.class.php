<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-19
	 * Time: 10:45
	 */
	namespace Manager\Model;

	use Quasar\StringPlus;

	class ClientModel extends ManagerModel{
		protected $tableName   = 'client';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
		}

		public function getColumn($just_desc = false){
			$result = $this->query('SELECT
	COLUMN_NAME `NAME`,
	(CASE WHEN COLUMN_NAME = \'gender\' THEN \'性别\' ELSE COLUMN_COMMENT END) `DESC`,
	COLUMN_TYPE `TYPE`
FROM information_schema.`COLUMNS`
WHERE TABLE_SCHEMA = \'gimmay_meeting\'
AND TABLE_NAME = \'user_client\'
AND COLUMN_NAME NOT IN (\'id\', \'password\', \'status\', \'creatime\', \'creator\', \'pinyin_code\')');
			if($just_desc){
				$list[0] = [];
				foreach($result as $val) array_push($list[0], $val['desc']);

				return $list;
			}
			else return $result;
		}

	}