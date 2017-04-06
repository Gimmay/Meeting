<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 17:08
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ClientModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		// todo 统计签到、收款等报表时 注意场外/场内的客户区别
		protected $tableName        = 'client';
		protected $autoCheckFields  = true;
		protected $connection       = 'DB_CONFIG_ROYALWISS_DEAL';
		private   $_defaultPassword = '';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID'    => 'mid',
			'clientID'     => 'cid',
			'type'         => 'type',
			'reviewStatus' => 'reviewed',
			'signStatus'   => 'signed',
			'limit'        => 'limit'
		];
		/** 性别 */
		const GENDER = [
			0 => '未指定',
			1 => '男',
			2 => '女'
		];
		/** 是否新客 */
		const IS_NEW = [
			9 => '',
			0 => '否',
			1 => '是'
		];
		/** 客户类型 */
		const TYPE = ['其他', '终端', '老板娘', '嘉宾', '员工', '陪同'];

		public function getList($control = []){
			$getCustomColumn = function (){
				/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
				$attendee_model     = D('RoyalwissD/Attendee');
				$custom_column_list = $attendee_model->getColumnList(true);
				$list               = [];
				foreach($custom_column_list as $column) $list[] = $column['column_name'];
				$str = count($list)>0 ? implode(',', $list).',' : ',';

				return $str;
			};
			$keyword         = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order           = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status          = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id      = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$client_id       = $control[self::CONTROL_COLUMN_PARAMETER_SELF['clientID']];
			$review_status   = $control[self::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus']];
			$sign_status     = $control[self::CONTROL_COLUMN_PARAMETER_SELF['signStatus']];
			$type            = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$limit           = $control[self::CONTROL_COLUMN_PARAMETER_SELF['limit']];
			$where           = ' WHERE 0 = 0 ';
			$split           = '';
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
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			if(isset($client_id) && isset($client_id[0]) && isset($client_id[1])) $where .= " and cid $client_id[0] $client_id[1] ";
			if(isset($type) && isset($type[0]) && isset($type[1])) $where .= " and type $type[0] $type[1] ";
			if(isset($review_status) && isset($review_status[0]) && isset($review_status[1])) $where .= " and review_status $review_status[0] $review_status[1] ";
			if(isset($sign_status) && isset($sign_status[0]) && isset($sign_status[1])) $where .= " and sign_status $sign_status[0] $sign_status[1] ";
			if(isset($limit) && isset($limit[0]) && isset($limit[1])) $split = " limit $limit[0], $limit[1] ";
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
			FROM meeting_royalwiss_deal.grouping g
			JOIN meeting_royalwiss_deal.grouping_member gm ON gm.gid = g.id
			WHERE g.mid = a1.mid AND gm.cid = a1.cid AND g.status <> 2 AND gm.status = 1
			ORDER BY gm.id desc
			LIMIT 1
		) group_name,
		(
			SELECT
				GROUP_CONCAT(CONCAT('[',hotel.name,']',room.name) SEPARATOR ', ')
			FROM hotel
			JOIN room ON room.hid = hotel.id and room.status <> 2
			JOIN room_customer ON room_customer.rid = room.id AND room_customer.status <> 2 AND room_customer.process_status = 1
			WHERE hotel.mid = a1.mid AND room.mid = a1.mid AND hotel.status <> 2 AND room_customer.cid = a1.cid
		) hotel_room_name,
		a1.cid,
		a1.id,
		a1.mid
	FROM meeting_royalwiss_deal.attendee a1
	JOIN meeting_royalwiss_deal.client c1 ON c1.id = a1.cid
	LEFT JOIN meeting_common.user u1 ON u1.id = a1.creator AND u1.status <> 2
	LEFT JOIN meeting_common.user u2 ON u2.id = a1.sign_director AND u2.status <> 2
	LEFT JOIN meeting_common.user u3 ON u3.id = a1.review_director AND u3.status <> 2
) tab
$where
$order
$split
";

			return $this->query($sql);
		}

		/**
		 * 创建客户
		 *
		 * @param array $data 客户信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建客户成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建客户失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取默认密码
		 *
		 * @return string
		 */
		public function getDefaultPassword(){
			return $this->_defaultPassword;
		}

		/**
		 * 获取字段列表
		 *
		 * @return array
		 */
		public function getColumnList(){
			$list = $this->query("
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT,
	'fixed' TYPE
FROM information_schema.TABLES t
JOIN information_schema.COLUMNS c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = 'meeting_royalwiss_deal'
AND t.TABLE_NAME = 'client'
");

			return $list;
		}

		/**
		 * 获取Select插件的数据列表（审核的客户）
		 *
		 * @param int  $meeting_id   会议ID
		 * @param bool $include_unit 是否包含会所信息
		 *
		 * @return array
		 */
		public function getSelectedList($meeting_id, $include_unit = false){
			$name = $include_unit ? "concat('[',c.unit,'] ',c.name)" : 'c.name';
			$sql  = "
SELECT
	c.id value,
	IF(a.sign_status = 1, $name, concat('* ', $name)) html,
	concat(c.name,',',c.name_pinyin,',',c.unit,',',c.unit_pinyin,',',c.mobile) keyword
FROM meeting_royalwiss_deal.client c
JOIN meeting_royalwiss_deal.attendee a ON c.id = a.cid
AND a.mid = $meeting_id
WHERE a.status = 1 AND a.review_status = 1
ORDER BY a.sign_time DESC, a.review_time DESC, a.id DESC";

			return $this->query($sql);
		}

		/**
		 * 获取Select插件的数据列表（审核的客户）
		 *
		 * @param int  $meeting_id   会议ID
		 * @param bool $include_unit 是否包含会所信息
		 *
		 * @return array
		 */
		public function getSelectedUnitList($meeting){
		}
	}