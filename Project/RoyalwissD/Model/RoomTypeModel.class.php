<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-28
	 * Time: 15:41
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class RoomTypeModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'room_type';
		const TABLE_NAME = 'room_type';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'hotelID'   => 'hid'
		];

		/**
		 * 创建房间类型
		 *
		 * @param array $data 房间类型信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建房间类型成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建房间类型失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getList($control = []){
			$table_room_type = $this->tableName;
			$table_user      = UserModel::TABLE_NAME;
			$table_room      = RoomModel::TABLE_NAME;
			$common_database = GeneralModel::DATABASE_NAME;
			$this_database   = self::DATABASE_NAME;
			$keyword    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status     = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$hotel_id   = $control[self::CONTROL_COLUMN_PARAMETER_SELF['hotelID']];
			$where      = ' WHERE 0 = 0 ';
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
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			if(isset($hotel_id)) $where .= " and hid = $hotel_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		rt.id,
		rt.mid,
		rt.hid,
		rt.name,
		rt.name_pinyin,
		rt.capacity,
		rt.number,
		rt.price,
		rt.status,
		rt.comment,
		rt.creator creator_code,
		rt.creatime,
		(SELECT count(r1.id) FROM $this_database.$table_room r1 WHERE r1.type = rt.id AND rt.hid = r1.hid AND rt.mid = r1.mid AND r1.status <> 2) assigned,
		u1.name creator
	FROM $this_database.$table_room_type rt
	LEFT JOIN $common_database.$table_user u1 ON u1.id = rt.creator AND u1.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 获取房间类型的已使用的房间数和可分配的房间总数
		 *
		 * @param int $meeting_id 会议ID
		 * @param int $hotel_id   酒店ID
		 *
		 * @return array
		 */
		public function getNumber($meeting_id, $hotel_id){
			$sql    = "
SELECT
	sum((SELECT count(r1.id) FROM meeting_royalwiss_deal.room r1 WHERE r1.type = rt.id AND rt.hid = r1.hid AND rt.mid = r1.mid AND r1.status <> 2)) assigned,
	sum(rt.number) number
FROM meeting_royalwiss_deal.room_type rt
WHERE rt.mid = $meeting_id AND rt.hid = $hotel_id AND rt.status = 1
";
			$result = $this->query($sql);
			if(isset($result[0]) && $result[0]['assigned']) return $result[0];
			else return ['assigned' => 0, 'number' => 0];
		}

		/**
		 * 获取Select插件的数据列表
		 *
		 * @param int                   $meeting_id   会议ID
		 * @param int                   $hotel_id     酒店ID
		 * @param null|array|string|int $include_type 特殊包含的房间类型ID
		 *
		 * @return array
		 */
		public function getSelectedList($meeting_id, $hotel_id, $include_type = null){
			if(is_null($include_type)) $condition = '';
			elseif(is_numeric($include_type)) $condition = " OR id = $include_type";
			elseif(is_string($include_type)){
				$type      = explode(',', $include_type);
				$condition = '';
				foreach($type as $id) $condition .= " OR id IN ($id)";
			}
			elseif(is_array($include_type)){
				$condition = '';
				foreach($include_type as $id) $condition .= " OR id IN ($id)";
			}
			else return [];
			$sql    = "
SELECT
	id value,
	concat(name, ' [', assigned,'/',`number` ,']') html,
	concat(name, ',', name_pinyin) keyword
FROM (
	SELECT
		(SELECT count(r1.id) FROM meeting_royalwiss_deal.room r1 WHERE r1.type = rt.id AND rt.hid = r1.hid AND rt.mid = r1.mid AND r1.status <> 2) assigned,
		rt.*
	FROM meeting_royalwiss_deal.room_type rt
) tab
WHERE mid = $meeting_id
AND hid = $hotel_id
AND status = 1
AND ((assigned < number AND number > 0 OR number = 0) $condition)";
			$result = $this->query($sql);

			return $result;
		}
	}