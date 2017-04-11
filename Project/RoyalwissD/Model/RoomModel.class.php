<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-28
	 * Time: 15:41
	 */
	namespace RoyalwissD\Model;

	use CMS\Logic\Session;
	use Exception;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class RoomModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		const CLIENT_NAME_SEPARATOR = '&$@#,#@$&';
		protected $tableName = 'room';
		const TABLE_NAME = 'room';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'hotelID'   => 'hid',
			'roomID'    => 'rid',
			'type'      => 'type'
		];
		/** 入住状态 */
		const PROCESS_STATUS = [
			0 => '退房',
			1 => '入住中',
			2 => '换房'
		];
		/** 导入的房间类型名称 */
		const IMPORT_ROOM_TYPE_NAME = 'room_type_name';

		/**
		 * 创建房间
		 *
		 * @param array $data 房间信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建房间成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建房间失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getList($control = []){
			$table_client        = ClientModel::TABLE_NAME;
			$table_attendee      = AttendeeModel::TABLE_NAME;
			$table_user          = UserModel::TABLE_NAME;
			$table_hotel         = HotelModel::TABLE_NAME;
			$table_room          = self::TABLE_NAME;
			$table_room_type     = RoomTypeModel::TABLE_NAME;
			$table_room_customer = RoomCustomerModel::TABLE_NAME;
			$common_database     = GeneralModel::DATABASE_NAME;
			$this_database       = self::DATABASE_NAME;
			$keyword             = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order               = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status              = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id          = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$hotel_id            = $control[self::CONTROL_COLUMN_PARAMETER_SELF['hotelID']];
			$room_id             = $control[self::CONTROL_COLUMN_PARAMETER_SELF['roomID']];
			$type                = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$where               = ' WHERE 0 = 0 ';
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
			if(isset($type) && isset($type[0]) && isset($type[1])) $where .= " and type_code = $type[0] $type[1]";
			if(isset($room_id) && isset($room_id[0]) && isset($room_id[1])) $where .= " and id $room_id[0] $room_id[1]";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			if(isset($hotel_id)) $where .= " and hid = $hotel_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		r.id,
		r.mid,
		r.hid,
		r.name,
		r.name_pinyin,
		r.status,
		r.comment,
		r.creator creator_code,
		r.creatime,
		(
SELECT group_concat(c.name ORDER BY c.name_pinyin SEPARATOR '".self::CLIENT_NAME_SEPARATOR."')
FROM $this_database.$table_room_customer rc
JOIN $this_database.$table_client c ON c.id = rc.cid AND c.status <> 2
JOIN $this_database.$table_attendee a ON a.cid = c.id AND a.status <> 2
WHERE rc.mid = r.mid AND rc.rid = r.id AND rc.status <> 2 AND rc.process_status = 1 AND a.mid = r.mid) client,
		(
SELECT group_concat(c.id ORDER BY c.name_pinyin)
FROM $this_database.$table_room_customer rc
JOIN $this_database.$table_client c ON c.id = rc.cid AND c.status <> 2
JOIN $this_database.$table_attendee a ON a.cid = c.id AND a.status <> 2
WHERE rc.mid = r.mid AND rc.rid = r.id AND rc.status <> 2 AND rc.process_status = 1 AND a.mid = r.mid) client_code,
		rt.id type_code,
		rt.name type,
		rt.capacity,
		rt.price,
		h.name hotel_name,
		u1.name creator
	FROM $this_database.$table_room r
	JOIN $this_database.$table_room_type rt ON r.type = rt.id AND rt.status <> 2
	JOIN $this_database.$table_hotel h ON r.hid = h.id AND h.status <> 2
	LEFT JOIN $common_database.$table_user u1 ON u1.id = r.creator AND u1.status <> 2
) tab
$where
$order";

			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 获取入住房间的成员列表
		 *
		 * @param int      $meeting_id 会议ID
		 * @param int|null $room_id    分组ID
		 *
		 * @return array
		 */
		public function getCustomer($meeting_id, $room_id = null){
			$table_client        = ClientModel::TABLE_NAME;
			$table_attendee      = AttendeeModel::TABLE_NAME;
			$table_room          = self::TABLE_NAME;
			$table_room_customer = RoomCustomerModel::TABLE_NAME;
			$this_database       = self::DATABASE_NAME;
			if(!($room_id == null)) $group_filter = " AND r.id = $room_id";
			$sql    = "
SELECT c.*
FROM $this_database.$table_room r
JOIN $this_database.$table_room_customer rc ON rc.rid = r.id AND rc.status <> 2
JOIN $this_database.$table_client c ON c.id = rc.cid AND c.status <> 2
JOIN $this_database.$table_attendee a ON a.cid = c.id AND a.status <> 2
WHERE r.status <> 2 AND rc.process_status = 1 AND a.mid = r.mid AND r.mid = $meeting_id $group_filter
";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 入住房间
		 *
		 * @param int              $meeting_id 会议ID
		 * @param int              $room_id    房间ID
		 * @param int|string|array $client_id  客户ID
		 *
		 * @return array
		 */
		public function checkIn($meeting_id, $room_id, $client_id){
			if(is_null($client_id) || $client_id == '' || !$client_id) return [
				'status'  => false,
				'message' => '没有选择任何客户'
			];
			elseif(is_numeric($client_id)) $client_id = [$client_id];
			elseif(is_string($client_id)) $client_id = explode(',', $client_id);
			elseif(is_array($client_id)) ;
			else return ['status' => false, 'message' => '参数类型错误'];
			// 判断房间可容纳人数
			if(!$this->fetch(['id' => $room_id, 'mid' => $meeting_id])) return [
				'status'  => false,
				'message' => '找不到房间信息'
			];
			$room_record = $this->getObject();
			/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
			$room_type_model = D('RoyalwissD/RoomType');
			if(!$room_type_model->fetch(['id' => $room_record['type']])) return [
				'status'  => false,
				'message' => '找不到房间类型信息'
			];
			$room_type_record = $room_type_model->getObject();
			$customer_list    = $this->getCustomer($meeting_id, $room_id);
			if(((count($customer_list)+count($client_id))>$room_type_record['capacity']) && $room_type_record['capacity']>0) return [
				'status'  => false,
				'message' => '分房失败：人数超过上限',
			];
			// 将这些客户以前的入住进行退房操作
			/** @var \RoyalwissD\Model\RoomCustomerModel $room_customer_model */
			$room_customer_model = D('RoyalwissD/RoomCustomer');
			$room_customer_model->where([
				'mid'            => $meeting_id,
				'cid'            => ['in', $client_id],
				'process_status' => 1
			])->save([
				'process_status' => 0,
				'check_out_time' => Time::getCurrentTime()
			]);
			$room_customer_data = [];
			foreach($client_id as $val){
				if($val != 0 || $val != '') $room_customer_data[] = [
					'mid'           => $meeting_id,
					'cid'           => $val,
					'rid'           => $room_id,
					'creatime'      => Time::getCurrentTime(),
					'creator'       => Session::getCurrentUser(),
					'check_in_time' => Time::getCurrentTime()
				];
			}
			// 保存数据
			$result = $room_customer_model->addAll($room_customer_data);

			return $result ? [
				'status'  => true,
				'message' => '入住成功'
			] : [
				'status'  => false,
				'message' => '入住失败'
			];
		}

		/**
		 * 退房
		 *
		 * @param int              $meeting_id     会议ID
		 * @param int              $room_id        房间ID
		 * @param int|string|array $client_id      客户ID
		 * @param null|string      $check_out_time 退房时间
		 *
		 * @return array
		 */
		public function checkOut($meeting_id, $room_id, $client_id, $check_out_time = null){
			if(is_null($client_id) || $client_id == '' || !$client_id) return [
				'status'  => false,
				'message' => '没有选择任何客户'
			];
			elseif(is_numeric($client_id)) $client_id = [$client_id];
			elseif(is_string($client_id)) $client_id = explode(',', $client_id);
			elseif(is_array($client_id)) ;
			else return ['status' => false, 'message' => '参数类型错误'];
			if($check_out_time == null) $check_out_time = Time::getCurrentTime();
			/** @var \RoyalwissD\Model\RoomCustomerModel $room_customer_model */
			$room_customer_model = D('RoyalwissD/RoomCustomer');
			$result              = $room_customer_model->where([
				'mid'            => $meeting_id,
				'cid'            => ['in', $client_id],
				'rid'            => $room_id,
				'process_status' => 1
			])->save([
				'process_status' => 0,
				'check_out_time' => $check_out_time
			]);

			return $result ? [
				'status'  => true,
				'message' => '退房成功'
			] : [
				'status'  => false,
				'message' => '退房失败'
			];
		}

		/**
		 * 换房
		 *
		 * @param int $client_id_a 客户A
		 * @param int $room_id_a   客户A入住的房间
		 * @param int $client_id_b 客户B
		 * @param int $room_id_b   客户B入住的房间
		 * @param int $meeting_id  会议ID
		 *
		 * @return array
		 */
		public function change($client_id_a, $room_id_a, $client_id_b, $room_id_b, $meeting_id){
			/** @var \RoyalwissD\Model\RoomCustomerModel $room_customer_model */
			$room_customer_model = D('RoyalwissD/RoomCustomer');
			$room_customer_model->where([
				'mid' => $meeting_id,
				'rid' => $room_id_a,
				'cid' => $client_id_a
			])->save([
				'process_status' => 2,
				'change_time'    => Time::getCurrentTime()
			]);
			$room_customer_model->where([
				'mid' => $meeting_id,
				'rid' => $room_id_b,
				'cid' => $client_id_b
			])->save([
				'process_status' => 2,
				'change_time'    => Time::getCurrentTime()
			]);
			if($client_id_a != 0 && $room_id_b != 0) $result1 = $room_customer_model->create([
				'mid'           => $meeting_id,
				'cid'           => $client_id_a,
				'rid'           => $room_id_b,
				'check_in_time' => Time::getCurrentTime(),
				'creatime'      => Time::getCurrentTime(),
				'creator'       => Session::getCurrentUser()
			]);
			if($client_id_b != 0 && $room_id_a != 0) $result2 = $room_customer_model->create([
				'mid'           => $meeting_id,
				'cid'           => $client_id_b,
				'rid'           => $room_id_a,
				'check_in_time' => Time::getCurrentTime(),
				'creatime'      => Time::getCurrentTime(),
				'creator'       => Session::getCurrentUser()
			]);
			if(!isset($result1['status']) && !isset($result2['status']) && !$result1['status'] && !$result2['status']) return [
				'status'  => false,
				'message' => '换房失败B'
			];

			return ['status' => true, 'message' => '换房成功'];
		}

		/**
		 * 获取入住记录
		 *
		 * @param int      $meeting_id 会议ID
		 * @param null|int $room_id    房间ID
		 * @param null|int $client_id  客户ID
		 *
		 * @return array
		 */
		public function getCheckInHistory($meeting_id, $room_id = null, $client_id = null){
			$table_client        = ClientModel::TABLE_NAME;
			$table_attendee      = AttendeeModel::TABLE_NAME;
			$table_room          = self::TABLE_NAME;
			$table_room_customer = RoomCustomerModel::TABLE_NAME;
			$this_database       = self::DATABASE_NAME;
			$condition           = '';
			if($room_id) $condition .= " AND rid = $room_id";
			if($client_id) $condition .= " AND cid = $client_id";
			$sql = "
SELECT
	r.name room,
	r.id room_code,
	c.id cid,
	c.name client,
	rc.process_status
FROM $this_database.$table_room_customer rc
JOIN $this_database.$table_client c ON c.id = rc.cid AND c.status <> 2
JOIN $this_database.$table_room r ON r.id = rc.rid AND r.status <> 2
JOIN $this_database.$table_attendee a ON a.cid = c.id AND a.status <> 2
WHERE a.mid = r.mid AND r.mid = $meeting_id AND rc.status = 1 $condition
";

			return $this->query($sql);
		}

		/**
		 * 获取字段列表
		 *
		 * @return array
		 */
		public function getColumnList(){
			$table_room      = self::TABLE_NAME;
			$table_room_type = RoomTypeModel::TABLE_NAME;
			$this_database   = self::DATABASE_NAME;
			$room_type_name  = self::IMPORT_ROOM_TYPE_NAME;
			$sql             = "
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'$room_type_name' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.TABLES t
JOIN information_schema.COLUMNS c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_room_type'
AND c.COLUMN_NAME IN ('name')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.TABLES t
JOIN information_schema.COLUMNS c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_room'
AND c.COLUMN_NAME IN ('name', 'comment')
";
			$list            = $this->query($sql);

			return $list;
		}

		/**
		 * 获取Select插件的数据列表（可用的房间列表）
		 *
		 * @param int $meeting_id 会议ID
		 * @param int $hotel_id   酒店ID
		 *
		 * @return array
		 */
		public function getSelectedList($meeting_id, $hotel_id){
			$table_client        = ClientModel::TABLE_NAME;
			$table_attendee      = AttendeeModel::TABLE_NAME;
			$table_user          = UserModel::TABLE_NAME;
			$table_hotel         = HotelModel::TABLE_NAME;
			$table_room          = self::TABLE_NAME;
			$table_room_type     = RoomTypeModel::TABLE_NAME;
			$table_room_customer = RoomCustomerModel::TABLE_NAME;
			$common_database     = GeneralModel::DATABASE_NAME;
			$this_database       = self::DATABASE_NAME;
			$sql                 = "
SELECT
	id value,
	name html,
	concat(name,',',name_pinyin,',',type,',','type_pinyin') keyword
FROM (
	SELECT
		r.id,
		r.mid,
		r.hid,
		r.name,
		r.name_pinyin,
		r.status,
		r.comment,
		r.creator creator_code,
		r.creatime,
		(
		SELECT count(c.id)
		FROM $this_database.$table_room_customer rc
		JOIN $this_database.$table_client c ON c.id = rc.cid AND c.status <> 2
		JOIN $this_database.$table_attendee a ON a.cid = c.id AND a.status <> 2
		WHERE rc.mid = r.mid AND rc.rid = r.id AND rc.status <> 2 AND rc.process_status = 1 AND a.mid = r.mid) assigned,
		rt.id type_code,
		rt.name type,
		rt.name type_pinyin,
		rt.capacity,
		rt.price,
		h.name hotel_name,
		u1.name creator
	FROM $this_database.$table_room r
	JOIN $this_database.$table_room_type rt ON r.type = rt.id AND rt.status <> 2
	JOIN $this_database.$table_hotel h ON r.hid = h.id AND h.status <> 2
	LEFT JOIN $common_database.$table_user u1 ON u1.id = r.creator AND u1.status <> 2
) tab
WHERE mid = $meeting_id AND hid = $hotel_id AND status = 1 AND (assigned < capacity OR capacity = 0)
";
			$result              = $this->query($sql);

			return $result;
		}
	}