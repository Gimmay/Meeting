<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-6
	 * Time: 16:11
	 */
	namespace CMS\Model;

	use General\Model\GeneralModel;
	use General\Model\MeetingModel;

	class SystemLogModel extends CMSModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'system_log';
		const TABLE_NAME = 'system_log';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'operator'  => 'operator',
			'object'    => 'object',
			'action'    => 'action',
			'meetingID' => 'mid'
		];

		public function getList($control = []){
			$table_system_log = $this->tableName;
			$table_user       = UserModel::TABLE_NAME;
			$table_meeting    = MeetingModel::TABLE_NAME;
			$common_database  = GeneralModel::DATABASE_NAME;
			$keyword    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status     = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$operator   = $control[self::CONTROL_COLUMN_PARAMETER_SELF['operator']];
			$object     = $control[self::CONTROL_COLUMN_PARAMETER_SELF['object']];
			$action     = $control[self::CONTROL_COLUMN_PARAMETER_SELF['action']];
			$where      = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					information like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($operator) && isset($operator[0]) && isset($operator[1])) $where .= " and operator_code $operator[0] $operator[1] ";
			if(isset($object) && isset($object[0]) && isset($object[1])) $where .= " and object $object[0] $object[1] ";
			if(isset($action) && isset($action[0]) && isset($action[1])) $where .= " and action $action[0] $action[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		sl.id,
		sl.operator operator_code,
		u2.name operator,
		sl.object,
		sl.information,
		sl.action,
		sl.ip,
		sl.comment,
		sl.mid,
		sl.creatime,
		sl.creator creator_code,
		m.name meeting_name,
		u1.name creator
	FROM $common_database.$table_system_log sl
	LEFT JOIN $common_database.$table_user u1 ON u1.id = sl.creator AND u1.status <> 2
	LEFT JOIN $common_database.$table_meeting m ON m.id = sl.mid AND m.status <> 2
	JOIN $common_database.$table_user u2 ON u2.id = sl.operator AND u2.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}
	}