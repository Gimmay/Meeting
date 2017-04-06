<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 12:07
	 */
	namespace CMS\Model;

	class UserModel extends CMSModel{
		public function _initialize(){
			parent::_initialize();
		}

		const ROLE_NAME_SEPARATOR = '&$@#,#@$&';
		protected $tableName       = 'user';
		protected $autoCheckFields = true;
		protected $connection      = "DB_CONFIG_COMMON";
		const CONTROL_COLUMN_PARAMETER_SELF = ['roleID' => 'rid'];

		public function getList($control = []){
			$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order   = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status  = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$where   = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					name like '%$keyword%'
					or name_pinyin like '%$keyword%'
					or nickname like '%$keyword%'
					or nickname_pinyin like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			$sql = "
SELECT * FROM (
	SELECT
		u1.id,
		u1.name,
		u1.name_pinyin,
		u1.password,
		u1.nickname,
		u1.nickname_pinyin,
		u1.status,
		u1.comment,
		u1.parent_id,
		u3.name parent_name,
		u1.creatime,
		u1.creator creator_code,
		u2.name creator,
		(SELECT group_concat(r1.name ORDER BY r1.name_pinyin SEPARATOR '".self::ROLE_NAME_SEPARATOR."')
		FROM meeting_common.role r1
		JOIN meeting_common.user_assign_role uar1 ON r1.id = uar1.rid
		WHERE u1.id = uar1.uid AND r1.status <> 2) role_name,
		(SELECT group_concat(r1.id ORDER BY r1.name_pinyin)
		FROM meeting_common.role r1
		JOIN meeting_common.user_assign_role uar1 ON r1.id = uar1.rid
		WHERE u1.id = uar1.uid AND r1.status <> 2) role_id
	FROM meeting_common.user u1
	LEFT JOIN meeting_common.user u2 ON u2.id = u1.creator AND u2.status <> 2
	LEFT JOIN meeting_common.user u3 ON u3.id = u1.parent_id AND u3.status <> 2
) tab
$where
$order
";

			return $this->query($sql);
		}

		/**
		 * 获取Select插件的数据列表
		 *
		 * @return array
		 */
		public function getSelectedList(){
			return $this->where('status = 1')->field('id value, IF(nickname<>\'\', concat(name,\' (\',nickname,\')\'),name) html, concat(name,\',\',name_pinyin,\',\',nickname,\',\',nickname_pinyin) keyword')->select();
		}
	}