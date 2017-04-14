<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-7
	 * Time: 16:18
	 */
	namespace RoyalwissD\Model;

	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class ReportModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $connection = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID'    => 'mid',
			'clientID'     => 'cid',
			'type'         => 'type',
			'reviewStatus' => 'reviewed',
			'signStatus'   => 'signed',
			'area'         => 'area',
			'isNew'        => 'is_new'
		];

		public function getClientList($control = []){
			$table_client        = ClientModel::TABLE_NAME;
			$table_attendee      = AttendeeModel::TABLE_NAME;
			$table_user          = UserModel::TABLE_NAME;
			$table_unit          = UnitModel::TABLE_NAME;
			$table_hotel         = HotelModel::TABLE_NAME;
			$table_room          = RoomModel::TABLE_NAME;
			$table_room_customer = RoomCustomerModel::TABLE_NAME;
			$table_grouping      = GroupingModel::TABLE_NAME;
			$table_group_member  = GroupMemberModel::TABLE_NAME;
			$common_database     = GeneralModel::DATABASE_NAME;
			$this_database       = self::DATABASE_NAME;
			$getCustomColumn     = function (){
				/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
				$attendee_model     = D('RoyalwissD/Attendee');
				$custom_column_list = $attendee_model->getColumnList(true);
				$list               = [];
				foreach($custom_column_list as $column) $list[] = $column['column_name'];
				$str = count($list)>0 ? implode(',', $list).',' : '';

				return $str;
			};
			$keyword             = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order               = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$meeting_id          = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$sign_status         = $control[self::CONTROL_COLUMN_PARAMETER_SELF['signStatus']];
			$type                = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$where               = ' ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				AND (
					name like '%$keyword%'
					OR name_pinyin like '%$keyword%'
					OR unit like '%$keyword%'
					OR unit_pinyin like '%$keyword%'
					OR mobile like '%$keyword%'
				)";
			}
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			if(isset($type) && isset($type[0]) && isset($type[1])) $where .= " and type $type[0] $type[1] ";
			if(isset($sign_status) && isset($sign_status[0]) && isset($sign_status[1])) $where .= " and sign_status $sign_status[0] $sign_status[1] ";
			$custom_column = $getCustomColumn();
			$sql           = "
SELECT * FROM (
	SELECT
		c1.name,
		c1.name_pinyin,
		c1.unit,
		c1.unit_pinyin,
		c1.mobile,
		c1.gender,
		c1.birthday,
		c1.email,
		c1.title,
		c1.position,
		c1.address,
		c1.id_card_number,
		c1.develop_consultant,
		c1.service_consultant,
		c1.is_new,
		c1.password,
		c1.team,
		c1.type,
		c1.comment,
		$custom_column
		a1.register_type,
		a1.review_status,
		a1.review_time,
		a1.review_director review_director_code,
		u3.name review_director,
		a1.sign_qrcode,
		a1.sign_code,
		a1.sign_code_qrcode,
		a1.sign_status,
		a1.sign_time,
		a1.sign_type,
		a1.sign_director sign_director_code,
		u2.name sign_director,
		a1.print_status,
		a1.print_time,
		a1.gift_status,
		a1.gift_time,
		a1.status,
		a1.creator creator_code,
		u1.name creator,
		a1.creatime,
		(
			SELECT g.NAME
			FROM $this_database.$table_grouping g
			JOIN $this_database.$table_group_member gm ON gm.gid = g.id
			WHERE g.mid = a1.mid AND gm.cid = a1.cid AND g.status <> 2 AND gm.status = 1 AND gm.process_status = 1   
			ORDER BY gm.id desc
			LIMIT 1
		) group_name,
		(
			SELECT
				GROUP_CONCAT(CONCAT('[',h.name,']',r.name) SEPARATOR ', ')
			FROM $this_database.$table_hotel h
			JOIN $this_database.$table_room r ON r.hid = h.id and r.status <> 2
			JOIN $this_database.$table_room_customer rc ON rc.rid = r.id AND rc.status <> 2 AND rc.process_status = 1
			WHERE h.mid = a1.mid AND r.mid = a1.mid AND h.status <> 2 AND rc.cid = a1.cid
		) hotel_room_name,
		a1.cid,
		a1.id,
		a1.mid,
		u4.area unit_area,
		u4.address unit_address,
		u4.is_new unit_is_new,
		u4.id uid
	FROM $this_database.$table_attendee a1
	JOIN $this_database.$table_client c1 ON c1.id = a1.cid
	LEFT JOIN $common_database.$table_user u1 ON u1.id = a1.creator AND u1.status <> 2
	LEFT JOIN $common_database.$table_user u2 ON u2.id = a1.sign_director AND u2.status <> 2
	LEFT JOIN $common_database.$table_user u3 ON u3.id = a1.review_director AND u3.status <> 2
	LEFT JOIN $this_database.$table_unit u4 ON u4.name = c1.unit AND u4.status <> 2
) tab
WHERE status = 1 AND review_status = 1 $where
$order
";

			return $this->query($sql);
		}

		public function getUnitList($control = []){
			$keyword    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status     = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$where      = ' ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				AND (
					name like '%$keyword%'
					OR name_pinyin like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($meeting_id)) $meeting_id_condition = " and mid = $meeting_id ";
			else $meeting_id_condition = '';
			$sql    = "
SELECT
	*,
	if(signed_client > 0, 1, 0) is_signed
FROM(
	SELECT
		u.id,
		u.name,
		u.name_pinyin,
		u.address,
		u.area,
		u.is_new,
		u.comment,
		u.status,
		u.creatime,
		u.creator creator_code,
		(SELECT count(c.name) FROM meeting_royalwiss_deal.attendee a
		JOIN meeting_royalwiss_deal.client c on a.cid = c.id
		WHERE c.status = 1 AND a.status = 1 AND a.review_status = 1 AND u.name = c.unit $meeting_id_condition) total_client,
		(SELECT count(c.name) FROM meeting_royalwiss_deal.attendee a
		JOIN meeting_royalwiss_deal.client c on a.cid = c.id
		WHERE c.status = 1 AND a.status = 1 AND a.review_status = 1 AND u.name = c.unit $meeting_id_condition AND a.sign_status = 1) signed_client,
		u1.name creator
	FROM meeting_royalwiss_deal.unit u
	LEFT JOIN meeting_common.user u1 ON u1.id = u.creator AND u1.status <> 2
) tab
WHERE name in (
	SELECT c.unit FROM meeting_royalwiss_deal.attendee a
	JOIN meeting_royalwiss_deal.client c on a.cid = c.id
	WHERE c.status = 1 AND a.status = 1 AND a.review_status = 1 $meeting_id_condition
)
$where
$order
";
			$result = $this->query($sql);

			return $result;
		}

		public function getUnitArea($meeting_id){
		}

		public function getClientTeam($meeting_id){
		}
	}