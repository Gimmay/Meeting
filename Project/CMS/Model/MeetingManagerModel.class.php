<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-10
	 * Time: 15:23
	 */
	namespace CMS\Model;

	use General\Model\GeneralModel;

	class MeetingManagerModel extends CMSModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'meeting_manager';
		const TABLE_NAME = 'meeting_manager';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'userID'    => 'uid',
			'roleID'    => 'rid'
		];

		/**
		 * 获取会务人员列表
		 *
		 * @param array $control
		 *
		 * @return array
		 */
		public function getList($control = []){
			$table_meeting_manager = $this->tableName;
			$table_user            = UserModel::TABLE_NAME;
			$common_database       = GeneralModel::DATABASE_NAME;
			$keyword               = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order                 = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$meeting_id            = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$user_id               = $control[self::CONTROL_COLUMN_PARAMETER_SELF['userID']];
			$role_id               = $control[self::CONTROL_COLUMN_PARAMETER_SELF['roleID']];
			$where                 = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					name like '%$keyword%'
					or name_pinyin like '%$keyword%'
				)";
			}
			if(isset($user_id) && isset($user_id[0]) && isset($user_id[1])) $where .= " and uid $user_id[0] $user_id[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			if(isset($role_id)) $where .= " and rid = $role_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		mm.id,
		mm.status,
		mm.mid,
		mm.rid,
		u2.name creator,
		u1.id uid,
		u1.name,
		u1.name_pinyin,
		u1.nickname,
		u1.nickname_pinyin,
		u1.creator creator_code
	FROM $common_database.$table_meeting_manager mm
	JOIN $common_database.$table_user u1 ON u1.id = mm.uid AND u1.status = 1
	LEFT JOIN $common_database.$table_user u2 ON u2.id = mm.creator AND u2.status <> 2
	WHERE mm.status = 1
) tab
$where
$order";

			$result = $this->query($sql);

			return $result;
		}

		public function getUnassignedUser($control = []){
			$table_meeting_manager = $this->tableName;
			$table_user            = UserModel::TABLE_NAME;
			$common_database       = GeneralModel::DATABASE_NAME;
			$keyword    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$meeting_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$user_id    = $control[self::CONTROL_COLUMN_PARAMETER_SELF['userID']];
			$role_id    = $control[self::CONTROL_COLUMN_PARAMETER_SELF['roleID']];
			$where      = $sub_where = '';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					u1.name like '%$keyword%'
					or u1.name_pinyin like '%$keyword%'
				)";
			}
			if(isset($order)) $order = " ORDER BY $order ";
			if(isset($user_id) && isset($user_id[0]) && isset($user_id[1])) $where .= " and u1.id $user_id[0] $user_id[1] ";
			if(isset($meeting_id)) $sub_where .= " and mm.mid = $meeting_id ";
			if(isset($role_id)) $sub_where .= " and mm.rid = $role_id ";
			$sql    = "SELECT * FROM $common_database.$table_user u1
WHERE u1.id NOT IN (
	SELECT uid FROM $common_database.$table_meeting_manager mm
	WHERE mm.STATUS = 1
	$sub_where
)
AND u1.status = 1
$where
$order
";
			$result = $this->query($sql);

			return $result;
		}
	}