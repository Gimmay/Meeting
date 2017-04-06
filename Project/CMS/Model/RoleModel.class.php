<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 17:27
	 */
	namespace CMS\Model;

	use General\Model\RoleModel as GeneralRoleModel;

	class RoleModel extends CMSModel{
		public function _initialize(){
			parent::_initialize();
		}

		const USER_NAME_SEPARATOR = '&$@#,#@$&';
		protected $tableName       = 'role';
		protected $autoCheckFields = true;
		protected $connection      = "DB_CONFIG_COMMON";
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'level' => 'level'
		];

		public function getList($control = []){
			$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order   = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status  = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$level   = $control[self::CONTROL_COLUMN_PARAMETER_SELF['level']];
			$where   = ' WHERE 0 = 0 ';
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
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($level)) $where .= " and level ".GeneralRoleModel::LEVEL_ROLE." $level ";
			$sql = "
SELECT * FROM (
	SELECT
		r1.id,
		r1.name,
		r1.name_pinyin,
		r1.status,
		r1.level,
		r1.comment,
		r1.creator creator_code,
		r1.creatime,
		u1.name creator,
		(SELECT
		GROUP_CONCAT(u2.name ORDER BY u2.name_pinyin SEPARATOR '".self::USER_NAME_SEPARATOR."')
		FROM meeting_common.user u2
		JOIN meeting_common.user_assign_role uar1 ON uar1.uid = u2.id
		WHERE r1.id = uar1.rid AND u2.status <> 2) user_name,
		(SELECT
		GROUP_CONCAT(u2.id ORDER BY u2.name_pinyin)
		FROM meeting_common.user u2
		JOIN meeting_common.user_assign_role uar1 ON uar1.uid = u2.id
		WHERE r1.id = uar1.rid AND u2.status <> 2) user_id
	FROM meeting_common.role r1
	LEFT JOIN meeting_common.user u1 ON u1.id = r1.creator AND u1.status <> 2
) tab
$where
$order
";

			return $this->query($sql);
		}
	}