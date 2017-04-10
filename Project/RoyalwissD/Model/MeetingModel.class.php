<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-9
	 * Time: 11:19
	 */
	namespace RoyalwissD\Model;

	class MeetingModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'meeting';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'user'          => '_user',
			'processStatus' => 'process_status',
			'type'          => 'type',
			'meetingID'     => 'id',
			'process'       => 'process'
		];

		public function getList($control = []){
			$getCustomColumn = function (){
				/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
				$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
				$custom_column_list      = $meeting_configure_model->getColumnList(true);
				$list                    = [];
				foreach($custom_column_list as $column) $list[] = $column['column_name'];
				$str = count($list)>0 ? implode(',', $list).',' : ',';

				return $str;
			};
			$keyword         = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order           = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status          = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$user            = $control[self::CONTROL_COLUMN_PARAMETER_SELF['user']];
			$type            = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$id              = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$process_status  = $control[self::CONTROL_COLUMN_PARAMETER_SELF['processStatus']];
			$where           = ' where 0 = 0 ';
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
			if(isset($type)) $where .= " and type_code = $type ";
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($id) && isset($id[0]) && isset($id[1])) $where .= " and id $id[0] $id[1] ";
			if(isset($process_status) && isset($process_status[0]) && isset($process_status[1])) $where .= " and process_status $process_status[0] $process_status[1] ";
			$custom_column = $getCustomColumn();
			$sql           = "
SELECT * FROM (
	SELECT
		m.id,
		m.type type_code,
		m.name,
		m.name_pinyin,
		m.host,
		m.plan,
		m.place,
		m.sign_end_time,
		m.sign_start_time,
		m.start_time,
		m.end_time,
		m.director,
		m.contacts_1,
		m.contacts_2,
		m.brief,
		m.logo,
		m.comment,
		m.process_status,
		m.status,
		m.creator creator_code,
		m.creatime,
		$custom_column
		u1.name creator,
		mt.name type
	FROM meeting_common.meeting m
	JOIN meeting_common.meeting_type mt ON mt.type = m.type AND mt.status = 1
	JOIN meeting_royalwiss_deal.meeting_configure mc ON mc.mid = m.id
	LEFT JOIN meeting_common.user u1 ON u1.id = m.creator AND u1.status <> 2
	WHERE (m.id IN (
		SELECT mid FROM meeting_manager
		WHERE meeting_manager.uid = $user
	)
	OR ifnull(
		(
			SELECT permission.id
			FROM USER
			JOIN user_assign_role ON user_assign_role.uid = USER.id
			JOIN role ON user_assign_role.rid = role.id AND role.STATUS = 1
			JOIN role_assign_permission ON role_assign_permission.rid = role.id
			JOIN permission ON role_assign_permission.pid = permission.id
			WHERE USER.id = $user AND permission.CODE = 'GENERAL-MEETING.VIEW_ALL'
			LIMIT 1
		), NULL
	))
) tab
$where
$order
";

			return $this->query($sql);
		}

		/**
		 * 获取会议类型
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return int
		 */
		public function getMeetingType($meeting_id){
			$record = $this->where(['id' => $meeting_id])->find();

			return $record['type'];
		}
	}