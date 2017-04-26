<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 17:08
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class ClientModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'client';
		const TABLE_NAME = 'client';
		protected $autoCheckFields  = true;
		protected $connection       = 'DB_CONFIG_ROYALWISS_DEAL';
		private   $_defaultPassword = '';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID'    => 'mid',
			'clientID'     => 'cid',
			'type'         => 'type',
			'reviewStatus' => 'reviewed',
			'signStatus'   => 'signed',
			'limit'        => 'limit',
			'signCode'     => 'sign_code'
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
		/** 特殊客户类型：此类客户类型不会被进入签到统计，但是可以收款 */
		const TYPE_SPECIAL = ['内场'];
		/** 客户类型 */
		const TYPE = ['其他', '终端', '老板娘', '嘉宾', '员工', '陪同', '专家', '观摩'];

		/**
		 * 获取客户类型
		 *
		 * @return array
		 */
		public static function getClientType(){
			$list = array_merge(self::TYPE, self::TYPE_SPECIAL);

			return $list;
		}

		public function getList($control = []){
			$table_client        = $this->tableName;
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
				foreach($custom_column_list as $column){
					if(preg_match('/'.AttendeeModel::CUSTOM_COLUMN.'(\d)+/', $column['column_name'])) $list[] = $column['column_name'];
				}
				$str = count($list)>0 ? implode(',', $list).',' : '';

				return $str;
			};
			$keyword             = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order               = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status              = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id          = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$client_id           = $control[self::CONTROL_COLUMN_PARAMETER_SELF['clientID']];
			$review_status       = $control[self::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus']];
			$sign_status         = $control[self::CONTROL_COLUMN_PARAMETER_SELF['signStatus']];
			$sign_code         = $control[self::CONTROL_COLUMN_PARAMETER_SELF['signCode']];
			$type                = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$limit               = $control[self::CONTROL_COLUMN_PARAMETER_SELF['limit']];
			$where               = ' WHERE 0 = 0 ';
			$split               = '';
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
			if(isset($sign_code)) $where .= " and sign_code = '$sign_code'";
			if(isset($client_id) && isset($client_id[0]) && isset($client_id[1])) $where .= " and cid $client_id[0] $client_id[1] ";
			if(isset($type) && isset($type[0]) && isset($type[1])) $where .= " and type $type[0] $type[1] ";
			elseif(isset($type) && is_bool($type)){
				$str = "";
				foreach(self::TYPE as $val) $str .= "'$val',";
				$str = trim($str, ',');
				if($type) $where .= " and type in ($str)";
			}
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
		a1.consumption,
		a1.receivables,
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
		/* 微信字段 */
		c1.wechat_type,
		c1.wechat_openid,
		c1.wechat_userid,
		c1.wechat_nickname,
		c1.wechat_mobile,
		c1.wechat_email,
		c1.wechat_gender,
		c1.wechat_lang,
		c1.wechat_country,
		c1.wechat_province,
		c1.wechat_city,
		c1.wechat_avatar,
		c1.wechat_is_follow,
		c1.wechat_department,
		c1.wechat_appid,
		c1.wechat_id,
		c1.wechat_position,
		/* 微信字段 */
		/* 会所字段 */
		u0.id uid,
		u0.is_new unit_is_new,
		u0.area unit_area,
		/* 会所字段 */
		a1.cid,
		a1.id,
		a1.mid
	FROM $this_database.$table_attendee a1
	JOIN $this_database.$table_client c1 ON c1.id = a1.cid
	LEFT JOIN $this_database.$table_unit u0 ON u0.name = c1.unit
	LEFT JOIN $common_database.$table_user u1 ON u1.id = a1.creator AND u1.status <> 2
	LEFT JOIN $common_database.$table_user u2 ON u2.id = a1.sign_director AND u2.status <> 2
	LEFT JOIN $common_database.$table_user u3 ON u3.id = a1.review_director AND u3.status <> 2
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
			$table_client  = $this->tableName;
			$table_unit    = UnitModel::TABLE_NAME;
			$this_database = self::DATABASE_NAME;
			$list          = $this->query("
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	concat('unit_', COLUMN_NAME) COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT,
	'fixed' TYPE
FROM information_schema.TABLES t
JOIN information_schema.COLUMNS c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_unit'
AND c.COLUMN_NAME IN ('is_new', 'area')
UNION
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
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_client'
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
			$getTypeString  = function (){
				$str = "";
				foreach(self::TYPE as $val) $str .= "'$val',";
				$str = trim($str, ',');

				return $str;
			};
			$table_client   = $this->tableName;
			$table_attendee = AttendeeModel::TABLE_NAME;
			$this_database  = self::DATABASE_NAME;
			$name           = $include_unit ? "concat('[',c.unit,'] ',c.name)" : 'c.name';
			$type           = $getTypeString();
			$sql            = "
SELECT
	c.id value,
	IF(a.sign_status = 1, IF(c.type IN ($type), $name, concat('× ', $name)), IF(c.type IN ($type), concat('* ', $name), concat('× ', $name))) html,
	concat(c.name,',',c.name_pinyin,',',c.unit,',',c.unit_pinyin) keyword
FROM $this_database.$table_client c
JOIN $this_database.$table_attendee a ON c.id = a.cid
AND a.mid = $meeting_id
WHERE a.status = 1 AND a.review_status = 1
ORDER BY a.sign_time DESC, a.review_time DESC, a.id DESC";

			return $this->query($sql);
		}


		/**
		 * 获取团队Select插件的数据列表（审核的客户）
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getTeamSelectedList($meeting_id){
			$table_client   = $this->tableName;
			$table_attendee = AttendeeModel::TABLE_NAME;
			$this_database  = self::DATABASE_NAME;
			$sql            = "
SELECT
	DISTINCT c.team value,
	c.team html,
	concat(c.team) keyword
FROM $this_database.$table_client c
JOIN $this_database.$table_attendee a ON c.id = a.cid
AND a.mid = $meeting_id
WHERE a.status = 1 AND a.review_status = 1
ORDER BY c.team
";
			$result         = $this->query($sql);

			return $result;
		}
	}