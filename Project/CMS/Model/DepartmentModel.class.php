<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-28
	 * Time: 14:48
	 */

	namespace CMS\Model;

	class DepartmentModel extends CMSModel{
		protected $connection = 'DB_CONFIG_COMMON';
		protected $tableName  = 'department';
		const TABLE_NAME = 'department';
		protected $autoCheckFields = true;

		public function getList($control = []){
			$table_department = DepartmentModel::TABLE_NAME;
			$this_database    = self::DATABASE_NAME;
			$order            = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status           = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$where            = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			$sql    = "
SELECT *
FROM $this_database.$table_department d
$where
$order
";
			$result = $this->query($sql);

			return $result;
		}
	}